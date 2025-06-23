<?php
// File: app/Http/Controllers/LoginController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login.index');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Redirect berdasarkan role
            return $this->redirectUserByRole($user);
        }

        return redirect()->back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput();
    }

    private function redirectUserByRole($user)
    {
        switch ($user->role) {
            case 'admin':
                // Admin diarahkan ke panel admin Filament
                return redirect('/admin');
                
            case 'dokter':
                // Dokter diarahkan ke panel dokter Filament
                return redirect('/dokter');
                
            case 'user':
            default:
                // User/Pasien diarahkan ke dashboard user
                return redirect()->route('dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}