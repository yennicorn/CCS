<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationStatusLog;
use App\Models\Student;
use App\Support\ApplicationStatusReasoner;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DecisionController extends Controller
{
    public function decide(Request $request, Application $application)
    {
        abort_unless(auth()->user()?->role === 'super_admin', 403);
        $allowedFinalStatuses = $application->allowedFinalDecisionStatuses();
        if ($allowedFinalStatuses === []) {
            return back()->withErrors([
                'status' => ApplicationStatusReasoner::invalidTransitionMessage((string) $application->status, (string) $request->input('status', '')),
            ]);
        }

        $request->validate([
            'status' => ['required', Rule::in($allowedFinalStatuses)],
            'remarks' => 'nullable|string|max:1000',
        ]);

        $targetStatus = (string) $request->input('status');
        if (ApplicationStatusReasoner::requiresRemarks($targetStatus) && trim((string) $request->input('remarks', '')) === '') {
            return back()->withErrors(['remarks' => 'Remarks are required for rejected or waitlisted decisions.']);
        }

        if (!ApplicationStatusReasoner::canFinalizeTo((string) $application->status, $targetStatus)) {
            return back()->withErrors([
                'status' => ApplicationStatusReasoner::invalidTransitionMessage((string) $application->status, $targetStatus),
            ]);
        }

        DB::transaction(function () use ($request, $application, $targetStatus) {
            $application->update([
                'status' => $targetStatus,
                'finalized_at' => now(),
                'finalized_by' => auth()->id(),
                'remarks' => $request->remarks,
            ]);

            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'changed_by' => auth()->id(),
                'status' => $targetStatus,
                'remarks' => $request->remarks,
                'changed_at' => now(),
            ]);

            if ($targetStatus === ApplicationStatusReasoner::APPROVED) {
                Student::firstOrCreate(
                    ['application_id' => $application->id],
                    [
                        'user_id' => $application->user_id,
                        'student_no' => 'CCS-'.now()->format('Y').'-'.str_pad((string) $application->id, 5, '0', STR_PAD_LEFT),
                        'full_name' => $application->learner_full_name,
                        'grade_level' => $application->grade_level,
                    ]
                );
            }
        });

        AuditLogger::log('application_finalized', 'application', $application->id, [
            'status' => $targetStatus,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Final decision saved.');
    }
}
