<?php
// File: app/Http/Middleware/EnsureUserRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user adalah role 'user' (pasien)
        if (!Auth::check() || Auth::user()->role !== 'user') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akses ditolak. Area khusus pasien.']);
        }

        return $next($request);
    }
}