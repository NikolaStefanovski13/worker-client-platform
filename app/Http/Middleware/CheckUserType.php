<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $userType
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $userType)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($userType === 'worker' && !$user->isWorker()) {
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. This area is for workers only.');
        }

        if ($userType === 'client' && !$user->isClient()) {
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. This area is for clients only.');
        }

        return $next($request);
    }
}
