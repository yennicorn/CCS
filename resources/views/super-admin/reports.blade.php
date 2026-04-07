@extends('layouts.super-admin')

@section('page_title', 'Official Reports')
@section('page_subtitle', 'Enrollment and demographic summary')

@section('content')
<section class="panel">
    <div class="panel-head"><h2> Official Reports</h2><a class="btn" href="{{ route('super-admin.reports.export.csv') }}"> Export CSV</a></div>

    <div class="grid mb-10">
        <div class="stat"><span class="stat-label">Total Applicants</span><span class="stat-value">{{ $report['total_applicants'] }}</span></div>
        <div class="stat"><span class="stat-label">Approved Students</span><span class="stat-value">{{ $report['total_approved_students'] }}</span></div>
    </div>

    <div class="split">
        <div class="panel panel-no-margin">
            <h3> Enrollment per Grade Level</h3>
            @php
                $gradeOrder = ['Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
                $enrollmentPerGrade = collect($report['enrollment_per_grade'] ?? []);
                $orderedGrades = collect($gradeOrder)
                    ->filter(fn ($grade) => $enrollmentPerGrade->has($grade))
                    ->merge($enrollmentPerGrade->keys()->diff($gradeOrder));
            @endphp

            @forelse($orderedGrades as $grade)
                <p><strong>{{ $grade }}</strong>: {{ (int) $enrollmentPerGrade->get($grade, 0) }}</p>
            @empty
                <p>No grade-level data.</p>
            @endforelse
        </div>
        <div class="panel panel-no-margin">
            <h3> Gender Distribution</h3>
            @forelse($report['gender_distribution'] as $gender => $total)
                @php($genderLabel = \Illuminate\Support\Str::title(str_replace('_', ' ', (string) $gender)))
                <p><strong>{{ $genderLabel }}</strong>: {{ $total }}</p>
            @empty
                <p>No gender data.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
