@extends('layouts.master-admin')

@section('page_title', 'Master Administrator Dashboard')
@section('page_subtitle', 'Academic Enrollment Governance Overview')

@section('content')
@php
    $gradeTotal = max(1, (int) collect($stats['by_grade'])->sum());
    $genderTotal = max(1, (int) collect($genderStats)->sum());
    $male = (int) ($genderStats['male'] ?? 0);
    $female = (int) ($genderStats['female'] ?? 0);
    $other = (int) ($genderStats['other'] ?? 0);
    $unspecified = (int) ($genderStats['unspecified'] ?? 0);
    $malePct = round(($male / $genderTotal) * 100);
    $femalePct = round(($female / $genderTotal) * 100);
    $otherPct = round(($other / $genderTotal) * 100);
    $unspecifiedPct = max(0, 100 - $malePct - $femalePct - $otherPct);
@endphp

<section class="dashboard-topbar panel">
    <div class="dash-head-left">
        <h2>Institutional Overview</h2>
        <p class="muted">Master-level control panel for approvals, governance, and reporting.</p>
    </div>
    <div class="dash-search">
        <input type="text" placeholder="Search applications, names, or school year...">
    </div>
    <div class="dash-head-right">
        <div class="notif">NT</div>
        <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
    </div>
</section>

<section class="stats-grid">
    <article class="stat-hero stat-blue"><span class="icon">TL</span><div><h3>{{ $stats['total'] }}</h3><p>Total Enrollees</p></div></article>
    <article class="stat-hero stat-lightblue"><span class="icon">PD</span><div><h3>{{ $stats['pending'] }}</h3><p>Pending Review</p></div></article>
    <article class="stat-hero stat-purple"><span class="icon">RV</span><div><h3>{{ $stats['reviewed'] }}</h3><p>Reviewed Applications</p></div></article>
    <article class="stat-hero stat-green"><span class="icon">OK</span><div><h3>{{ $stats['approved'] }}</h3><p>Enrolled Students</p></div></article>
</section>

<section class="split">
    <article class="panel chart-panel">
        <div class="panel-head"><h3>Enrollment Distribution per Grade Level</h3></div>
        @forelse($stats['by_grade'] as $grade => $count)
            @php $pct = round(($count / $gradeTotal) * 100); @endphp
            <div class="bar-row">
                <div class="bar-label">{{ $grade }}</div>
                <div class="bar-track"><div class="bar-fill" style="width: {{ $pct }}%;"></div></div>
                <div class="bar-value">{{ $count }}</div>
            </div>
        @empty
            <p class="muted">No enrollment data yet.</p>
        @endforelse
    </article>

    <article class="panel chart-panel">
        <div class="panel-head"><h3>Gender Distribution</h3></div>
        <div class="pie-wrap">
            <div class="pie-chart" style="background: conic-gradient(#2477b8 0 {{ $malePct }}%, #5f8ee8 {{ $malePct }}% {{ $malePct + $femalePct }}%, #67a0d8 {{ $malePct + $femalePct }}% {{ $malePct + $femalePct + $otherPct }}%, #d6e6f7 {{ $malePct + $femalePct + $otherPct }}% 100%);"></div>
            <div class="pie-legend">
                <p><span class="dot d1"></span> Male: {{ $male }}</p>
                <p><span class="dot d2"></span> Female: {{ $female }}</p>
                <p><span class="dot d3"></span> Other: {{ $other }}</p>
                <p><span class="dot d4"></span> Unspecified: {{ $unspecified }}</p>
            </div>
        </div>
    </article>
</section>

<section id="school-year-control" class="panel schoolyear-panel">
    <div class="panel-head"><h3>School Year Governance</h3></div>
    <div class="sy-main">
        <div>
            <p>Active School Year</p>
            <span class="big-badge">{{ $activeSchoolYear?->name ?? 'No Active Year' }}</span>
        </div>
        <div>
            <p>Enrollment Status</p>
            <span class="big-badge {{ $activeSchoolYear && $activeSchoolYear->enrollment_open ? 'approved' : 'rejected' }}">
                {{ $activeSchoolYear && $activeSchoolYear->enrollment_open ? 'Open' : 'Closed' }}
            </span>
        </div>
        @if($activeSchoolYear)
            <form method="POST" action="{{ route('master.school-years.toggle', $activeSchoolYear) }}">
                @csrf
                <button class="btn btn-secondary" type="submit">Toggle Open / Closed</button>
            </form>
        @endif
    </div>
    <div class="sy-list">
        @foreach($schoolYears as $sy)
            <form method="POST" action="{{ route('master.school-years.set-active', $sy) }}">
                @csrf
                <button class="btn {{ $sy->is_active ? 'btn-secondary' : '' }}" type="submit">
                    Set {{ $sy->name }} as Active
                </button>
            </form>
        @endforeach
    </div>
</section>

<section id="decision-queue" class="panel">
    <div class="panel-head"><h3>Final Decision Queue</h3><p class="muted">Reviewed applications awaiting master decision.</p></div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Applicant</th>
                <th>Grade Level</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($applications as $app)
                <tr>
                    <td>
                        <div class="applicant-cell">
                            <div class="app-avatar">{{ strtoupper(substr($app->learner_full_name, 0, 1)) }}</div>
                            <div>
                                <strong>{{ $app->learner_full_name }}</strong>
                                <p class="muted">Application #{{ $app->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td>{{ $app->grade_level }}</td>
                    <td><span class="badge {{ $app->status }}">{{ strtoupper($app->status) }}</span></td>
                    <td>
                        <div class="action-row">
                            <form method="POST" action="{{ route('master.applications.decide', $app) }}">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button class="btn" type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('master.applications.decide', $app) }}">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-danger" type="submit">Reject</button>
                            </form>
                            <form method="POST" action="{{ route('master.applications.decide', $app) }}">
                                @csrf
                                <input type="hidden" name="status" value="waitlisted">
                                <button class="btn btn-secondary" type="submit">Waitlist</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No reviewed applications in queue.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $applications->links() }}</div>
</section>

<section class="split">
    <article class="panel">
        <div class="panel-head"><h3>System Activity Timeline</h3></div>
        <div class="timeline">
            @forelse($recentAuditLogs as $log)
                <div class="timeline-item">
                    <div class="timeline-dot">o</div>
                    <div>
                        <p><strong>{{ ucwords(str_replace('_', ' ', $log->action)) }}</strong></p>
                        <p class="muted">{{ $log->entity_type }} @if($log->entity_id)#{{ $log->entity_id }}@endif | {{ $log->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            @empty
                <p class="muted">No audit activity yet.</p>
            @endforelse
        </div>
    </article>

    <article id="backup-panel" class="panel backup-card">
        <div class="panel-head"><h3>Database Backup</h3></div>
        <p class="muted">Generate a local backup file for records recovery and safekeeping.</p>
        <form method="POST" action="{{ route('master.backup.database') }}">
            @csrf
            <button class="btn btn-backup" type="submit">Generate Backup</button>
        </form>
    </article>
</section>
@endsection