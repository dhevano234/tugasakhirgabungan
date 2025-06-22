<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('register.index');
    }

    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'nomor_ktp' => 'required|string|size:16|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'phone' => 'required|string|max:20|unique:users',
        'birth_date' => 'nullable|date',
        'gender' => 'nullable|in:Laki-laki,Perempuan',
        'address' => 'nullable|string',
    ]);

    User::create([
        'name' => $request->name,
        'nomor_ktp' => $request->nomor_ktp,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone' => $request->phone,
        'birth_date' => $request->birth_date,
        'gender' => $request->gender,
        'address' => $request->address,
    ]);

    return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
}
}
