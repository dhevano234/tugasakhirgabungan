<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureDokterRole
{
    public function handle(Request $request, Closure $next)
    {
        // Gunakan guard dokter
        if (!Auth::guard('dokter')->check() || Auth::guard('dokter')->user()->role !== 'dokter') {
            Auth::guard('dokter')->logout();
            return redirect()->route('filament.dokter.auth.login')
                ->withErrors(['email' => 'Akses ditolak. Hanya dokter yang diizinkan.']);
        }

        return $next($request);
    }
}