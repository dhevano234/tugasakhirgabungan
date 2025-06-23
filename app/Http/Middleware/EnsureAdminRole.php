<?php
// File: app/Http/Middleware/EnsureAdminRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user login dan role admin
        if (!Auth::guard('web')->check() || Auth::guard('web')->user()->role !== 'admin') {
            Auth::guard('web')->logout();
            
            // Redirect ke login admin dengan pesan error
            return redirect()->route('filament.admin.auth.login')
                ->withErrors(['email' => 'Akses ditolak. Hanya admin yang diizinkan mengakses panel ini.']);
        }

        return $next($request);
    }
}