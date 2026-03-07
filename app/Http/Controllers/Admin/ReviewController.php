<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationStatusLog;
use App\Support\ApplicationStatusReasoner;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function review(Request $request, Application $application)
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
        $request->validate(['remarks' => 'nullable|string|max:1000']);

        $targetStatus = ApplicationStatusReasoner::REVIEWED;

        if (!$application->canTransitionTo($targetStatus)) {
            return back()->withErrors([
                'status' => ApplicationStatusReasoner::invalidTransitionMessage((string) $application->status, $targetStatus),
            ]);
        }

        DB::transaction(function () use ($application, $request, $targetStatus) {
            $application->update([
                'status' => $targetStatus,
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'remarks' => $request->remarks,
            ]);

            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'changed_by' => auth()->id(),
                'status' => $targetStatus,
                'remarks' => $request->remarks,
                'changed_at' => now(),
            ]);
        });

        AuditLogger::log('application_reviewed', 'application', $application->id, ['remarks' => $request->remarks]);

        return back()->with('success', 'Application marked as reviewed.');
    }
}
