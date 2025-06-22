<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        // Query untuk SEMUA riwayat antrian user (termasuk yang dibatalkan)
        $query = Antrian::with(['user', 'doctor'])
                        ->where('user_id', Auth::id());

        // Filter berdasarkan poli jika ada
        if ($request->filled('poli')) {
            $query->where('poli', $request->poli);
        }

        // 🔧 URUTKAN BERDASARKAN CREATED_AT (RIWAYAT TERBARU DULU)
        $riwayatAntrian = $query->orderBy('created_at', 'desc')
                               ->paginate(10);

        return view('riwayat.index', compact('riwayatAntrian'));
    }
}