<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            Auth::check()
            && Auth::user()->role === 'user'
            && is_null(Auth::user()->profile_completed_at)
        ) {
            return redirect()->route('complete-profile.show');
        }

        return $next($request);
    }
}
