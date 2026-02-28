<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Announcement;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function download()
    {
        $payload = [
            'generated_at' => now()->toDateTimeString(),
            'users' => User::withTrashed()->get(),
            'school_years' => SchoolYear::withTrashed()->get(),
            'applications' => Application::withTrashed()->get(),
            'students' => Student::withTrashed()->get(),
            'announcements' => Announcement::withTrashed()->get(),
        ];

        $name = 'backup/database-backup-'.now()->format('Ymd-His').'.json';
        Storage::disk('local')->put($name, json_encode($payload, JSON_PRETTY_PRINT));

        AuditLogger::log('database_backup_generated', 'system', null, ['file' => $name]);

        return Storage::disk('local')->download($name);
    }
}
