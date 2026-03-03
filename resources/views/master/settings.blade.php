@extends('layouts.master-admin')

@section('page_title', 'Settings')
@section('page_subtitle', 'Super Administrator account settings')

@section('content')
<section class="panel">
    <div class="panel-head"><h3>Profiles Information</h3></div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Password Change Flag</th>
            </tr>
            </thead>
            <tbody>
            @forelse($manageableUsers as $user)
                <tr>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ strtoupper(str_replace('_', ' ', $user->role)) }}</td>
                    <td>{{ $user->is_active ? 'Active' : 'Deactivated' }}</td>
                    <td>
                        <span class="badge {{ $user->force_password_change ? 'reviewed' : 'approved' }}">
                            {{ $user->force_password_change ? 'REQUIRED' : 'NOT REQUIRED' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-head">
        <h3>Change Your Password</h3>
        <p class="muted">Update your current account password.</p>
    </div>
    <form method="POST" action="{{ route('master.settings.password.update') }}">
        @csrf
        <label>Current Password</label>
        <input type="password" name="current_password" required>

        <label>New Password</label>
        <input type="password" name="password" required>

        <label>Confirm New Password</label>
        <input type="password" name="password_confirmation" required>

        <button class="btn mt-10" type="submit">Update Password</button>
    </form>
</section>

<section class="panel">
    <div class="panel-head">
        <h3>Force Password Change (Admin Only)</h3>
        <p class="muted">Require Admin users to change password on next login.</p>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Force Password Change</th>
                <th>Super Admin Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($adminUsers as $user)
                <tr>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ strtoupper(str_replace('_', ' ', $user->role)) }}</td>
                    <td>
                        <span class="badge {{ $user->force_password_change ? 'reviewed' : 'approved' }}">
                            {{ $user->force_password_change ? 'REQUIRED' : 'NOT REQUIRED' }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('master.settings.force-password-change', $user) }}">
                            @csrf
                            <button class="btn btn-secondary" type="submit">Set Password Change</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No eligible users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-head">
        <h3>Set Admin Password</h3>
        <p class="muted">Super Admin can directly set a new password for Admin accounts.</p>
    </div>
    @forelse($adminUsers as $user)
        <form method="POST" action="{{ route('master.settings.user-password.set', $user) }}" class="panel mt-12">
            @csrf
            <div class="panel-head">
                <h4>{{ $user->full_name }}</h4>
                <p class="muted">{{ $user->email }}</p>
            </div>
            <label>New Password for Admin</label>
            <input type="password" name="password" required>
            <label>Confirm New Password</label>
            <input type="password" name="password_confirmation" required>
            <button class="btn mt-10" type="submit">Set Admin Password</button>
        </form>
    @empty
        <p class="muted">No admin users available.</p>
    @endforelse
</section>
@endsection
