<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if (
            Auth::check()
            && Auth::user()->force_password_change
            && !$request->routeIs('password.change.form', 'password.change.update', 'logout', 'logout.get')
        ) {
            return redirect()->route('password.change.form');
        }

        return $next($request);
    }
}

