@extends('layouts.main')

<main class="main-content">
    <!-- Welcome Card -->
    <div class="welcome-card animate">
        <h1>Selamat Datang! 👋</h1>
        <p>Kelola antrian dan layanan klinik dengan mudah</p>
    </div>
    
    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-card blue animate">
            <div class="stat-icon blue">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">20</div>
            <div class="stat-label">Antrian Hari Ini</div>
        </div>
        
        <div class="stat-card green animate">
            <div class="stat-icon green">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">20</div>
            <div class="stat-label">Total Pasien</div>
        </div>
        
        <div class="stat-card orange animate">
            <div class="stat-icon orange">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-number">2</div>
            <div class="stat-label">Dokter Aktif</div>
        </div>
    </div>
    
        
        <!-- Status Antrian -->
        <div class="content-card animate">
            <div class="card-header">
                <i class="fas fa-chart-bar" style="color: #27ae60;"></i>
                <h5>Status Antrian</h5>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div style="text-align: center; padding: 15px; background: #e3f2fd; border-radius: 10px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #1976d2;">12</div>
                    <small style="color: #7f8c8d;">Menunggu</small>
                </div>
                <div style="text-align: center; padding: 15px; background: #fff3e0; border-radius: 10px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #f57c00;">3</div>
                    <small style="color: #7f8c8d;">Dipanggil</small>
                </div>
                <div style="text-align: center; padding: 15px; background: #e8f5e8; border-radius: 10px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #2e7d32;">156</div>
                    <small style="color: #7f8c8d;">Selesai</small>
                </div>
                <div style="text-align: center; padding: 15px; background: #ffebee; border-radius: 10px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #d32f2f;">2</div>
                    <small style="color: #7f8c8d;">Dibatalkan</small>
                </div>
            </div>
        </div>
    </div>
</main>
@section('content')
<!-- Konten sudah ada di navbar.blade.php -->
@endsection