@extends('layouts.admin')

@section('page_title', 'Administrator Dashboard')
@section('page_subtitle', 'Application Review and Enrollment Monitoring')

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
        <h2>Operational Monitoring</h2>
        <p class="muted">Admin-level review panel for enrollment workflow.</p>
    </div>
    <div class="dash-search">
        <input type="text" placeholder="Search applicant, grade level, status...">
    </div>
    <div class="dash-head-right">
        <div class="notif">NT</div>
        <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'A', 0, 1)) }}</div>
    </div>
</section>

<section id="analytics-monitoring" class="stats-grid">
    <article class="stat-hero stat-blue"><span class="icon">TL</span><div><h3>{{ $stats['total'] }}</h3><p>Total Applications</p></div></article>
    <article class="stat-hero stat-lightblue"><span class="icon">PD</span><div><h3>{{ $stats['pending'] }}</h3><p>Pending Review</p></div></article>
    <article class="stat-hero stat-purple"><span class="icon">RV</span><div><h3>{{ $stats['reviewed'] }}</h3><p>Reviewed Applications</p></div></article>
    <article class="stat-hero stat-green"><span class="icon">OK</span><div><h3>{{ $stats['approved'] }}</h3><p>Approved Students</p></div></article>
    <article class="stat-hero stat-red"><span class="icon">NO</span><div><h3>{{ $stats['rejected'] }}</h3><p>Rejected Applications</p></div></article>
    <article class="stat-hero stat-orange"><span class="icon">WL</span><div><h3>{{ $stats['waitlisted'] }}</h3><p>Waitlisted Applications</p></div></article>
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

<section id="review-queue" class="panel">
    <div class="panel-head"><h3>Application Review Queue</h3><p class="muted">Admin can move pending applications to reviewed.</p></div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Applicant</th>
                <th>Grade Level</th>
                <th>Status</th>
                <th>Action</th>
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
                        @if($app->status === 'pending')
                            <form method="POST" action="{{ route('admin.applications.review', $app) }}">
                                @csrf
                                <input type="text" name="remarks" placeholder="Review remarks (optional)">
                                <button class="btn mt-8" type="submit">Mark as Reviewed</button>
                            </form>
                        @else
                            <span class="muted">No action</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No applications found.</td></tr>
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

    <article class="panel backup-card">
        <div class="panel-head"><h3>Announcements Shortcut</h3></div>
        <p class="muted">Post and manage enrollment updates for end users from the announcements module.</p>
        <a class="btn btn-backup" href="{{ route('admin.announcements.index') }}">Open Announcements</a>
    </article>
</section>
@endsection