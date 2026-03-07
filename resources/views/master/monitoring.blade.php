@extends('layouts.master-admin')

@section('page_title', 'Manage Enrollment')
@section('page_subtitle', 'Enrollment application records grouped by grade level')

@section('content')
<section class="panel">
    <div class="panel-head">
        <h3>Filter by Learner Name</h3>
        <p class="muted">Search by full name, first name, middle name, or last name.</p>
    </div>
    <form method="GET" action="{{ route('master.monitoring') }}" class="action-inline">
        <input type="hidden" name="grade" value="{{ $selectedGrade ?? 'Kindergarten' }}">
        <input type="text" name="name" value="{{ $nameFilter ?? '' }}" placeholder="Enter learner name..." style="max-width: 360px;">
        <button class="btn" type="submit">Search</button>
        @if(!empty($nameFilter))
            <a class="btn btn-secondary" href="{{ route('master.monitoring', ['grade' => $selectedGrade ?? 'Kindergarten']) }}">Clear</a>
        @endif
    </form>
    @if(!empty($nameFilter))
        <p class="muted mt-10">
            {{ $matchedCount ?? 0 }} result(s) found for "{{ $nameFilter }}".
        </p>
    @endif
</section>

@if(($hasFilter ?? false) && ($matchedCount ?? 0) === 0)
    <section class="panel">
        <p class="muted">No learner record found for "{{ $nameFilter }}".</p>
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
                href="{{ route('master.monitoring', array_filter(['grade' => $grade, 'name' => $nameFilter ?? null])) }}"
            >
                {{ $grade }}
            </a>
        @endforeach
    </nav>
    <form method="GET" action="{{ route('master.monitoring') }}" class="grade-mobile-select">
        @if(!empty($nameFilter))
            <input type="hidden" name="name" value="{{ $nameFilter }}">
        @endif
        <label for="master_monitoring_grade_mobile">Quick Grade Jump</label>
        <select id="master_monitoring_grade_mobile" name="grade" onchange="this.form.submit()">
            @foreach(($gradeLevels ?? []) as $grade)
                <option value="{{ $grade }}" {{ ($selectedGrade ?? '') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
            @endforeach
        </select>
    </form>
</section>

@foreach($applicationsByGrade as $grade => $items)
    <section class="panel">
        <div class="panel-head">
            <h3>{{ $grade }}</h3>
            <p class="muted">{{ $items->count() }} application(s)</p>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Application #</th>
                    <th>Learner Name</th>
                    <th>Grade Level</th>
                    <th>Information</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($items as $application)
                    <tr>
                        <td>{{ $application->id }}</td>
                        <td>{{ $application->learner_full_name }}</td>
                        <td>{{ $application->grade_level }}</td>
                        <td>
                            <strong>LRN:</strong> {{ $application->lrn ?: 'N/A' }}<br>
                            <strong>Gender:</strong> {{ ucfirst($application->gender ?? '-') }}<br>
                            <strong>Returning Learner:</strong> {{ $application->returning_learner ? 'Yes' : 'No' }}<br>
                            <strong>Guardian Contact:</strong> {{ $application->guardian_contact_number ?: 'N/A' }}
                        </td>
                        <td><span class="badge {{ $application->status }}">{{ strtoupper($application->status) }}</span></td>
                        <td>{{ optional($application->submitted_at)->format('M d, Y h:i A') ?? '-' }}</td>
                        <td class="action-row">
                            <a class="btn" href="{{ route('master.monitoring.show', $application) }}">View Enrollment Form</a>
                            @if($application->canReceiveFinalDecision())
                                <form method="POST" action="{{ route('master.applications.decide', $application) }}">
                                    @csrf
                                    <select name="status" required>
                                        @foreach($application->allowedFinalDecisionStatuses() as $nextStatus)
                                            <option value="{{ $nextStatus }}">{{ strtoupper($nextStatus) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="remarks" placeholder="Remarks (required for reject/waitlist)">
                                    <button class="btn btn-secondary" type="submit">Decide</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No applications in {{ $grade }}.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endforeach
@endsection
