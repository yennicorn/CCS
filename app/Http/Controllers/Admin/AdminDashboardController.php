<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;

class AdminDashboardController extends Controller
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

        $applications = Application::whereIn('status', ['pending', 'reviewed'])->latest()->paginate(12);
        $genderStats = Application::selectRaw("COALESCE(NULLIF(gender, ''), 'unspecified') as gender, COUNT(*) as total")
            ->groupBy('gender')
            ->pluck('total', 'gender');
        $recentAuditLogs = AuditLog::latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'applications', 'genderStats', 'recentAuditLogs'));
    }
}
