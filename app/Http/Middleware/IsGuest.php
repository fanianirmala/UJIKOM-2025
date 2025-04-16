<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {

            $role = Auth::user()->role;

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard')->with('failed', 'Anda sudah login!');
            } elseif ($role === 'petugas') {
                return redirect()->route('petugas.dashboard')->with('failed', 'Anda sudah login!');
            } else {
                // Kalau role tidak dikenali, redirect ke login
                return redirect()->route('login')->with('failed', 'Role tidak dikenali!');
            }
        }

        return $next($request);    }
}
