@extends('layouts.enduser')

@section('page_title', 'Information Feed')
@section('page_subtitle', 'School announcements and official updates.')

@section('content')
@if($application)
    <section class="panel enrollment-status-panel">
        <div class="panel-head">
            <h2><span class="icon-inline"><x-icon name="timeline" /> Enrollment Status</span></h2>
        </div>
        @php $accountRole = auth()->user()->role ?? null; @endphp
        <div class="enduser-status-strip enrollment-status-card">
            <div class="enrollment-status-main">
                <p class="muted enrollment-status-label">Your Current Enrollment Status</p>
                @if(($enrolledCount ?? 0) > 0)
                    @if($accountRole === 'parent')
                        <strong class="enrollment-status-message">
                            Congaratulations! Enrollment confirmed for S.Y. {{ $currentSchoolYearLabel ?? 'CURRENT SCHOOL YEAR' }}:
                            {{ $enrolledLearnerNamesText ?: 'your child/children' }}.
                        </strong>
                    @elseif($accountRole === 'student')
                        <strong class="enrollment-status-message">Congratulations! You are now enrolled for S.Y. {{ $currentSchoolYearLabel ?? 'CURRENT SCHOOL YEAR' }}.</strong>
                    @else
                        <strong class="enrollment-status-message">Congratulations! You are now enrolled for S.Y. {{ $currentSchoolYearLabel ?? 'CURRENT SCHOOL YEAR' }}.</strong>
                    @endif
                @else
                    <span class="badge {{ $application->status }}">{{ \App\Support\StatusLabel::for($application->status) }}</span>
                @endif
            </div>
        </div>
    </section>
@endif

<section class="panel">
    <div class="panel-head">
        <h2><span class="icon-inline"><x-icon name="announcements" /> Announcement Feed</span></h2>
    </div>

    @forelse($announcements as $a)
        <article class="feed-post">
            <h4>{{ $a->title }}</h4>
            <div class="feed-meta">{{ optional($a->publish_at)->format('M d, Y h:i A') ?? $a->created_at->format('M d, Y h:i A') }}</div>
            <p>{{ $a->content }}</p>
            @if($a->image_path)
                <img src="{{ asset('storage/'.$a->image_path) }}" alt="Announcement image">
            @endif
        </article>
    @empty
        <p>No announcements available.</p>
    @endforelse
</section>
@endsection
