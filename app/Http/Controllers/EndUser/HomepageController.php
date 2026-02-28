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
        $announcements = Announcement::where(function ($q) {
            $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
        })->latest('publish_at')->latest('created_at')->get();

        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $application = Application::where('user_id', auth()->id())->latest()->first();

        return view('enduser.homepage', compact('announcements', 'activeSchoolYear', 'application'));
    }
}
