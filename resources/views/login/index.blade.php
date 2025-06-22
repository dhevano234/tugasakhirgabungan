<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .logo-section {
            text-align: center;
            padding: 2rem 1.5rem 1rem;
            background: white;
        }

        .logo-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 1rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .logo-image:hover {
            transform: scale(1.05);
        }

        .clinic-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .clinic-tagline {
            font-size: 1rem;
            color: #e67e22;
            font-weight: 600;
            margin: 0.2rem 0 0 0;
        }

        .login-header {
            text-align: center;
            padding: 1rem 1.5rem 0;
            background: white;
        }

        .login-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .login-body {
            padding: 1.5rem 1.5rem 2rem;
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
            background-color: white;
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: #6c757d;
            opacity: 0.8;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 12px;
            padding: 0.9rem 1.5rem;
            font-size: 1.05rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .auth-links p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0.5rem 0;
        }

        .auth-links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .forgot-password-link {
            display: inline-block;
            margin-top: 0.5rem;
            color: #007bff;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        /* Loading state */
        .btn-loading {
            position: relative;
            color: transparent;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            body {
                padding: 15px;
            }
            
            .login-container {
                max-width: 100%;
            }
            
            .logo-section {
                padding: 1.5rem 1rem 0.5rem;
            }
            
            .logo-image {
                width: 100px;
                height: 100px;
            }
            
            .clinic-name {
                font-size: 1rem;
            }
            
            .clinic-tagline {
                font-size: 0.9rem;
            }
            
            .login-header h2 {
                font-size: 1.5rem;
            }
            
            .login-body {
                padding: 1.5rem 1rem;
            }
        }

        @media (max-width: 400px) {
            .logo-image {
                width: 80px;
                height: 80px;
            }
            
            .clinic-name {
                font-size: 0.9rem;
            }
            
            .clinic-tagline {
                font-size: 0.8rem;
            }
        }

        /* --- Password Toggle Icon inside input --- */
        .password-input-wrapper {
            position: relative;
            width: 100%;
        }

        .password-input-wrapper .form-control {
            padding-right: 3rem; /* Tambah padding kanan agar teks tidak tertutup ikon */
        }

        .password-input-wrapper .password-toggle {
            position: absolute;
            right: 0.75rem; /* Sesuaikan posisi ikon dari kanan input */
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            font-size: 1.1rem;
            z-index: 2; /* Pastikan ikon di atas input */
            padding: 0.25rem; /* Beri sedikit padding agar mudah diklik */
            transition: color 0.3s ease;
            user-select: none;
        }
        
        .password-input-wrapper .password-toggle:hover {
            color: #2c3e50;
        }

        /* Sembunyikan ikon mata secara default (saat input kosong) */
        .password-input-wrapper .password-toggle.hidden {
            display: none;
        }
        /* --- Akhir Password Toggle Icon inside input --- */

        /* Animation untuk form muncul */
        .login-container {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Focus ring untuk accessibility */
        .form-control:focus,
        .btn:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Logo Section -->
    <div class="logo-section">
        <img src="{{ asset('assets/img/logo/logoklinikpratama.png') }}" alt="Logo Klinik Pratama" class="logo-image">
    </div>

    <!-- Login Header -->
    <div class="login-header">
        <h2>Login</h2>
    </div>

    <!-- Login Body -->
    <div class="login-body">
        <!-- Success Alert -->
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- Error Alert -->
        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login') }}" method="POST" id="loginForm">
            @csrf

            <!-- Email Field -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    name="email" 
                    id="email" 
                    placeholder="Enter your email"
                    required 
                    autofocus
                    value="{{ old('email') }}"
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        name="password" 
                        id="password" 
                        placeholder="Enter your password"
                        required
                    >
                    <i class="fas fa-eye password-toggle hidden" id="passwordToggle"></i>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>



            <!-- Login Button -->
            <button type="submit" class="btn btn-primary" id="loginBtn">
                <i class="fas fa-sign-in-alt me-2"></i>
                Login
            </button>
        </form>

        <!-- Auth Links -->
        <div class="auth-links">
            <p>Belum punya akun? <a href="{{ route('register') }}">Register</a></p>
            <p><a href="{{ route('password.request') }}" class="forgot-password-link">Lupa Password?</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password Toggle Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');
        
        // Show/hide toggle icon based on input content
        passwordInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                passwordToggle.classList.remove('hidden');
            } else {
                passwordToggle.classList.add('hidden');
            }
        });
        
        // Toggle password visibility
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            if (type === 'password') {
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            } else {
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            }
        });
    });

    // Form submission with loading state
    document.getElementById('loginForm').addEventListener('submit', function() {
        const loginBtn = document.getElementById('loginBtn');
        loginBtn.classList.add('btn-loading');
        loginBtn.disabled = true;
        
        // Reset setelah 10 detik jika tidak redirect
        setTimeout(() => {
            loginBtn.classList.remove('btn-loading');
            loginBtn.disabled = false;
        }, 10000);
    });

    // Auto-hide alerts setelah 5 detik
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.animation = 'fadeOut 0.5s ease-out forwards';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    });

    // CSS untuk fadeOut animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
    `;
    document.head.appendChild(style);

    // Enhanced form validation
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    emailField.addEventListener('blur', function() {
        if (this.value && !validateEmail(this.value)) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });

    // Clear validation on input
    emailField.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });

    passwordField.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });

    // Keyboard navigation enhancement
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
            const form = document.getElementById('loginForm');
            const inputs = form.querySelectorAll('input[required]');
            const currentInput = e.target;
            const currentIndex = Array.from(inputs).indexOf(currentInput);
            
            if (currentIndex < inputs.length - 1) {
                e.preventDefault();
                inputs[currentIndex + 1].focus();
            }
        }
    });
</script>

</body>
</html>