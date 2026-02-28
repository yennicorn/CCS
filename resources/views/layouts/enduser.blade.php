@extends('layouts.master')
@section('page_title', 'Enrollment Homepage')
@section('page_subtitle', 'Student and Parent Unified Portal')
@section('sidebar')
<a class="sidebar-link {{ request()->routeIs('homepage') ? 'active' : '' }}" href="{{ route('homepage') }}">Homepage</a>
@if(request()->routeIs('applications.show') && isset($application))
<a class="sidebar-link active" href="{{ route('applications.show', $application) }}">Status Timeline</a>
@endif
@endsection
