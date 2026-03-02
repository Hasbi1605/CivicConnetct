<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     * Redirect to profile edit page if profile is not complete.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->is_profile_complete) {
            // Allow access to profile edit, logout, and storage routes
            $allowed = ['profile.edit', 'profile.update', 'logout'];
            if (!in_array($request->route()?->getName(), $allowed)) {
                return redirect()->route('profile.edit')
                    ->with('info', 'Silakan lengkapi profil Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
