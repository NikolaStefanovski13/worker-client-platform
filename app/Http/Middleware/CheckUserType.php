<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $userType): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($userType === 'worker' && !$user->isWorker()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Worker access only.');
        }

        if ($userType === 'client' && !$user->isClient()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Client access only.');
        }

        return $next($request);
    }
}
