<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AntrianController extends Controller
{
    /**
     * Dashboard antrian user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Cari antrian terbaru user langsung dari tabel queues dengan user_id
        $antrianTerbaru = Queue::with(['service', 'counter'])
                              ->where('user_id', $user->id) // ✅ Langsung pakai user_id
                              ->latest()
                              ->first();

        return view('antrian.index', compact('antrianTerbaru'));
    }

    /**
     * Form buat antrian baru
     */
    public function create()
    {
        $user = Auth::user();
        
        // Cek apakah user sudah punya antrian aktif hari ini
        $existingQueue = Queue::where('user_id', $user->id) // ✅ Pakai user_id
                             ->whereIn('status', ['waiting', 'serving'])
                             ->whereDate('created_at', today())
                             ->first();

        if ($existingQueue) {
            return redirect()->route('antrian.index')->withErrors([
                'error' => 'Anda masih memiliki antrian aktif. Harap selesaikan antrian tersebut terlebih dahulu.'
            ]);
        }

        $services = Service::where('is_active', true)->get();
        
        // Ambil data dokter dari tabel doctor_schedules
        $doctors = collect();
        
        try {
            $doctors = DB::table('doctor_schedules')
                        ->where('is_active', true)
                        ->get();
        } catch (\Exception $e) {
            try {
                $doctors = DB::table('doctors')->where('is_active', true)->get();
            } catch (\Exception $e2) {
                $doctors = collect();
            }
        }
        
        return view('antrian.ambil', compact('services', 'doctors'));
    }

    /**
     * Simpan antrian baru
     */
    public function store(Request $request)
    {
        // Normalisasi gender
        $genderMapping = [
            'Laki-laki' => 'male',
            'Perempuan' => 'female',
            'male' => 'male',
            'female' => 'female'
        ];
        
        if ($request->has('gender') && isset($genderMapping[$request->gender])) {
            $request->merge(['gender' => $genderMapping[$request->gender]]);
        }

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'service_id' => 'required|exists:services,id',
            'doctor_id' => 'nullable|exists:doctor_schedules,id',
        ], [
            'name.required' => 'Nama harus diisi',
            'phone.required' => 'Nomor telepon harus diisi',
            'gender.required' => 'Jenis kelamin harus dipilih',
            'gender.in' => 'Jenis kelamin tidak valid',
            'service_id.required' => 'Layanan harus dipilih',
            'doctor_id.exists' => 'Dokter yang dipilih tidak valid',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // 1. Cek apakah user sudah punya antrian aktif hari ini
            $existingQueue = Queue::where('user_id', $user->id) // ✅ Pakai user_id
                                 ->whereIn('status', ['waiting', 'serving'])
                                 ->whereDate('created_at', today())
                                 ->first();

            if ($existingQueue) {
                DB::rollBack();
                return back()->withErrors([
                    'error' => 'Anda sudah memiliki antrian aktif hari ini.'
                ])->withInput();
            }

            // 2. Generate nomor antrian
            $queueNumber = $this->generateQueueNumber($request->service_id);

            // 3. Buat antrian baru di tabel queues - LANGSUNG PAKAI USER
            $queueData = [
                'service_id' => $request->service_id,
                'user_id' => $user->id,              // ✅ Langsung user_id (bukan patient_id)
                'number' => $queueNumber,
                'status' => 'waiting',
                'counter_id' => null,
                'called_at' => null,
                'served_at' => null,
                'canceled_at' => null,
                'finished_at' => null,
            ];

            // Tambahkan doctor_id jika ada dan kolom ada
            if ($request->filled('doctor_id')) {
                if (Schema::hasColumn('queues', 'doctor_id')) {
                    $queueData['doctor_id'] = $request->doctor_id;
                }
            }

            $queue = Queue::create($queueData);

            DB::commit();

            return redirect()->route('antrian.index')->with('success', 
                'Antrian berhasil dibuat! Nomor antrian Anda: ' . $queueNumber
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat membuat antrian: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Lihat detail antrian
     */
    public function show($id)
    {
        $queue = Queue::with(['service', 'counter'])->findOrFail($id);
        
        // Pastikan user hanya bisa lihat antrian mereka sendiri
        if ($queue->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('antrian.show', compact('queue'));
    }

    /**
     * Batalkan antrian
     */
    public function destroy($id)
    {
        $queue = Queue::findOrFail($id);
        
        // Pastikan user hanya bisa batalkan antrian mereka sendiri
        if ($queue->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check apakah masih bisa dibatalkan
        if (!in_array($queue->status, ['waiting'])) {
            return redirect()->route('antrian.index')
                           ->withErrors(['error' => 'Antrian tidak dapat dibatalkan karena sudah dipanggil atau selesai.']);
        }

        try {
            // Update status menjadi dibatalkan
            $queue->update([
                'status' => 'canceled',
                'canceled_at' => now()
            ]);
            
            return redirect()->route('antrian.index')->with('success', 'Antrian berhasil dibatalkan!');
            
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat membatalkan antrian.'
            ]);
        }
    }

    /**
     * Generate Queue Number
     */
    private function generateQueueNumber($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        $lastQueue = Queue::where('service_id', $serviceId)
                         ->whereDate('created_at', today())
                         ->orderBy('id', 'desc')
                         ->first();
        
        $sequence = $lastQueue ? 
                   (int) substr($lastQueue->number, strlen($service->prefix)) + 1 : 1;
        
        return $service->prefix . sprintf('%0' . $service->padding . 'd', $sequence);
    }

    public function edit($id)
{
    $queue = Queue::with(['service', 'counter'])->findOrFail($id);
    
    // Pastikan user hanya bisa edit antrian mereka sendiri
    if ($queue->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    // Check apakah masih bisa diedit
    if (!in_array($queue->status, ['waiting'])) {
        return redirect()->route('antrian.index')
                       ->withErrors(['error' => 'Antrian tidak dapat diedit karena sudah dipanggil atau selesai.']);
    }

    $services = Service::where('is_active', true)->get();
    
    try {
        $doctors = DB::table('doctor_schedules')
                    ->where('is_active', true)
                    ->get();
    } catch (\Exception $e) {
        $doctors = collect();
    }
    
    return view('antrian.edit', compact('queue', 'services', 'doctors'));
}

/**
 * Update antrian
 */
public function update(Request $request, $id)
{
    $queue = Queue::findOrFail($id);
    
    // Pastikan user hanya bisa update antrian mereka sendiri
    if ($queue->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    // Check apakah masih bisa diupdate
    if (!in_array($queue->status, ['waiting'])) {
        return redirect()->route('antrian.index')
                       ->withErrors(['error' => 'Antrian tidak dapat diubah karena sudah dipanggil atau selesai.']);
    }

    $request->validate([
        'service_id' => 'required|exists:services,id',
        'doctor_id' => 'nullable|exists:doctor_schedules,id',
    ]);

    try {
        DB::beginTransaction();

        // Update service_id dan regenerate queue number jika service berubah
        $updateData = ['service_id' => $request->service_id];
        
        if ($queue->service_id != $request->service_id) {
            // Generate nomor antrian baru untuk service baru
            $updateData['number'] = $this->generateQueueNumber($request->service_id);
        }
        
        if ($request->filled('doctor_id') && Schema::hasColumn('queues', 'doctor_id')) {
            $updateData['doctor_id'] = $request->doctor_id;
        }

        $queue->update($updateData);

        DB::commit();

        return redirect()->route('antrian.index')->with('success', 'Antrian berhasil diubah!');
        
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Terjadi kesalahan saat mengubah antrian.'])->withInput();
    }
}

/**
 * Print ticket
 */
public function print($id)
{
    $queue = Queue::with(['service', 'counter', 'user'])->findOrFail($id);
    
    // Pastikan user hanya bisa print antrian mereka sendiri
    if ($queue->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    return view('antrian.print', compact('queue'));
}
}