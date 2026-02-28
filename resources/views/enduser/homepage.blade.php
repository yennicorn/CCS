@extends('layouts.enduser')

@section('content')
<section class="panel">
    <div class="panel-head">
        <h2>Unified Homepage (Parent and Student)</h2>
        @if($application)
            <span class="badge {{ $application->status }}">{{ strtoupper($application->status) }}</span>
        @endif
    </div>

    @if(!$activeSchoolYear || !$activeSchoolYear->enrollment_open)
        <div class="alert alert-warning">Enrollment Closed</div>
    @else
        <p>Enrollment is open for school year: <strong>{{ $activeSchoolYear->name }}</strong>.</p>
    @endif
</section>

<section class="panel">
    <div class="panel-head"><h3>Enrollment Application</h3></div>

    @if($application)
        <form method="POST" action="{{ route('applications.update', $application) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <label>Learner Full Name</label>
            <input type="text" name="learner_full_name" value="{{ $application->learner_full_name }}" {{ $application->status !== 'pending' ? 'disabled' : '' }}>

            <label>Grade Level</label>
            <input type="text" name="grade_level" value="{{ $application->grade_level }}" {{ $application->status !== 'pending' ? 'disabled' : '' }}>

            <label>Gender</label>
            <select name="gender" {{ $application->status !== 'pending' ? 'disabled' : '' }}>
                <option value="male" {{ $application->gender === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $application->gender === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ $application->gender === 'other' ? 'selected' : '' }}>Other</option>
            </select>

            @if($application->status === 'pending')
                <label>Supporting Image (optional)</label>
                <input type="file" name="supporting_image" accept=".jpg,.jpeg,.png">
            @endif

            @if($application->status === 'pending')
                <button class="btn" type="submit" style="margin-top:10px;">Update Application</button>
            @endif
        </form>

        <p style="margin-top:12px;"><a class="btn btn-secondary" href="{{ route('applications.show', $application) }}">View Status Timeline</a></p>
    @elseif($activeSchoolYear && $activeSchoolYear->enrollment_open)
        <form method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data">
            @csrf
            <label>Learner Full Name</label>
            <input type="text" name="learner_full_name" required>

            <label>Grade Level</label>
            <input type="text" name="grade_level" required>

            <label>Gender</label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>

            <label>Supporting Image (optional)</label>
            <input type="file" name="supporting_image" accept=".jpg,.jpeg,.png">

            <button class="btn" type="submit" style="margin-top:10px;">Submit Application</button>
        </form>
    @endif
</section>

<section class="panel">
    <div class="panel-head"><h3>Announcements Feed</h3></div>
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
