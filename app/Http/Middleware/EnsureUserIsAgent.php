<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAgent
{
    /**
     * Only allow CIVIC Agents to access moderation features.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isAgent()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Hanya CIVIC Agent yang dapat mengakses halaman ini.'], 403);
            }
            abort(403, 'Akses ditolak. Hanya CIVIC Agent yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
