<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->force_password_change) {
            if ($request->routeIs('password.change.form', 'password.change.update', 'logout', 'logout.get')) {
                return $next($request);
            }

            $canUseRoleLandingOnce = (bool) $request->session()->pull('allow_role_landing_once', false);
            if ($canUseRoleLandingOnce && $this->isRoleLandingRoute($request)) {
                return $next($request);
            }

            return redirect()->route('password.change.form');
        }

        return $next($request);
    }

    private function isRoleLandingRoute(Request $request): bool
    {
        $role = (string) (Auth::user()->role ?? '');

        return match ($role) {
            'super_admin' => $request->routeIs('master.dashboard'),
            'admin' => $request->routeIs('admin.dashboard'),
            'parent', 'student' => $request->routeIs('homepage', 'homepage.feed', 'homepage.enrollment'),
            default => false,
        };
    }
}
