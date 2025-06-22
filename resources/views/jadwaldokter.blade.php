@extends('layouts.main')

@section('content')
<!-- Main Content -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header animate">
        <h1><i class="fas fa-user-md"></i> Jadwal Dokter</h1>
        <p>Lihat jadwal praktek dokter yang tersedia</p>
    </div>

    <!-- Doctors Grid -->
    <div class="doctors-grid">
        @forelse ($doctors as $doctor)
            <div class="doctor-card animate">
                <div class="doctor-photo">
                    <img src="{{ $doctor->foto ? asset($doctor->foto) : asset('assets/img/doctors/doctors-1.jpg') }}"
                         alt="{{ $doctor->doctor_name }}">
                </div>
                
                <div class="doctor-info">
                    <h5>{{ $doctor->doctor_name }}</h5>
                    <span class="badge badge-{{ $doctor->spesialisasi == 'Dokter Umum' ? 'primary' : 'success' }}">
                        {{ $doctor->spesialisasi }}
                    </span>
                    
                    <div class="schedule-info">
                        <div class="schedule-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ $doctor->day_of_week}}</span>
                        </div>
                        <div class="schedule-item">
                            <i class="fas fa-clock"></i>
                            <span>
                                {{ \Carbon\Carbon::parse($doctor->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($doctor->end_time)->format('H:i') }} WIB
                            </span>
                        </div>
                    </div>
                    
                    <div class="doctor-actions">
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-user-md"></i>
                <h4>Tidak Ada Dokter</h4>
                <p>Tidak ada data dokter tersedia saat ini.</p>
            </div>
        @endforelse
    </div>
</main>

<!-- Additional Styles -->
<style>
.page-header {
    background: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.page-header h1 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.page-header p {
    color: #7f8c8d;
    margin: 0;
}

.doctors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}

.doctor-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    cursor: pointer;
}

.doctor-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.doctor-photo {
    text-align: center;
    margin-bottom: 20px;
}

.doctor-photo img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #f8f9fa;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.doctor-info h5 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
    text-align: center;
}

.doctor-info .badge {
    display: block;
    width: fit-content;
    margin: 0 auto 20px;
}

.schedule-info {
    margin-bottom: 20px;
}

.schedule-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    color: #7f8c8d;
    font-size: 14px;
}

.schedule-item i {
    width: 16px;
    color: #3498db;
}

.doctor-actions {
    text-align: center;
}

.btn-sm {
    padding: 8px 20px;
    font-size: 13px;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    color: #bdc3c7;
}

.empty-state h4 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state p {
    font-size: 1rem;
    margin: 0;
}

@media (max-width: 768px) {
    .doctors-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .doctor-card {
        padding: 20px;
    }
    
    .doctor-photo img {
        width: 80px;
        height: 80px;
    }
    
    .empty-state {
        padding: 40px 20px;
    }
    
    .empty-state i {
        font-size: 3rem;
    }
}
</style>
@endsection