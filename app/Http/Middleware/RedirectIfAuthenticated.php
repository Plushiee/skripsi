<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            switch (Auth::user()->role) {
                case 'admin-master':
                    return redirect()->route('admin-master.dashboard');
                case 'admin':
                    return redirect()->route('admin.dashboard');
                default:
                    return redirect('/');
            }
        }

        return $next($request);
    }
}
