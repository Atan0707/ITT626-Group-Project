<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Add this line for debugging
        \Log::info('AdminMiddleware check', ['user' => Auth::user()]);

        if (!Auth::check()) {
            return redirect('/login');
        }

        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect('/login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
