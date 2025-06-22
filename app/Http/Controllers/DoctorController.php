<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function jadwaldokter()
    {
        $doctors = Doctor::all(); // ambil semua data dokter
        return view('jadwaldokter', compact('doctors'));
    }
}
