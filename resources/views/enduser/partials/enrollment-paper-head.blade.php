@php
    $application = $application ?? null;
    $activeSchoolYear = $activeSchoolYear ?? null;

    $schoolYearLabel = $schoolYearLabel
        ?? ($application?->schoolYear->year
            ?? $application?->schoolYear->name
            ?? $activeSchoolYear?->year
            ?? $activeSchoolYear?->name
            ?? 'N/A');

    $gradeLabel = $gradeLabel ?? ($application?->grade_level ?? old('grade_level', ''));
    $receivedDate = $receivedDate ?? optional($application?->submitted_at)->format('m/d/Y') ?? now()->format('m/d/Y');
    $showGradeToEnroll = $showGradeToEnroll ?? true;
@endphp

<div class="enrollment-paper-head-formal">
    <div class="paper-head-left">
        <img src="{{ asset('images/branding/CCS_logo.png') }}" alt="School seal logo">
    </div>
    <div class="paper-head-center">
        <h3>Basic Education Enrollment Form</h3>
        <p>This form is not for sale</p>
    </div>
    <div class="paper-head-right">
        <small>Received as of {{ $receivedDate }}</small>
        <div class="annex-badge">ANNEX 1</div>
    </div>
</div>

<div class="enrollment-top-meta">
    <div>
        <label>School Year</label>
        <input type="text" id="paper-school-year" value="{{ $schoolYearLabel }}" readonly>
    </div>
    @if($showGradeToEnroll)
        <div>
            <label>Grade To Enroll</label>
            <input type="text" id="paper-grade-to-enroll" value="{{ $gradeLabel ?: 'N/A' }}" readonly>
        </div>
    @endif
</div>
