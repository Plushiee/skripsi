<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('umum.dashboard')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = Auth::user()->role;

        if (!in_array($userRole, $roles)) {
            // Jika pengguna sudah diarahkan ke dashboard role-nya, hindari loop
            $redirectRoute = match ($userRole) {
                'admin' => 'admin.dashboard',
                'admin-master' => 'admin-master.dashboard',
                default => 'umum.dashboard',
            };

            if ($request->route()->getName() === $redirectRoute) {
                return $next($request);
            }

            return redirect()->route($redirectRoute)->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
