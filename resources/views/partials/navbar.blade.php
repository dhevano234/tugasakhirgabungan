<style>
/* ============================================================================ */
/* SIMPLE CLEAN DASHBOARD - BACK TO BASICS */
/* ============================================================================ */

/* Import font yang bagus */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: #f5f6fa;
    margin: 0;
    padding: 0;
}

/* ============================================================================ */
/* TOP NAVBAR - SIMPLE & CLEAN */
/* ============================================================================ */
.top-nav {
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 2000; /* Higher than filter dropdown */
    height: 70px;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo-section img {
    width: 45px;
    height: 45px;
    border-radius: 8px;
    object-fit: cover;
}

.logo-text {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
}

.user-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-welcome {
    color: #7f8c8d;
    font-size: 14px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #3498db, #2980b9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 500;
    cursor: pointer;
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: 50px;
    right: 0;
    background: white;
    border: 1px solid #ecf0f1;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    min-width: 180px;
    padding: 10px 0;
    display: none;
    z-index: 2001; /* Higher than navbar */
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    padding: 10px 15px;
    color: #2c3e50;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.dropdown-item:hover {
    background: #f8f9fa;
}

.dropdown-item.logout:hover {
    background: #fee;
    color: #e74c3c;
}

/* ============================================================================ */
/* SIDEBAR - SIMPLE & CLEAN */
/* ============================================================================ */
.sidebar {
    position: fixed;
    left: 0;
    top: 70px;
    width: 250px;
    height: calc(100vh - 70px);
    background: white;
    border-right: 1px solid #ecf0f1;
    padding: 20px 0;
    overflow-y: auto;
    z-index: 1900; /* Lower than navbar but higher than content */
}

.nav-section {
    margin-bottom: 30px;
}

.nav-title {
    font-size: 12px;
    font-weight: 600;
    color: #bdc3c7;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 0 20px;
    margin-bottom: 15px;
}

.nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 5px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 20px;
    color: #7f8c8d;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: #f8f9fa;
    color: #2c3e50;
    padding-left: 25px;
}

.nav-link.active {
    background: linear-gradient(90deg, #3498db, #2980b9);
    color: white;
    border-radius: 0 25px 25px 0;
    margin-right: 10px;
}

.nav-link i {
    width: 20px;
    text-align: center;
}

/* ============================================================================ */
/* MAIN CONTENT - SIMPLE & CLEAN */
/* ============================================================================ */
.main-content {
    margin-left: 250px;
    margin-top: 70px;
    padding: 30px;
    min-height: calc(100vh - 70px);
}

/* ============================================================================ */
/* CARDS - SIMPLE & CLEAN */
/* ============================================================================ */
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    text-align: center;
}

.welcome-card h1 {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.welcome-card p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
    border-left: 5px solid;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card.blue { border-left-color: #3498db; }
.stat-card.green { border-left-color: #27ae60; }
.stat-card.orange { border-left-color: #f39c12; }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 24px;
    color: white;
}

.stat-icon.blue { background: linear-gradient(45deg, #3498db, #2980b9); }
.stat-icon.green { background: linear-gradient(45deg, #27ae60, #229954); }
.stat-icon.orange { background: linear-gradient(45deg, #f39c12, #e67e22); }

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.stat-label {
    color: #7f8c8d;
    font-size: 14px;
    font-weight: 500;
}

.content-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}

.content-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #ecf0f1;
}

.card-header h5 {
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
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
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: white;
}

.btn-secondary {
    background: #ecf0f1;
    color: #7f8c8d;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-primary { background: #e3f2fd; color: #1976d2; }
.badge-success { background: #e8f5e8; color: #2e7d32; }
.badge-warning { background: #fff3e0; color: #f57c00; }
.badge-danger { background: #ffebee; color: #d32f2f; }

/* ============================================================================ */
/* MOBILE RESPONSIVE */
/* ============================================================================ */
.mobile-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 20px;
    color: #7f8c8d;
    cursor: pointer;
}

@media (max-width: 768px) {
    .mobile-toggle {
        display: block;
    }
    
    .logo-text {
        display: none;
    }
    
    .user-welcome {
        display: none;
    }
    
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 2500; /* Higher than all content on mobile */
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    
    .sidebar.open {
        transform: translateX(0);
    }
    
    /* HAPUS SIDEBAR OVERLAY - TIDAK PERLU OVERLAY GELAP */
    
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    
    .top-nav {
        padding: 1rem;
    }
    
    .stats-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .content-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

/* ============================================================================ */
/* ANIMATIONS */
/* ============================================================================ */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate {
    animation: fadeIn 0.5s ease-out;
}

.animate:nth-child(1) { animation-delay: 0.1s; }
.animate:nth-child(2) { animation-delay: 0.2s; }
.animate:nth-child(3) { animation-delay: 0.3s; }
</style>

<!-- Top Navigation -->
<nav class="top-nav">
    <div class="logo-section">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <img src="{{ asset('assets/img/logo/logoklinikpratama.png') }}" alt="Logo">
        <span class="logo-text">Klinik Pratama Hadiana Sehat</span>
    </div>
    
    <div class="user-section">
        <span class="user-welcome">Selamat datang, {{ Auth::user()->name }}</span>
        <div class="user-avatar" onclick="toggleDropdown(event)">
            <i class="fas fa-user"></i>
            <div class="dropdown-menu" id="userDropdown">
                <a href="/editprofile" class="dropdown-item">
                    <i class="fas fa-user-edit"></i>
                    Edit Profile
                </a>
                <a href="{{ route('login') }}" class="dropdown-item logout" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="nav-section">
        <div class="nav-title">Home</div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/dashboard" class="nav-link">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
        </ul>
    </div>
    
    <div class="nav-section">
        <div class="nav-title">Menu</div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/antrian" class="nav-link">
                    <i class="fas fa-plus-circle"></i>
                    Buat Antrian
                </a>
            </li>
            <li class="nav-item">
                <a href="/riwayatkunjungan" class="nav-link">
                    <i class="fas fa-history"></i>
                    Riwayat Kunjungan
                </a>
            </li>
            <li class="nav-item">
                <a href="/jadwaldokter" class="nav-link">
                    <i class="fas fa-user-md"></i>
                    Jadwal Dokter
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- HAPUS SIDEBAR OVERLAY - TIDAK PERLU -->

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('open');
    
    // HAPUS SEMUA KODE OVERLAY - TIDAK PERLU
}

function toggleDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Set active menu
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        }
    });
    
    // HAPUS EVENT LISTENER OVERLAY - TIDAK PERLU
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('userDropdown');
    const userAvatar = document.querySelector('.user-avatar');
    
    if (!userAvatar.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

// Close sidebar on mobile when clicking outside
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.mobile-toggle');
    
    if (window.innerWidth <= 768 && 
        !sidebar.contains(e.target) && 
        !toggle.contains(e.target) &&
        sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
        // HAPUS OVERLAY CLOSE - TIDAK PERLU
    }
});
</script>