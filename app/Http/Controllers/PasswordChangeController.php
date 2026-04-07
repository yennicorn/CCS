<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function form()
    {
        return view('auth.force-password-change');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?!.*[;:"\'\/\.])(?=\S+$).{8,20}$/'],
        ]);

        $user = $request->user();

        if (Hash::check((string) $request->input('password'), (string) $user->password)) {
            return back()->withErrors([
                'password' => 'This password was already used. Please enter a new password.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->force_password_change = false;
        $user->save();

        return (match ($user->role) {
            'super_admin' => redirect()->route('super-admin.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('homepage'),
        })->with('success', 'Password updated successfully.');
    }
}
