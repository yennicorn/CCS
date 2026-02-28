@extends('layouts.master')

@section('page_title', 'Master Administration')
@section('page_subtitle', 'School Enrollment Governance and Decision Management')

@section('sidebar')
<div class="role-chip">Role: MASTER ADMIN</div>

<a class="sidebar-link {{ request()->routeIs('master.dashboard') ? 'active' : '' }}" href="{{ route('master.dashboard') }}">
    <span class="nav-ico">DB</span> Dashboard
</a>
<a class="sidebar-link {{ request()->routeIs('master.enrollment') ? 'active' : '' }}" href="{{ route('master.enrollment') }}">
    <span class="nav-ico">EN</span> Manage Enrollment
</a>
<a class="sidebar-link {{ request()->routeIs('master.school-years.*') ? 'active' : '' }}" href="{{ route('master.school-years.index') }}">
    <span class="nav-ico">SY</span> School Year Control
</a>
<a class="sidebar-link {{ request()->routeIs('master.reports.*') ? 'active' : '' }}" href="{{ route('master.reports.index') }}">
    <span class="nav-ico">RP</span> Reports
</a>
<a class="sidebar-link {{ request()->routeIs('master.announcements.*') ? 'active' : '' }}" href="{{ route('master.announcements.index') }}">
    <span class="nav-ico">AN</span> Announcements
</a>
<a class="sidebar-link {{ request()->routeIs('master.users.*') ? 'active' : '' }}" href="{{ route('master.users.index') }}">
    <span class="nav-ico">US</span> Users
</a>
<a class="sidebar-link {{ request()->routeIs('master.audit-logs.*') ? 'active' : '' }}" href="{{ route('master.audit-logs.index') }}">
    <span class="nav-ico">LG</span> System Activity Logs
</a>
<a class="sidebar-link {{ request()->routeIs('master.backup.*') ? 'active' : '' }}" href="{{ route('master.backup.index') }}">
    <span class="nav-ico">BK</span> Backup
</a>
<a class="sidebar-link {{ request()->routeIs('master.settings.*') ? 'active' : '' }}" href="{{ route('master.settings.index') }}">
    <span class="nav-ico">ST</span> Settings
</a>
@endsection