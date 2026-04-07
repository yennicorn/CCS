@extends('layouts.super-admin')

@section('page_title', 'Super Administrator Dashboard')
@section('page_subtitle', 'Enrollment statistics and chart analytics')

@section('content')
@php@endphp

<section class="dashboard-topbar panel">
    <div class="dash-head-left">
        <h2>Analytics Overview</h2>
        <p class="muted">Dashboard view for enrollment statistics and chart insights.</p>
    </div>
    <div class="dash-head-right">
        <a class="notif notif-link" href="{{ route('super-admin.monitoring') }}" title="Pending applications awaiting action">
            <x-icon name="bell" />
            @if(($notificationCount ?? 0) > 0)
                <span class="notif-badge">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>
            @endif
        </a>
    </div>
</section>

<section class="stats-grid dashboard-stats">
    <article class="stat-hero stat-blue"><span class="icon"><x-icon name="total" /></span><div><h3>{{ $stats['total'] }}</h3><p>Total Enrollees</p></div></article>
    <article class="stat-hero stat-lightblue"><span class="icon"><x-icon name="pending" /></span><div><h3>{{ $stats['pending'] }}</h3><p>Pending Applications</p></div></article>
    <article class="stat-hero stat-purple"><span class="icon"><x-icon name="reviewed" /></span><div><h3>{{ $stats['reviewed'] }}</h3><p>Internally Tagged Reviewed</p></div></article>
    <article class="stat-hero stat-green"><span class="icon"><x-icon name="approved" /></span><div><h3>{{ $stats['approved'] }}</h3><p>Enrolled Students</p></div></article>
</section>

<section class="chart-stack">
    <article class="panel chart-panel">
        <div class="panel-head">
            <div class="panel-head__title"><h3>Enrollment Distribution per Grade Level</h3></div>
        </div>
        @include('partials.grade-enrollment-bar-chart', ['chartId' => 'gradeEnrollmentChartMaster'])
    </article>
</section>

@include('partials.dashboard-announcements', ['routePrefix' => 'super-admin', 'announcements' => $announcements ?? collect()])
@endsection
