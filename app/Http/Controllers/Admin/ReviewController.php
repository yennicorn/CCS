<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationStatusLog;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function review(Request $request, Application $application)
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
        $request->validate(['remarks' => 'nullable|string|max:1000']);

        if ($application->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending applications can be reviewed.']);
        }

        $application->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'remarks' => $request->remarks,
        ]);

        ApplicationStatusLog::create([
            'application_id' => $application->id,
            'changed_by' => auth()->id(),
            'status' => 'reviewed',
            'remarks' => $request->remarks,
            'changed_at' => now(),
        ]);

        AuditLogger::log('application_reviewed', 'application', $application->id, ['remarks' => $request->remarks]);

        return back()->with('success', 'Application marked as reviewed.');
    }
}
