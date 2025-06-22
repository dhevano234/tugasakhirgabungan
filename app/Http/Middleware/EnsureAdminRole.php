<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        // Gunakan guard web
        if (!Auth::guard('web')->check() || Auth::guard('web')->user()->role !== 'admin') {
            Auth::guard('web')->logout();
            return redirect()->route('filament.admin.auth.login')
                ->withErrors(['email' => 'Akses ditolak. Hanya admin yang diizinkan.']);
        }

        return $next($request);
    }
}