<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActivation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // check if user account is deactivated
            if (!($user->is_activate ?? true)) {
                // logout the deactivated user
                Auth::logout();

                // invalidate the session
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // redirect to login
                return redirect()->route('login')->with('error', 
                    'Your account has been deactivated. Please contact the administrator for more details.'
                );
            }
        }
        
        return $next($request);
    }
}
