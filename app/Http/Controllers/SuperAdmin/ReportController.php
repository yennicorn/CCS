<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Student;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function index()
    {
        $report = [
            'total_applicants' => Application::count(),
            'total_approved_students' => Student::count(),
            'enrollment_per_grade' => Application::selectRaw('grade_level, COUNT(*) total')->groupBy('grade_level')->pluck('total', 'grade_level'),
            'gender_distribution' => Application::selectRaw("COALESCE(NULLIF(gender, ''), 'unspecified') as gender, COUNT(*) total")->groupBy('gender')->pluck('total', 'gender'),
        ];

        return view('super-admin.reports', compact('report'));
    }

    public function exportCsv()
    {
        $rows = Application::select('id', 'learner_full_name', 'grade_level', 'gender', 'status', 'submitted_at')->get();

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Application ID', 'Learner Name', 'Grade Level', 'Gender', 'Status', 'Submitted At (PHT)']);
        foreach ($rows as $row) {
            fputcsv($output, [
                $row->id,
                $row->learner_full_name,
                $row->grade_level,
                $row->gender,
                $row->status,
                optional($row->submitted_at)->timezone(config('app.timezone'))->format('Y-m-d h:i:s A') ?? '',
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, Response::HTTP_OK, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="enrollment-report.csv"',
        ]);
    }
}
