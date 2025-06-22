<!-- resources/views/auth/passwords/reset.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ config('app.name') }}</title>
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

        .reset-container {
            width: 100%;
            max-width: 480px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .reset-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .reset-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            text-align: center;
            padding: 2rem 1.5rem;
        }

        .reset-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .reset-header p {
            opacity: 0.9;
            font-size: 0.95rem;
            margin: 0;
        }

        .reset-body {
            padding: 2rem 1.5rem;
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

        .form-control[readonly] {
            background-color: #e9ecef;
            color: #6c757d;
        }

        .form-control::placeholder {
            color: #6c757d;
            opacity: 0.8;
        }

        .password-field {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            z-index: 10;
            font-size: 1rem;
        }

        .password-toggle:hover {
            color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
            transform: translateY(-50%) scale(1.1);
        }

        .password-toggle:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }

        .password-field input {
            padding-right: 50px; /* Beri ruang untuk tombol */
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

        .btn-primary:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
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

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .login-link p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }

        .match-indicator {
            font-size: 0.85rem;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        .match-success {
            color: #28a745;
        }

        .match-error {
            color: #dc3545;
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
            
            .reset-container {
                max-width: 100%;
            }
            
            .reset-header {
                padding: 1.5rem 1rem;
            }
            
            .reset-header h2 {
                font-size: 1.5rem;
            }
            
            .reset-body {
                padding: 1.5rem 1rem;
            }
        }

        /* Animation untuk form muncul */
        .reset-container {
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
    </style>
</head>
<body>

<div class="reset-container">
    <!-- Header -->
    <div class="reset-header">
        <h2><i class="fas fa-shield-alt me-2"></i>Reset Password</h2>
        <p>Buat Password Baru Anda</p>
    </div>

    <!-- Body -->
    <div class="reset-body">
        <!-- Error Alert -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('password.update') }}" id="resetForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Field -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    class="form-control" 
                    name="email" 
                    id="email" 
                    value="{{ $email ?? '' }}" 
                    readonly
                >
            </div>

            <!-- New Password Field -->
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        name="password" 
                        id="password" 
                        placeholder="Enter new password"
                        required
                        minlength="8"
                    >
                    <button type="button" class="password-toggle" id="togglePassword1">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-requirements">
                    <i class="fas fa-info-circle me-1"></i>
                    Minimal 8 karakter
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password Field -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        class="form-control" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        placeholder="Confirm new password"
                        required
                    >
                    <button type="button" class="password-toggle" id="togglePassword2">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="match-indicator" id="matchIndicator"></div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save me-2"></i>
                Reset Password
            </button>
        </form>

        <!-- Login Link -->
        <div class="login-link">
            <p><a href="{{ route('login') }}"><i class="fas fa-arrow-left me-1"></i> Back to Login</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword1 = document.getElementById('togglePassword1');
        const togglePassword2 = document.getElementById('togglePassword2');

        if (togglePassword1) {
            togglePassword1.addEventListener('click', function(e) {
                e.preventDefault();
                togglePasswordVisibility('password', this);
            });
        }

        if (togglePassword2) {
            togglePassword2.addEventListener('click', function(e) {
                e.preventDefault();
                togglePasswordVisibility('password_confirmation', this);
            });
        }
    });

    function togglePasswordVisibility(fieldId, toggleBtn) {
        const field = document.getElementById(fieldId);
        const icon = toggleBtn.querySelector('i');
        
        if (field && icon) {
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                toggleBtn.setAttribute('title', 'Hide password');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                toggleBtn.setAttribute('title', 'Show password');
            }
        }
    }

    // Password match checker
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    const matchIndicator = document.getElementById('matchIndicator');
    const submitBtn = document.getElementById('submitBtn');

    confirmField.addEventListener('input', checkPasswordMatch);
    passwordField.addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
        const password = passwordField.value;
        const confirmPassword = confirmField.value;
        
        if (confirmPassword === '') {
            matchIndicator.textContent = '';
            submitBtn.disabled = password.length < 8;
            return;
        }
        
        if (password === confirmPassword) {
            matchIndicator.textContent = '✓ Passwords match';
            matchIndicator.className = 'match-indicator match-success';
            submitBtn.disabled = password.length < 8;
        } else {
            matchIndicator.textContent = '✗ Passwords do not match';
            matchIndicator.className = 'match-indicator match-error';
            submitBtn.disabled = true;
        }
    }

    // Form submission with loading state
    document.getElementById('resetForm').addEventListener('submit', function() {
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;
        
        // Reset setelah 10 detik jika tidak redirect
        setTimeout(() => {
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
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
</script>

</body>
</html>