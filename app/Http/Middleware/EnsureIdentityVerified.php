<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdentityVerified
{
    /**
     * Block active features (posting, voting, etc.) unless identity is verified.
     * Anonymous users are bypassed (they are already read-only by design).
     * Agents are also bypassed as they have a separate assignment flow.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && !$user->isAnonim() && !$user->isAgent() && !$user->isIdentityVerified()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Verifikasi identitas akademik Anda terlebih dahulu untuk menggunakan fitur ini.',
                    'redirect' => route('identity.verify'),
                ], 403);
            }

            return redirect()->route('identity.verify')
                ->with('warning', 'Verifikasi identitas akademik Anda terlebih dahulu untuk menggunakan fitur ini.');
        }

        return $next($request);
    }
}
