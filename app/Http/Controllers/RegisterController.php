<?php
// File: app/Http/Controllers/RegisterController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('register.index');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nomor_ktp' => 'required|string|size:16|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:20|unique:users',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Buat user baru dengan role default 'user' (pasien)
        $user = User::create([
            'name' => $request->name,
            'nomor_ktp' => $request->nomor_ktp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'role' => 'user', // Default role untuk registrasi publik
        ]);

        // Login otomatis setelah register
        Auth::login($user);

        // Redirect ke dashboard user
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}