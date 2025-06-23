@extends('layouts.main')

@section('content')
<!-- Main Content -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header animate">
        <h1><i class="fas fa-plus-circle"></i>Antrian Klinik</h1>
        <p>Ambil Nomor Antrian Untuk Melakukan Kunjungan</p>
    </div>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success animate">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="alert-close">&times;</button>
        </div>
    @endif

    {{-- Alert Error --}}
    @if ($errors->any())
        <div class="alert alert-danger animate">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Error:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="alert-close">&times;</button>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-buttons animate">
        <a href="{{ route('antrian.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ambil Antrian Baru
        </a>
    </div>

    <!-- Antrian Terbaru User -->
    @if ($antrianTerbaru)
        <div class="content-card animate">
            <div class="card-header">
                <i class="fas fa-clock" style="color: #3498db;"></i>
                <h5>Antrian Terbaru Anda</h5>
            </div>
            
            <div class="antrian-item">
                <div class="antrian-info">
                    <div class="antrian-number">
                        <span class="badge badge-primary">{{ $antrianTerbaru->no_antrian }}</span>
                    </div>
                    <div class="antrian-details">
                        <h6>{{ $antrianTerbaru->user->name ?? '-' }}</h6>
                        <p><i class="fas fa-map-marker-alt"></i> {{ $antrianTerbaru->user->address ?? '-' }}</p>
                        <div class="detail-row">
                            <span><i class="fas fa-venus-mars"></i> {{ $antrianTerbaru->user->gender ?? '-' }}</span>
                            <span><i class="fas fa-phone"></i> {{ $antrianTerbaru->user->phone ?? '-' }}</span>
                        </div>
                        <div class="detail-row">
                            <span><i class="fas fa-id-card"></i> {{ $antrianTerbaru->user->nomor_ktp ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="antrian-meta">
                    <div class="poli-info">
                        <span class="badge badge-info{{ $antrianTerbaru->service->name == 'Umum' ? 'primary' : 'success' }}">
                            {{ $antrianTerbaru->poli }}
                        </span>
                        <small>{{ $antrianTerbaru->doctor->nama ?? 'Belum ditentukan' }}</small>
                    </div>
                    <div class="date-info">
                        <small><i class="fas fa-calendar"></i> {{ $antrianTerbaru->formatted_tanggal }}</small>
                    </div>
                    <div class="status-info">
                        <span class="badge badge-{{ $antrianTerbaru->status_badge }}">
                            {{ ucfirst($antrianTerbaru->status) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="antrian-actions">
                @if($antrianTerbaru->canEdit())
                    <a href="{{ route('antrian.edit', $antrianTerbaru->id) }}" 
                       class="btn btn-warning btn-sm" 
                       title="Edit Antrian">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                
                @if($antrianTerbaru->canPrint())
                <a href="{{ route('antrian.print', $antrianTerbaru->id) }}" 
                   class="btn btn-info btn-sm" 
                   target="_blank"
                   title="Print Tiket">
                    <i class="fas fa-print"></i> Print
                </a>
                @endif
                
                @if($antrianTerbaru->canCancel())
                    <form action="{{ route('antrian.destroy', $antrianTerbaru->id) }}" 
                          method="POST" 
                          style="display: inline;"
                          onsubmit="return confirm('Yakin ingin membatalkan antrian?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn btn-danger btn-sm"
                                title="Batalkan Antrian">
                            <i class="fas fa-trash"></i> Batalkan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif
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

.action-buttons {
    margin-bottom: 30px;
}

.alert {
    background: white;
    border: none;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
}

.alert-success {
    border-left: 5px solid #27ae60;
    color: #2e7d32;
}

.alert-danger {
    border-left: 5px solid #e74c3c;
    color: #d32f2f;
}

.alert-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #7f8c8d;
}

.antrian-item {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 20px;
}

.antrian-info {
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

.antrian-number {
    min-width: 60px;
}

.antrian-details h6 {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.antrian-details p {
    color: #7f8c8d;
    margin-bottom: 8px;
    font-size: 14px;
}

.detail-row {
    display: flex;
    gap: 20px;
    margin-bottom: 5px;
}

.detail-row span {
    color: #7f8c8d;
    font-size: 13px;
}

.detail-row i {
    width: 15px;
    margin-right: 5px;
}

.antrian-meta {
    text-align: right;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.poli-info small {
    display: block;
    color: #7f8c8d;
    margin-top: 5px;
}

.date-info, .status-info {
    margin-top: 5px;
}

.antrian-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    padding-top: 15px;
    border-top: 1px solid #ecf0f1;
}

.btn-sm {
    padding: 8px 15px;
    font-size: 13px;
}

@media (max-width: 768px) {
    .antrian-item {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .antrian-meta {
        text-align: left;
    }
    
    .detail-row {
        flex-direction: column;
        gap: 5px;
    }
    
    .antrian-actions {
        justify-content: center;
    }
}
</style>
@endsection