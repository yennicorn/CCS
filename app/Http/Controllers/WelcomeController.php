<?php

namespace App\Http\Controllers;

use App\Models\Announcement;

class WelcomeController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where(function ($q) {
            $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
        })->latest('publish_at')->latest('created_at')->take(5)->get();

        return view('welcome', compact('announcements'));
    }
}
