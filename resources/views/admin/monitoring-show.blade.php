@extends('layouts.admin')

@section('page_title', 'Enrollment Form')
@section('page_subtitle', 'View, print, and review enrollment application')

@section('content')
<section class="panel print-hide">
    <div class="panel-head">
        <h3>Application #{{ $application->id }}</h3>
        <p class="muted">Status: <span class="badge {{ $application->status }}">{{ strtoupper($application->status) }}</span></p>
    </div>
    <div class="action-row">
        <a class="btn btn-secondary" href="{{ route('admin.monitoring') }}">Return</a>
        <button class="btn" type="button" onclick="window.print()">Print</button>
    </div>
</section>

<section class="enrollment-paper-wrap">
    <article class="enrollment-paper">
        @include('enduser.partials.enrollment-paper-head', [
            'application' => $application,
        ])
        @include('enduser.partials.enrollment-form-fields', [
            'application' => $application,
            'readonly' => true,
        ])
    </article>
</section>

@if($application->status === 'pending')
    <section class="panel print-hide">
        <div class="panel-head">
            <h3>Review Action</h3>
            <p class="muted">Submit review after checking the enrollment form details above.</p>
        </div>
        <form method="POST" action="{{ route('admin.applications.review', $application) }}">
            @csrf
            <label>Review Remarks (optional)</label>
            <input type="text" name="remarks" placeholder="Enter remarks">
            <button class="btn mt-8" type="submit">Mark as Reviewed</button>
        </form>
    </section>
@endif
@endsection
