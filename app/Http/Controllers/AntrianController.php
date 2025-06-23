<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service; 
use App\Models\Patient;
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
        // Cari antrian terbaru user berdasarkan data patient yang terhubung dengan user
        $user = Auth::user();
        
        // Cari patient yang terkait dengan user (berdasarkan nama atau email)
        $patient = Patient::where('name', 'like', '%' . $user->name . '%')
                         ->orWhere('phone', $user->phone)
                         ->first();

        $antrianTerbaru = null;
        if ($patient) {
            $antrianTerbaru = Queue::with(['service', 'counter', 'patient'])
                                  ->where('patient_id', $patient->id)
                                  ->latest()
                                  ->first();
        }

        return view('antrian.index', compact('antrianTerbaru'));
    }

    /**
     * Form buat antrian baru
     */
    public function create()
    {
        // Cek apakah user sudah punya antrian aktif
        $user = Auth::user();
        $patient = Patient::where('name', 'like', '%' . $user->name . '%')
                         ->orWhere('phone', $user->phone)
                         ->first();

        if ($patient) {
            $existingQueue = Queue::where('patient_id', $patient->id)
                                 ->whereIn('status', ['waiting', 'serving'])
                                 ->whereDate('created_at', today())
                                 ->first();

            if ($existingQueue) {
                return redirect()->route('antrian.index')->withErrors([
                    'error' => 'Anda masih memiliki antrian aktif. Harap selesaikan antrian tersebut terlebih dahulu.'
                ]);
            }
        }

        $services = Service::where('is_active', true)->get();
        
        // Ambil data dokter dari tabel doctor_schedules
        $doctors = collect(); // Default empty collection
        
        try {
            // Mengambil dari tabel doctor_schedules berdasarkan screenshot database Anda
            $doctors = DB::table('doctor_schedules')
                        ->where('is_active', true)
                        ->get();
        } catch (\Exception $e) {
            // Jika tabel doctor_schedules tidak ada, coba doctors
            try {
                $doctors = DB::table('doctors')->where('is_active', true)->get();
            } catch (\Exception $e2) {
                // Jika kedua tabel tidak ada, gunakan collection kosong
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
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'service_id' => 'required|exists:services,id',
            'doctor_id' => 'nullable|exists:doctor_schedules,id', // Sesuaikan dengan tabel yang benar
        ], [
            'name.required' => 'Nama harus diisi',
            'phone.required' => 'Nomor telepon harus diisi',
            'gender.required' => 'Jenis kelamin harus dipilih',
            'service_id.required' => 'Layanan harus dipilih',
            'doctor_id.exists' => 'Dokter yang dipilih tidak valid',
        ]);

        try {
            DB::beginTransaction();

            // 1. Cari atau buat patient baru
            $patient = Patient::where('name', $request->name)
                             ->where('phone', $request->phone)
                             ->first();

            if (!$patient) {
                $patient = Patient::create([
                    'medical_record_number' => $this->generateMedicalRecordNumber(),
                    'name' => $request->name,
                    'birth_date' => $request->birth_date,
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'phone' => $request->phone,
                ]);
            }

            // 2. Cek apakah patient sudah punya antrian aktif
            $existingQueue = Queue::where('patient_id', $patient->id)
                                 ->whereIn('status', ['waiting', 'serving'])
                                 ->whereDate('created_at', today())
                                 ->first();

            if ($existingQueue) {
                DB::rollBack();
                return back()->withErrors([
                    'error' => 'Anda sudah memiliki antrian aktif hari ini.'
                ])->withInput();
            }

            // 3. Generate nomor antrian
            $queueNumber = $this->generateQueueNumber($request->service_id);

            // 4. Buat antrian baru
            $queueData = [
                'service_id' => $request->service_id,
                'patient_id' => $patient->id,
                'number' => $queueNumber,
                'status' => 'waiting',
            ];

            // Tambahkan doctor_id jika ada dan valid
            if ($request->filled('doctor_id')) {
                // Cek apakah kolom doctor_id ada di tabel queues
                if (Schema::hasColumn('queues', 'doctor_id')) {
                    $queueData['doctor_id'] = $request->doctor_id;
                }
            }

            $queue = Queue::create($queueData);

            // 5. Simpan relasi user dengan patient (untuk tracking)
            if (!$patient->users()->where('user_id', Auth::id())->exists()) {
                $patient->users()->attach(Auth::id());
            }

            DB::commit();

            return redirect()->route('antrian.index')->with('success', 
                'Antrian berhasil dibuat! Nomor antrian Anda: ' . $queueNumber
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat membuat antrian. Silakan coba lagi.'
            ])->withInput();
        }
    }

    /**
     * Lihat detail antrian
     */
    public function show($id)
    {
        $queue = Queue::with(['service', 'counter', 'patient'])->findOrFail($id);
        
        // Pastikan user hanya bisa lihat antrian mereka sendiri
        $user = Auth::user();
        $hasAccess = $queue->patient->users()->where('user_id', $user->id)->exists() ||
                    $queue->patient->name === $user->name ||
                    $queue->patient->phone === $user->phone;

        if (!$hasAccess) {
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
        $user = Auth::user();
        $hasAccess = $queue->patient->users()->where('user_id', $user->id)->exists() ||
                    $queue->patient->name === $user->name ||
                    $queue->patient->phone === $user->phone;

        if (!$hasAccess) {
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
     * Generate Medical Record Number
     */
    private function generateMedicalRecordNumber()
    {
        $date = now()->format('Ymd');
        $lastPatient = Patient::whereDate('created_at', today())
                             ->orderBy('id', 'desc')
                             ->first();
        
        $sequence = $lastPatient ? 
                   (int) substr($lastPatient->medical_record_number, -3) + 1 : 1;
        
        return 'MR' . $date . sprintf('%03d', $sequence);
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
}