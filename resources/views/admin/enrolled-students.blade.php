@extends('layouts.admin')

@section('page_title', 'Enrolled Students')
@section('page_subtitle', 'Final approved enrollees grouped by grade level and sex')

@section('content')
<section class="panel">
    <div class="panel-head">
        <h3>Filter Enrolled Students</h3>
        <p class="muted">Search by learner name or LRN.</p>
    </div>
    <form method="GET" action="{{ route('admin.enrolled-students') }}" class="action-inline">
        <input type="hidden" name="grade" value="{{ $selectedGrade ?? 'Kindergarten' }}">
        <input type="text" name="name" value="{{ $nameFilter ?? '' }}" placeholder="Enter learner name or LRN..." style="max-width: 360px;">
        <button class="btn" type="submit">Search</button>
        @if(!empty($nameFilter))
            <a class="btn btn-secondary" href="{{ route('admin.enrolled-students', ['grade' => $selectedGrade ?? 'Kindergarten']) }}">Clear</a>
        @endif
    </form>
    @if(!empty($nameFilter))
        <p class="muted mt-10">{{ $matchedCount ?? 0 }} result(s) found for "{{ $nameFilter }}".</p>
    @endif
</section>

@if($enrolledByGrade->isEmpty())
    <section class="panel">
        <p class="muted">
            {{ !empty($nameFilter) ? 'No enrolled students matched your filter.' : 'No approved enrollees yet.' }}
        </p>
    </section>
@endif

<section class="panel grade-quick-nav-panel">
    <div class="panel-head">
        <h3>Grade Level Navigation</h3>
        <p class="muted">Open one grade level at a time.</p>
    </div>
    <nav class="grade-quick-nav" aria-label="Grade level navigation">
        @foreach(($gradeLevels ?? []) as $grade)
            <a
                class="grade-quick-nav-link {{ ($selectedGrade ?? '') === $grade ? 'is-active' : '' }}"
                href="{{ route('admin.enrolled-students', array_filter(['grade' => $grade, 'name' => $nameFilter ?? null])) }}"
            >
                {{ $grade }}
            </a>
        @endforeach
    </nav>
    <form method="GET" action="{{ route('admin.enrolled-students') }}" class="grade-mobile-select">
        @if(!empty($nameFilter))
            <input type="hidden" name="name" value="{{ $nameFilter }}">
        @endif
        <label for="admin_enrolled_grade_mobile">Quick Grade Jump</label>
        <select id="admin_enrolled_grade_mobile" name="grade" onchange="this.form.submit()">
            @foreach(($gradeLevels ?? []) as $grade)
                <option value="{{ $grade }}" {{ ($selectedGrade ?? '') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
            @endforeach
        </select>
    </form>
</section>

@foreach($enrolledByGrade as $grade => $group)
    <section class="panel">
        <div class="panel-head">
            <h3>{{ $grade }}</h3>
            <p class="muted">
                Total: {{ $group['male']->count() + $group['female']->count() + $group['other']->count() }}
            </p>
        </div>

        <h4>Male</h4>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Student Name</th>
                    <th>LRN</th>
                    <th>Sex</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($group['male'] as $application)
                    @php
                        $fullName = trim(($application->last_name ?? '').', '.($application->first_name ?? '').' '.($application->middle_name ?? ''));
                    @endphp
                    <tr>
                        <td>{{ $fullName !== ',' ? $fullName : $application->learner_full_name }}</td>
                        <td>{{ $application->lrn ?: 'N/A' }}</td>
                        <td>Male</td>
                        <td><a class="btn" href="{{ route('admin.monitoring.show', $application) }}">View Enrollment Form</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4">No male enrollees in {{ $grade }}.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <h4 class="mt-12">Female</h4>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Student Name</th>
                    <th>LRN</th>
                    <th>Sex</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($group['female'] as $application)
                    @php
                        $fullName = trim(($application->last_name ?? '').', '.($application->first_name ?? '').' '.($application->middle_name ?? ''));
                    @endphp
                    <tr>
                        <td>{{ $fullName !== ',' ? $fullName : $application->learner_full_name }}</td>
                        <td>{{ $application->lrn ?: 'N/A' }}</td>
                        <td>Female</td>
                        <td><a class="btn" href="{{ route('admin.monitoring.show', $application) }}">View Enrollment Form</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4">No female enrollees in {{ $grade }}.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($group['other']->isNotEmpty())
            <h4 class="mt-12">Other / Unspecified</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>LRN</th>
                        <th>Sex</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($group['other'] as $application)
                        @php
                            $fullName = trim(($application->last_name ?? '').', '.($application->first_name ?? '').' '.($application->middle_name ?? ''));
                        @endphp
                        <tr>
                            <td>{{ $fullName !== ',' ? $fullName : $application->learner_full_name }}</td>
                            <td>{{ $application->lrn ?: 'N/A' }}</td>
                            <td>{{ ucfirst($application->gender ?? 'unspecified') }}</td>
                            <td><a class="btn" href="{{ route('admin.monitoring.show', $application) }}">View Enrollment Form</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endforeach
@endsection
