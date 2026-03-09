<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Application;
use App\Models\SchoolYear;

class HomepageController extends Controller
{
    public function index()
    {
        return redirect()->route('homepage.feed');
    }

    public function feed()
    {
        $announcements = Announcement::where(function ($q) {
            $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
        })->latest('publish_at')->latest('created_at')->get();

        $application = Application::with('schoolYear')
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        $activeSchoolYears = SchoolYear::where('is_active', true)->get();
        $activeSchoolYear = $activeSchoolYears->count() === 1 ? $activeSchoolYears->first() : null;
        $currentSchoolYearLabel = $activeSchoolYear?->name
            ?? $activeSchoolYear?->year
            ?? $application?->schoolYear?->name
            ?? $application?->schoolYear?->year
            ?? 'CURRENT SCHOOL YEAR';

        $enrolledApplicationsQuery = Application::query()
            ->where('user_id', auth()->id())
            ->where('status', 'approved');

        if ($activeSchoolYear) {
            $enrolledApplicationsQuery->where('school_year_id', $activeSchoolYear->id);
        }

        $enrolledLearnerNames = $enrolledApplicationsQuery
            ->pluck('learner_full_name')
            ->filter()
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values();

        $enrolledCount = $enrolledLearnerNames->count();
        $enrolledLearnerNamesText = $enrolledLearnerNames->implode(', ');

        return view('enduser.feed', compact('announcements', 'application', 'currentSchoolYearLabel', 'enrolledCount', 'enrolledLearnerNamesText'));
    }

    public function enrollment()
    {
        $activeSchoolYears = SchoolYear::where('is_active', true)->get();
        $activeSchoolYear = $activeSchoolYears->count() === 1 ? $activeSchoolYears->first() : null;
        $isEnrollmentOpen = $activeSchoolYear?->isEnrollmentOpenNow() ?? false;
        $applications = Application::where('user_id', auth()->id())
            ->latest('submitted_at')
            ->latest('created_at')
            ->get();
        $latestApplication = $applications->first();

        if ($applications->isNotEmpty()) {
            $applications->load([
                'statusLogs' => fn ($query) => $query->orderBy('changed_at'),
                'statusLogs.changedBy',
                'documents',
            ]);
        }

        return view('enduser.homepage', compact('activeSchoolYear', 'applications', 'latestApplication', 'isEnrollmentOpen'));
    }
}
