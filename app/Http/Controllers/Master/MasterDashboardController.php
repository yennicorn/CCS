<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\SchoolYear;
use App\Support\AuditLogger;

class MasterDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'pending')->count(),
            'reviewed' => Application::where('status', 'reviewed')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
            'waitlisted' => Application::where('status', 'waitlisted')->count(),
            'by_grade' => Application::selectRaw('grade_level, COUNT(*) as total')->groupBy('grade_level')->pluck('total', 'grade_level'),
        ];

        $applications = Application::where('status', 'reviewed')->latest()->paginate(10);
        $schoolYears = SchoolYear::latest()->get();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $genderStats = Application::selectRaw("COALESCE(NULLIF(gender, ''), 'unspecified') as gender, COUNT(*) as total")
            ->groupBy('gender')
            ->pluck('total', 'gender');
        $recentAuditLogs = AuditLog::latest()->take(8)->get();

        return view('master.dashboard', compact(
            'stats',
            'applications',
            'schoolYears',
            'activeSchoolYear',
            'genderStats',
            'recentAuditLogs'
        ));
    }

    public function toggleEnrollment(SchoolYear $schoolYear)
    {
        $schoolYear->enrollment_open = !$schoolYear->enrollment_open;
        $schoolYear->save();

        AuditLogger::log('school_year_enrollment_toggled', 'school_year', $schoolYear->id, [
            'enrollment_open' => $schoolYear->enrollment_open,
        ]);

        return back()->with('success', 'Enrollment status updated.');
    }

    public function setActive(SchoolYear $schoolYear)
    {
        SchoolYear::query()->update(['is_active' => false]);
        $schoolYear->is_active = true;
        $schoolYear->save();

        AuditLogger::log('school_year_activated', 'school_year', $schoolYear->id, [
            'name' => $schoolYear->name,
        ]);

        return back()->with('success', 'Active school year updated.');
    }

    public function enrollment()
    {
        $applications = Application::where('status', 'reviewed')->latest()->paginate(10);

        return view('master.enrollment', compact('applications'));
    }

    public function schoolYears()
    {
        $schoolYears = SchoolYear::latest()->get();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        return view('master.school-years', compact('schoolYears', 'activeSchoolYear'));
    }

    public function backup()
    {
        $recentAuditLogs = AuditLog::latest()->take(8)->get();

        return view('master.backup', compact('recentAuditLogs'));
    }
}
