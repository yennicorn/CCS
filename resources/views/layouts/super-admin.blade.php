@extends('layouts.master')

@section('page_title', 'Super Administration')
@section('page_subtitle', 'School Enrollment Governance and Decision Management')

@section('sidebar')
<a class="sidebar-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}" href="{{ route('super-admin.dashboard') }}">
    <span class="nav-ico"><x-icon name="dashboard" /></span> Dashboard
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.monitoring*') ? 'active' : '' }}" href="{{ route('super-admin.monitoring') }}">
    <span class="nav-ico"><x-icon name="monitor" /></span> Manage Enrollment
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.enrolled-students') ? 'active' : '' }}" href="{{ route('super-admin.enrolled-students') }}">
    <span class="nav-ico"><x-icon name="users" /></span> Enrolled Students
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.enrollment-history') ? 'active' : '' }}" href="{{ route('super-admin.enrollment-history') }}">
    <span class="nav-ico"><x-icon name="logs" /></span> Enrollment History
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.school-years.*') ? 'active' : '' }}" href="{{ route('super-admin.school-years.index') }}">
    <span class="nav-ico"><x-icon name="school-year" /></span> School Year Control
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.reports.*') ? 'active' : '' }}" href="{{ route('super-admin.reports.index') }}">
    <span class="nav-ico"><x-icon name="reports" /></span> Reports
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.announcements.*') ? 'active' : '' }}" href="{{ route('super-admin.announcements.index') }}">
    <span class="nav-ico"><x-icon name="announcements" /></span> Announcements
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}" href="{{ route('super-admin.users.index') }}">
    <span class="nav-ico"><x-icon name="users" /></span> Users
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('super-admin.audit-logs.index') }}">
    <span class="nav-ico"><x-icon name="logs" /></span> System Activity Logs
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.backup.*') ? 'active' : '' }}" href="{{ route('super-admin.backup.index') }}">
    <span class="nav-ico"><x-icon name="backup" /></span> Backup
</a>
<a class="sidebar-link {{ request()->routeIs('super-admin.settings.*') ? 'active' : '' }}" href="{{ route('super-admin.settings.index') }}">
    <span class="nav-ico"><x-icon name="settings" /></span> Account Settings
</a>
@endsection
