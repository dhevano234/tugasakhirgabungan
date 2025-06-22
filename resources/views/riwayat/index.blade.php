@extends('layouts.main')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-history"></i>Riwayat Kunjungan Pasien</h1>
        <p>Lihat riwayat kunjungan dan status antrian yang tersedia</p>
    </div>

    <div class="sort-container">
        <div class="custom-dropdown">
            <div class="dropdown-header" onclick="toggleFilterDropdown()">
                <div class="dropdown-selected">
                    <i class="fas fa-filter dropdown-icon"></i>
                    <span id="selected-text">
                        @if(request('poli') == 'Umum')
                            Poli Umum
                        @elseif(request('poli') == 'Kebidanan') 
                            Poli Kebidanan
                        @else
                            Semua Poli
                        @endif
                    </span>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </div>
            <div class="dropdown-menu" id="filter-dropdown-menu">
                <div class="dropdown-item {{ !request('poli') ? 'active' : '' }}" onclick="selectOption('all', 'Semua Poli')">
                    <i class="fas fa-list-ul item-icon"></i>
                    <span>Semua Poli</span>
                    @if(!request('poli'))
                        <i class="fas fa-check check-icon"></i>
                    @endif
                </div>
                <div class="dropdown-item {{ request('poli') == 'Umum' ? 'active' : '' }}" onclick="selectOption('Umum', 'Poli Umum')">
                    <i class="fas fa-stethoscope item-icon"></i>
                    <span>Poli Umum</span>
                    @if(request('poli') == 'Umum')
                        <i class="fas fa-check check-icon"></i>
                    @endif
                </div>
                <div class="dropdown-item {{ request('poli') == 'Kebidanan' ? 'active' : '' }}" onclick="selectOption('Kebidanan', 'Poli Kebidanan')">
                    <i class="fas fa-baby item-icon"></i>
                    <span>Poli Kebidanan</span>
                    @if(request('poli') == 'Kebidanan')
                        <i class="fas fa-check check-icon"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Membungkus card dengan div history-grid untuk konsistensi layout --}}
    <div class="history-grid"> 
        <div class="card mobile-card">
            <div class="card-header mobile-header">
                <h5 class="mobile-title">
                    <i class="fas fa-history me-2"></i>Riwayat Kunjungan
                </h5>
                <span class="mobile-count badge bg-light text-dark">
                    {{ $riwayatAntrian->total() ?? 0 }}
                </span>
            </div>
            <div class="card-body p-0">
                @if(isset($riwayatAntrian) && $riwayatAntrian->count() > 0)
                
                {{-- Desktop Table --}}
                <div class="desktop-view">
                    <div class="table-container">
                        <table class="table table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>No Antrian</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Gender</th>
                                    <th>HP</th>
                                    <th>Poli</th>
                                    <th>Tgl Antrian</th>
                                    <th>Status</th>
                                    <th>Dokter</th>
                                    <th>Tgl Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayatAntrian as $key => $antrian)
                                <tr>
                                    <td>{{ $riwayatAntrian->firstItem() + $key }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $antrian->no_antrian }}</span>
                                    </td>
                                    <td>{{ $antrian->name ?? '-' }}</td>
                                    <td>{{ $antrian->user->address ?? '-' }}</td>
                                    <td>{{ $antrian->gender ?? '-' }}</td>
                                    <td>{{ $antrian->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $antrian->poli == 'Umum' ? 'primary' : 'success' }}">
                                            {{ $antrian->poli }}
                                        </span>
                                    </td>
                                    <td>{{ $antrian->formatted_tanggal }}</td>
                                    <td>
                                        <span class="badge bg-{{ $antrian->status_badge }}">
                                            {{ ucfirst($antrian->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $antrian->doctor->nama ?? '-' }}</td>
                                    <td>
                                        {{ $antrian->created_at->format('d/m/Y') }}<br>
                                        <small>{{ $antrian->created_at->format('H:i') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mobile Cards --}}
                <div class="mobile-view">
                    <div class="mobile-cards">
                        @foreach ($riwayatAntrian as $antrian)
                        <div class="mobile-card-item">
                            <div class="mobile-card-header">
                                <span class="badge bg-info">{{ $antrian->no_antrian }}</span>
                                <span class="badge bg-{{ $antrian->status_badge }}">
                                    {{ ucfirst($antrian->status) }}
                                </span>
                            </div>
                            <div class="mobile-card-body">
                                <h6 class="patient-name">{{ $antrian->name ?? '-' }}</h6>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $antrian->user->address ?? '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-venus-mars"></i>
                                        <span>{{ $antrian->gender ?? '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span>{{ $antrian->phone ?? '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-hospital"></i>
                                        <span class="badge bg-{{ $antrian->poli == 'Umum' ? 'primary' : 'success' }}">
                                            {{ $antrian->poli }}
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ $antrian->formatted_tanggal }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-user-md"></i>
                                        <span>{{ $antrian->doctor->nama ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="pagination-container">
                    <small class="text-muted">
                        {{ $riwayatAntrian->firstItem() }}-{{ $riwayatAntrian->lastItem() }} 
                        dari {{ $riwayatAntrian->total() }}
                    </small>
                    <div class="pagination-links">
                        {{ $riwayatAntrian->appends(request()->query())->links() }}
                    </div>
                </div>

                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h5>Belum Ada Riwayat</h5>
                    <p>Riwayat kunjungan akan muncul setelah Anda mengambil antrian.</p>
                    <a href="{{ route('antrian.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Ambil Antrian
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div> {{-- Penutup div history-grid --}}
</div>

<div class="backdrop-overlay" id="backdrop-overlay"></div>

<style>
/* Base */
.main-content {
    /* Perubahan di sini: tambahkan padding-top */
    padding: 30px 30px 0 30px; /* Atas 30px, Kanan 30px, Bawah 0, Kiri 30px */
    background: #f8f9fa;
    min-height: 100vh;
    z-index: 1000;
}

/* Page Header - DIPERBAIKI */
.page-header {
    background: white;
    padding: 25px 30px; /* Menyesuaikan padding agar serasi dengan main-content */
    margin-bottom: 30px; /* Jarak antara header halaman dan filter/konten utama */
    box-shadow: 0 5px 15px rgba(0,0,0,0.08); 
    border-radius: 15px; 
}

.page-header h1 {
    font-size: 1.8rem;
    font-weight: 600;
    margin: 0 0 5px 0;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header h1 i {
    font-size: 1.6rem;
    color: #6c757d;
}

.page-header p {
    font-size: 1rem;
    margin: 0;
    color: #6c757d;
    font-weight: 400;
}

/* Filter Container - Modifikasi untuk jarak atas */
.sort-container {
    padding: 0; /* Hapus padding internal */
    background: transparent; /* Background transparan */
    border-bottom: none; /* Menghapus border bottom */
    position: relative;
    z-index: 1500;
    margin-bottom: 25px; /* Jarak antara filter dropdown dan card riwayat kunjungan */
}

.custom-dropdown {
    position: relative;
    z-index: 1501;
    margin: 0 0px; /* Margin 0 karena main-content sudah memiliki padding */
    background: white; /* Memberi background putih */
    border-radius: 15px; /* Border radius agar terlihat seperti card */
    box-shadow: 0 5px 15px rgba(0,0,0,0.08); /* Menambahkan shadow */
    padding: 0; /* Menghapus padding internal jika ada */
}

.dropdown-header {
    background: transparent; /* Menjadikan transparan karena background sudah di custom-dropdown */
    border: none; /* Menghapus border */
    border-radius: 15px; /* Menyesuaikan border-radius */
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    z-index: 1502;
}

.dropdown-header:hover {
    border-color: #667eea;
    background: white;
}

.dropdown-header.active {
    border-color: #667eea;
    background: white;
    border-radius: 15px 15px 0 0;
}

.dropdown-selected {
    display: flex;
    align-items: center;
    flex: 1;
}

.dropdown-icon {
    color: #667eea;
    margin-right: 12px;
    font-size: 1.1rem;
}

.dropdown-selected span {
    font-size: 1rem;
    font-weight: 600;
    color: #495057;
}

.dropdown-arrow {
    color: #6c757d;
    font-size: 0.9rem;
    transition: transform 0.3s;
}

.dropdown-header.active .dropdown-arrow {
    transform: rotate(180deg);
    color: #667eea;
}

/* Dropdown Menu */
.sort-container .dropdown-menu {
    position: absolute;
    top: calc(100% - 0px); /* Sesuaikan posisi agar menyatu dengan card dropdown */
    left: 0;
    right: 0;
    background: white;
    border: none; /* Menghapus border */
    border-top: none;
    border-radius: 0 0 15px 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    z-index: 1503;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.sort-container .dropdown-menu.show {
    opacity: 1;
    visibility: visible;
}

.sort-container .dropdown-item {
    padding: 15px 20px;
    display: flex;
    align-items: center;
    cursor: pointer;
    border-bottom: 1px solid #f1f3f4;
    background: white;
}

.sort-container .dropdown-item:last-child {
    border-bottom: none;
}

.sort-container .dropdown-item:hover {
    background: #f8f9fa;
}

.sort-container .dropdown-item.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.item-icon {
    margin-right: 12px;
    font-size: 1rem;
    width: 20px;
    text-align: center;
    color: #6c757d;
}

.sort-container .dropdown-item.active .item-icon {
    color: white;
}

.sort-container .dropdown-item span {
    flex: 1;
    font-size: 0.95rem;
    font-weight: 500;
}

.check-icon {
    color: #28a745;
    font-size: 0.9rem;
}

.sort-container .dropdown-item.active .check-icon {
    color: white;
}

/* Backdrop */
.backdrop-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.1);
    z-index: 1450;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.backdrop-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Content Container (Diganti dengan history-grid) */
.content-container {
    padding: 0; /* Hapus padding, akan dihandle oleh main-content */
    z-index: 1000;
}

/* History Grid - BARU */
.history-grid {
    display: grid;
    grid-template-columns: 1fr; /* Untuk satu card/element utama */
    gap: 25px; /* Jika ada lebih dari satu card */
    padding-bottom: 25px; /* Jarak bawah dari konten */
}

.mobile-card {
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    border: none;
    background: white;
}

/* Card Header - DIPERBAIKI */
.mobile-header {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #e2e8f0;
}

.mobile-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: white !important;
    margin: 0;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.mobile-count {
    font-size: 0.9rem;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
    background: white !important;
    color: #2d3748 !important;
    border: 1px solid #e2e8f0;
}

/* Desktop Table */
.desktop-view {
    display: block;
}

.mobile-view {
    display: none;
}

.table-container {
    max-height: 600px;
    overflow: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

/* Table Header - DIPERBAIKI */
.table thead th {
    background-color: #2d3748 !important;
    color: white !important;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 12px 8px;
    border-bottom: 2px solid #4a5568;
    position: sticky;
    top: 0;
    z-index: 10;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.table tbody td {
    font-size: 0.85rem;
    padding: 10px 8px;
    border-bottom: 1px solid #dee2e6;
    color: #2d3748;
}

.badge {
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
}

/* Mobile Cards */
.mobile-cards {
    padding: 10px;
    max-height: 70vh;
    overflow-y: auto;
}

.mobile-card-item {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
}

.mobile-card-header {
    background: #f8f9fa;
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mobile-card-body {
    padding: 15px;
}

.patient-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 12px;
    border-bottom: 2px solid #3498db;
    padding-bottom: 8px;
}

.info-grid {
    display: grid;
    gap: 12px;
}

.info-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item i {
    width: 16px;
    color: #6c757d;
    margin-right: 10px;
}

.info-item span {
    color: #212529;
    font-size: 0.9rem;
}

/* Pagination */
.pagination-container {
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

/* Empty State */
.empty-state {
    padding: 60px 20px;
    text-align: center;
    background: #f8f9fa;
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #e3e6f0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i {
    font-size: 2rem;
    color: #6c757d;
}

.empty-state h5 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #495057;
    margin-bottom: 12px;
}

.empty-state p {
    color: #6c757d;
    font-size: 0.95rem;
    margin-bottom: 25px;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: white;
}

/* Mobile Responsive */
@media (max-width: 991.98px) {
    .main-content {
        padding: 20px 20px 0 20px; /* Sesuaikan untuk tablet/mobile */
    
    .main-content {
        padding: 0 20px; /* Sesuaikan padding untuk tablet/mobile */
    }
    .page-header {
        padding: 20px 20px; /* Sesuaikan padding untuk tablet/mobile */
        margin-bottom: 20px;
    }
    .sort-container { 
        padding: 0; 
        margin-bottom: 20px; /* Sesuaikan margin-bottom untuk tablet/mobile */
    }
    .custom-dropdown { 
        margin: 0 0px; /* Kembali ke 0 untuk margin horizontal, padding dari main-content */
        border-radius: 12px; /* Sesuaikan border-radius */
    }
    .dropdown-header { 
        border-radius: 12px; /* Sesuaikan border-radius */
    }
    .dropdown-header.active {
        border-radius: 12px 12px 0 0;
    }
    .sort-container .dropdown-menu { 
        border-radius: 0 0 12px 12px; /* Sesuaikan border-radius */
    }
    .history-grid {
        padding-bottom: 20px; /* Sesuaikan padding-bottom */
    }
}

@media (max-width: 576px) {
    .main-content {
        padding: 15px 15px 0 15px; /* Sesuaikan untuk mobile kecil */
    }
    .page-header { 
        padding: 18px 15px; 
        border-radius: 0 0 12px 12px;
        margin-bottom: 15px;
    }
    .page-header h1 { 
        font-size: 1.5rem; 
        gap: 10px;
    }
    .page-header h1 i { 
        font-size: 1.3rem; 
    }
    .page-header p { 
        font-size: 0.9rem; 
    }
    .sort-container { 
        padding: 0; 
        margin-bottom: 15px; /* Sesuaikan margin-bottom untuk mobile kecil */
    }
    .custom-dropdown { border-radius: 10px; } /* Sesuaikan border-radius */
    .dropdown-header { padding: 12px 15px; border-radius: 10px; }
    .dropdown-header.active { border-radius: 10px 10px 0 0; }
    .sort-container .dropdown-menu { border-radius: 0 0 10px 10px; }
    .history-grid {
        padding-bottom: 15px; 
    }
}

@media (max-width: 375px) {
    .main-content {
        padding: 10px 10px 0 10px; /* Sesuaikan untuk mobile sangat kecil */
    }
    .page-header { 
        padding: 18px 10px; 
        border-radius: 0 0 12px 12px;
        margin-bottom: 15px;
    }
    .page-header h1 { 
        font-size: 1.3rem; 
        gap: 8px;
    }
    .page-header h1 i { 
        font-size: 1.2rem; 
    }
    .page-header p { 
        font-size: 0.85rem; 
    }
    .sort-container { 
        padding: 0; 
        margin-bottom: 10px; /* Sesuaikan margin-bottom untuk mobile sangat kecil */
    }
    .custom-dropdown { border-radius: 8px; } /* Sesuaikan border-radius */
    .dropdown-header { padding: 10px 12px; border-radius: 8px; }
    .dropdown-header.active { border-radius: 8px 8px 0 0; }
    .sort-container .dropdown-menu { border-radius: 0 0 8px 8px; }
    .history-grid {
        padding-bottom: 10px; 
    }
}
</style>

<script>
let isFilterDropdownOpen = false;

function toggleFilterDropdown() {
    const dropdown = document.getElementById('filter-dropdown-menu');
    const header = document.querySelector('.dropdown-header');
    const backdrop = document.getElementById('backdrop-overlay');
    
    isFilterDropdownOpen = !isFilterDropdownOpen;
    
    if (isFilterDropdownOpen) {
        dropdown.classList.add('show');
        header.classList.add('active');
        backdrop.classList.add('show');
    } else {
        dropdown.classList.remove('show');
        header.classList.remove('active');
        backdrop.classList.remove('show');
    }
}

function closeFilterDropdown() {
    const dropdown = document.getElementById('filter-dropdown-menu');
    const header = document.querySelector('.dropdown-header');
    const backdrop = document.getElementById('backdrop-overlay');
    
    if (dropdown && header) {
        dropdown.classList.remove('show');
        header.classList.remove('active');
        backdrop.classList.remove('show');
        isFilterDropdownOpen = false;
    }
}

function selectOption(value, text) {
    document.getElementById('selected-text').textContent = text;
    
    const items = document.querySelectorAll('.sort-container .dropdown-item');
    items.forEach(item => {
        item.classList.remove('active');
        const checkIcon = item.querySelector('.check-icon');
        if (checkIcon) checkIcon.remove();
    });
    
    const selectedItem = [...items].find(item => item.textContent.trim().includes(text));
    if (selectedItem) {
        selectedItem.classList.add('active');
        const checkIcon = document.createElement('i');
        checkIcon.className = 'fas fa-check check-icon';
        selectedItem.appendChild(checkIcon);
    }
    
    closeFilterDropdown();
    
    setTimeout(() => {
        sortRiwayat(value);
    }, 150);
}

function sortRiwayat(poli) {
    let url = '{{ route('riwayat.index') }}';
    if (poli !== 'all') {
        url += '?poli=' + poli;
    }
    window.location.href = url;
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isFilterDropdownOpen) {
            closeFilterDropdown();
        }
    });
});

document.addEventListener('click', function(e) {
    const filterDropdown = document.querySelector('.custom-dropdown');
    
    if (isFilterDropdownOpen && !filterDropdown.contains(e.target)) {
        closeFilterDropdown();
    }
});

document.getElementById('backdrop-overlay').addEventListener('click', function() {
    if (isFilterDropdownOpen) {
        closeFilterDropdown();
    }
});

window.addEventListener('scroll', function() {
    if (isFilterDropdownOpen) {
        closeFilterDropdown();
    }
});
</script>
@endsection