@extends('layouts.super-admin')

@section('page_title', 'Account Settings')
@section('page_subtitle', 'Super Administrator account security settings')

@section('content')
@php
    $displayName = method_exists($user, 'displayName') ? $user->displayName() : trim((string) ($user->full_name ?? $user->email ?? 'User'));
    $photoUrl = method_exists($user, 'profilePhotoUrl') ? $user->profilePhotoUrl() : null;
@endphp

<section class="panel settings-profile">
    <div class="panel-head">
        <h3>Profile</h3>
        <p class="muted">Update your profile picture.</p>
    </div>

    <div class="settings-profile-row">
        <div class="settings-profile-avatar" aria-hidden="true">
            @if($photoUrl)
                <img src="{{ $photoUrl }}" alt="Profile photo">
            @else
                <div class="settings-profile-initials">{{ method_exists($user, 'initials') ? $user->initials() : \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($displayName, 0, 2)) }}</div>
            @endif
        </div>
        <div class="settings-profile-meta">
            <p class="muted">Signed in as</p>
            <p class="settings-profile-name"><strong>{{ $displayName }}</strong></p>
            <p class="settings-profile-role">Super Administrator</p>
        </div>
    </div>

    <form method="POST" action="{{ route('super-admin.settings.profile.update') }}" class="mt-10">
        @csrf
        <label>Full Name</label>
        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required>
        <button class="btn mt-10" type="submit">Save Name</button>
    </form>

    <form method="POST" action="{{ route('super-admin.settings.profile-photo.update') }}" enctype="multipart/form-data" class="mt-10">
        @csrf
        <label>Profile Picture</label>
        <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/webp" required>
        <div class="settings-profile-actions mt-10">
            <button class="btn" type="submit">{{ $photoUrl ? 'Change Picture' : 'Upload Picture' }}</button>
        </div>
    </form>

    @if($photoUrl)
        <form method="POST" action="{{ route('super-admin.settings.profile-photo.remove') }}" class="mt-10">
            @csrf
            <button class="btn btn-danger" type="submit">Remove Picture</button>
        </form>
    @endif
</section>

<section class="panel">
    <div class="panel-head">
        <h3>Change Email and Password</h3>
        <p class="muted">You can change your email address and set a new password for your Super Admin account.</p>
        <p class="muted">Current email: <strong>{{ $user->email }}</strong></p>
    </div>

    <form method="POST" action="{{ route('super-admin.settings.password.update') }}">
        @csrf

        <label>New Email Address</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>

        <label>Current Password</label>
        <input type="password" name="current_password" autocomplete="current-password" required>

        <label>New Password</label>
        <input type="password" name="password" autocomplete="new-password" required>

        <label>Confirm New Password</label>
        <input type="password" name="password_confirmation" autocomplete="new-password" required>

        <button class="btn mt-10" type="submit">Save Changes</button>
    </form>
</section>
@endsection
