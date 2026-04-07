<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        abort_unless((string) auth()->user()?->role === 'super_admin', 403);
        $user = auth()->user();

        return view('super-admin.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        abort_unless((string) $user?->role === 'super_admin', 403);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
        ]);

        $oldName = (string) ($user->full_name ?? '');
        $newName = trim((string) $validated['full_name']);

        $user->forceFill([
            'full_name' => $newName,
        ])->save();

        AuditLogger::log('super_admin_profile_updated', 'user', $user->id, [
            'changed_by' => $user->id,
            'full_name_changed' => $oldName !== $newName,
        ]);

        return back()->with('success', 'Profile updated.');
    }

    public function updateOwnPassword(Request $request)
    {
        $user = $request->user();
        abort_unless((string) $user?->role === 'super_admin', 403);

        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['required', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?!.*[;:"\'\/\.])(?=\S+$).{8,20}$/'],
            'current_password' => ['required', 'string'],
        ]);

        if (!Hash::check((string) $request->input('current_password'), (string) $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        if (Hash::check((string) $request->input('password'), (string) $user->password)) {
            return back()->withErrors([
                'password' => 'This password was already used. Please enter a new password.',
            ]);
        }

        $oldEmail = (string) $user->email;
        $newEmail = mb_strtolower(trim((string) $request->input('email')));

        $user->forceFill([
            'email' => $newEmail,
            'password' => Hash::make((string) $request->input('password')),
            'force_password_change' => false,
        ])->save();

        AuditLogger::log('super_admin_account_security_updated', 'user', $user->id, [
            'changed_by' => $user->id,
            'role' => $user->role,
            'email_changed' => $oldEmail !== $newEmail,
        ]);

        return back()->with('success', 'Account settings updated successfully.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $user = $request->user();
        abort_unless((string) $user?->role === 'super_admin', 403);

        if (!Schema::hasColumn('users', 'profile_photo_path')) {
            return back()->withErrors([
                'profile_photo' => 'Profile photo column is missing. Please run migrations (php artisan migrate) then try again.',
            ]);
        }

        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $oldPath = (string) ($user->profile_photo_path ?? '');

        $file = $request->file('profile_photo');
        $ext = mb_strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg'));
        $dir = public_path('uploads/profile-photos');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'user-'.$user->id.'-'.now()->format('YmdHis').'-'.Str::random(8).'.'.$ext;
        $file->move($dir, $filename);
        $path = 'uploads/profile-photos/'.$filename;

        $user->forceFill([
            'profile_photo_path' => $path,
        ])->save();

        if ($oldPath !== '') {
            if (Str::startsWith($oldPath, 'uploads/')) {
                $oldFile = public_path($oldPath);
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            } else {
                Storage::disk('public')->delete($oldPath);
            }
        }

        AuditLogger::log('super_admin_profile_photo_updated', 'user', $user->id, [
            'changed_by' => $user->id,
        ]);

        return back()->with('success', 'Profile picture updated.');
    }

    public function removeProfilePhoto(Request $request)
    {
        $user = $request->user();
        abort_unless((string) $user?->role === 'super_admin', 403);

        if (!Schema::hasColumn('users', 'profile_photo_path')) {
            return back()->withErrors([
                'profile_photo' => 'Profile photo column is missing. Please run migrations (php artisan migrate) then try again.',
            ]);
        }

        $oldPath = trim((string) ($user->profile_photo_path ?? ''));

        $user->forceFill([
            'profile_photo_path' => null,
        ])->save();

        if ($oldPath !== '') {
            if (Str::startsWith($oldPath, 'uploads/')) {
                $oldFile = public_path($oldPath);
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            } else {
                Storage::disk('public')->delete($oldPath);
            }
        }

        AuditLogger::log('super_admin_profile_photo_removed', 'user', $user->id, [
            'changed_by' => $user->id,
        ]);

        return back()->with('success', 'Profile picture removed.');
    }
}
