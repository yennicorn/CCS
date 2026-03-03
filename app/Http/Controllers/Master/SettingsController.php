<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        abort_unless((string) auth()->user()?->role === 'super_admin', 403);

        $manageableUsers = User::query()
            ->whereIn('role', ['super_admin', 'admin'])
            ->orderBy('full_name')
            ->get();
        $adminUsers = $manageableUsers->where('role', 'admin')->values();

        return view('master.settings', compact('manageableUsers', 'adminUsers'));
    }

    public function updateOwnPassword(Request $request)
    {
        $user = $request->user();
        $isAdmin = (string) $user->role === 'admin';

        $request->validate([
            'password' => ['required', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?!.*[;:"\'\/\.])(?=\S+$).{8,20}$/'],
            'current_password' => [$isAdmin ? 'nullable' : 'required', 'string'],
            'super_admin_password' => [$isAdmin ? 'required' : 'nullable', 'string'],
        ]);

        if ($isAdmin) {
            $superAdmins = User::query()
                ->where('role', 'super_admin')
                ->where('is_active', true)
                ->get(['password']);

            $isAuthorizedBySuperAdmin = $superAdmins->contains(function (User $superAdmin) use ($request) {
                return Hash::check((string) $request->input('super_admin_password'), (string) $superAdmin->password);
            });

            if (!$isAuthorizedBySuperAdmin) {
                return back()->withErrors(['super_admin_password' => 'Super Admin password is incorrect.']);
            }
        } elseif (!Hash::check((string) $request->input('current_password'), (string) $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        if (Hash::check((string) $request->input('password'), (string) $user->password)) {
            return back()->withErrors([
                'password' => 'This password was already used. Please enter a new password.',
            ]);
        }

        $user->password = Hash::make((string) $request->input('password'));
        $user->force_password_change = false;
        $user->save();

        AuditLogger::log('user_password_changed', 'user', $user->id, [
            'changed_by' => $user->id,
            'role' => $user->role,
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function forcePasswordChange(User $user)
    {
        abort_unless(auth()->user()?->role === 'super_admin', 403);

        if ((string) $user->role !== 'admin') {
            return back()->withErrors(['user' => 'Password change can only be enforced for Admin accounts.']);
        }

        $user->force_password_change = true;
        $user->save();

        AuditLogger::log('force_password_change_enabled', 'user', $user->id, [
            'set_by' => auth()->id(),
            'target_role' => $user->role,
        ]);

        return back()->with('success', 'Forced password change has been set for '.$user->full_name.'.');
    }

    public function setManagedUserPassword(Request $request, User $user)
    {
        abort_unless(auth()->user()?->role === 'super_admin', 403);

        if ((string) $user->role !== 'admin') {
            return back()->withErrors(['user' => 'Only Admin account passwords can be set by Super Admin.']);
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?!.*[;:"\'\/\.])(?=\S+$).{8,20}$/'],
        ]);

        if (Hash::check((string) $validated['password'], (string) $user->password)) {
            return back()->withErrors(['password' => 'New password must be different from the previous password.']);
        }

        $user->password = Hash::make((string) $validated['password']);
        $user->force_password_change = false;
        $user->save();

        AuditLogger::log('admin_password_set_by_super_admin', 'user', $user->id, [
            'set_by' => auth()->id(),
            'target_role' => $user->role,
        ]);

        return back()->with('success', 'Admin password updated for '.$user->full_name.'.');
    }
}
