<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationStatusLog;
use App\Models\Document;
use App\Models\SchoolYear;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        abort_unless(in_array($request->user()->role, ['parent', 'student'], true), 403);
        $schoolYear = SchoolYear::where('is_active', true)->first();
        if (!$schoolYear || !$schoolYear->enrollment_open) {
            return back()->withErrors(['application' => 'Enrollment is closed.']);
        }

        $request->validate([
            'learner_full_name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
            'gender' => 'required|in:male,female,other',
            'supporting_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $application = Application::create([
            'user_id' => $request->user()->id,
            'school_year_id' => $schoolYear->id,
            'learner_full_name' => $request->learner_full_name,
            'grade_level' => $request->grade_level,
            'gender' => $request->gender,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        if ($request->hasFile('supporting_image')) {
            $file = $request->file('supporting_image');
            $path = $file->store('applications', 'public');

            Document::create([
                'application_id' => $application->id,
                'type' => 'supporting_image',
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        ApplicationStatusLog::create([
            'application_id' => $application->id,
            'changed_by' => $request->user()->id,
            'status' => 'pending',
            'remarks' => 'Application submitted.',
            'changed_at' => now(),
        ]);

        AuditLogger::log('application_submitted', 'application', $application->id);

        return back()->with('success', 'Application submitted successfully.');
    }

    public function show(Application $application)
    {
        abort_unless(in_array(auth()->user()->role, ['parent', 'student'], true), 403);
        abort_if($application->user_id !== auth()->id(), 403);
        $application->load('statusLogs.changedBy', 'documents');
        return view('enduser.application-show', compact('application'));
    }

    public function update(Request $request, Application $application)
    {
        abort_unless(in_array($request->user()->role, ['parent', 'student'], true), 403);
        abort_if($application->user_id !== auth()->id(), 403);

        if ($application->status !== 'pending') {
            return back()->withErrors(['application' => 'Editing is allowed only while status is pending.']);
        }

        $request->validate([
            'learner_full_name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
            'gender' => 'required|in:male,female,other',
            'supporting_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $application->update($request->only('learner_full_name', 'grade_level', 'gender'));

        if ($request->hasFile('supporting_image')) {
            $file = $request->file('supporting_image');
            $path = $file->store('applications', 'public');

            Document::create([
                'application_id' => $application->id,
                'type' => 'supporting_image',
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'size_bytes' => $file->getSize(),
            ]);
        }
        AuditLogger::log('application_updated', 'application', $application->id);

        return back()->with('success', 'Application updated.');
    }
}
