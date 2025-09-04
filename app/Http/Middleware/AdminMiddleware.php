<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check login status
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user has role_id = 1 (admin)
        $user = Auth::user();
        if (!$user || $user->role_id !== 1) {
            // If not admin, return 403 
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
