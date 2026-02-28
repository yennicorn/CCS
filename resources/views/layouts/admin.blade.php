@extends('layouts.master')

@section('page_title', 'Admin Dashboard')
@section('page_subtitle', 'Operational Review and Monitoring')

@section('sidebar')
<div class="role-chip">Role: ADMIN</div>

<a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
    <span class="nav-ico">DB</span> Dashboard
</a>
<a class="sidebar-link" href="{{ route('admin.dashboard') }}#review-queue">
    <span class="nav-ico">AP</span> Applications
</a>
<a class="sidebar-link" href="{{ route('admin.dashboard') }}#analytics-monitoring">
    <span class="nav-ico">MN</span> Monitoring
</a>
<a class="sidebar-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
    <span class="nav-ico">AN</span> Announcements
</a>
@endsection