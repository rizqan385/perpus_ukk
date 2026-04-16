<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiswaAuthMiddleware
{
    /**
     * Handle an incoming request.
     * Ensures user is authenticated AND has role 'siswa'.
     * If unauthenticated → redirect to siswa login page (not Breeze).
     * If authenticated but wrong role → redirect to appropriate dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            // Store intended URL so we can redirect back after login
            session()->put('url.intended', $request->url());
            return redirect()->route('siswa.login.page');
        }

        if ($request->user()->role !== 'siswa') {
            if ($request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('siswa.login.page');
        }

        return $next($request);
    }
}
