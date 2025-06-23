<?php
// File: app/Http/Middleware/EnsureDokterRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureDokterRole
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user login dan role dokter
        if (!Auth::guard('dokter')->check() || Auth::guard('dokter')->user()->role !== 'dokter') {
            Auth::guard('dokter')->logout();
            
            // Redirect ke login dokter dengan pesan error
            return redirect()->route('filament.dokter.auth.login')
                ->withErrors(['email' => 'Akses ditolak. Hanya dokter yang diizinkan mengakses panel ini.']);
        }

        return $next($request);
    }
}