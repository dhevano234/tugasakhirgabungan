<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ config('app.name') }}</title>
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

        .register-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .register-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .logo-section {
            text-align: center;
            padding: 1.5rem 1rem 1rem;
            background: white;
        }

        .logo-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 0.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .logo-image:hover {
            transform: scale(1.05);
        }

        .register-header {
            text-align: center;
            padding: 0.5rem 1.5rem 0;
            background: white;
        }

        .register-header h2 {
            font-size: 1.6rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .register-body {
            padding: 1rem 1.5rem 1.5rem;
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.7rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            margin-bottom: 0.8rem; /* Default margin-bottom for form-control */
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

        .row-compact {
            display: flex;
            gap: 0.8rem;
        }

        .row-compact .form-group {
            flex: 1;
            /* Hapus position: relative di sini, pindah ke password-input-wrapper */
        }

        .gender-options {
            display: flex;
            gap: 1rem;
            margin-top: 0.3rem;
            margin-bottom: 0.8rem;
        }

        .form-check {
            flex: 1;
        }

        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }

        .form-check-label {
            font-weight: 500;
            color: #495057;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 12px;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .invalid-feedback {
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }

        .auth-links {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .auth-links p {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
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

        /* Loading state */
        .btn-loading {
            position: relative;
            color: transparent;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Password Strength Indicator Styles */
        .password-strength-feedback {
            margin-top: 0.5rem;
            margin-bottom: 0.8rem;
            padding: 0.5rem;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #495057;
            background-color: #f8f9fa;
        }

        .password-strength-meter {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease-in-out, background-color 0.3s ease-in-out;
        }

        .password-strength-text {
            font-weight: 600;
            text-align: right;
            margin-top: 0.3rem;
        }

        /* Strength levels */
        .password-strength-bar.weak { background-color: #dc3545; }
        .password-strength-text.weak { color: #dc3545; }

        .password-strength-bar.medium { background-color: #ffc107; }
        .password-strength-text.medium { color: #ffc107; }

        .password-strength-bar.strong { background-color: #28a745; }
        .password-strength-text.strong { color: #28a745; }

        /* Password requirements checklist */
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements li {
            margin-bottom: 0.2rem;
            display: flex;
            align-items: center;
        }

        .password-requirements li i {
            margin-right: 0.5rem;
            width: 1em; /* Ensure consistent spacing */
            text-align: center;
        }

        .password-requirements li.valid i {
            color: #28a745;
        }

        .password-requirements li.invalid i {
            color: #dc3545;
        }

        /* --- Password Toggle Icon inside input --- */
        .password-input-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 0.8rem; /* Pindahkan margin dari input ke wrapper ini */
        }

        .password-input-wrapper .form-control {
            padding-right: 2.5rem; /* Tambah padding kanan agar teks tidak tertutup ikon */
            margin-bottom: 0; /* Hapus margin-bottom dari input, sekarang ditangani oleh wrapper */
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
        }
        .password-input-wrapper .password-toggle:hover {
            color: #2c3e50;
        }

        /* Sembunyikan ikon mata secara default (saat input kosong) */
        .password-input-wrapper .password-toggle.hidden {
            display: none;
        }
        /* --- Akhir Password Toggle Icon inside input --- */


        /* Responsive */
        @media (max-width: 576px) {
            body {
                padding: 15px;
            }
            
            .register-container {
                max-width: 100%;
            }
            
            .register-body {
                padding: 1rem;
            }

            .row-compact {
                flex-direction: column;
                gap: 0;
            }

            .gender-options {
                flex-direction: column;
                gap: 0.5rem;
            }
            /* Sesuaikan posisi ikon mata pada layar mobile jika perlu */
            .password-input-wrapper .password-toggle {
                right: 1.5rem; /* Mungkin perlu sedikit lebih jauh dari kanan di mobile */
            }
        }

        /* Animation */
        .register-container {
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

<div class="register-container">
    <div class="logo-section">
        <img src="{{ asset('assets/img/logo/logoklinikpratama.png') }}" alt="Logo Klinik Pratama" class="logo-image">
    </div>

    <div class="register-header">
        <h2>Register</h2>
    </div>

    <div class="register-body">
        @if ($errors->any())
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror" 
                    name="name" 
                    id="name" 
                    placeholder="Masukkan nama lengkap" 
                    value="{{ old('name') }}" 
                    required
                    autofocus
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row-compact">
                <div class="form-group">
                    <label for="nomor_ktp" class="form-label">Nomor KTP</label>
                    <input 
                        type="text" 
                        class="form-control @error('nomor_ktp') is-invalid @enderror" 
                        name="nomor_ktp" 
                        id="nomor_ktp" 
                        placeholder="16 digit KTP" 
                        value="{{ old('nomor_ktp') }}" 
                        required
                    >
                    @error('nomor_ktp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        name="email" 
                        id="email" 
                        placeholder="name@example.com" 
                        value="{{ old('email') }}" 
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row-compact">
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            name="password" 
                            id="password" 
                            placeholder="Masukkan password" 
                            required
                        >
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="passwordStrengthFeedback" class="password-strength-feedback d-none">
                        <div class="password-strength-meter">
                            <div id="strengthBar" class="password-strength-bar"></div>
                        </div>
                        <div id="strengthText" class="password-strength-text"></div>
                        <div class="password-requirements mt-2">
                            <ul>
                                <li id="reqLength"><i class="fas fa-times-circle"></i> Minimal 8 karakter</li>
                                <li id="reqUppercase"><i class="fas fa-times-circle"></i> Huruf kapital (A-Z)</li>
                                <li id="reqLowercase"><i class="fas fa-times-circle"></i> Huruf kecil (a-z)</li>
                                <li id="reqNumber"><i class="fas fa-times-circle"></i> Angka (0-9)</li>
                                <li id="reqSpecial"><i class="fas fa-times-circle"></i> Karakter spesial (!@#$...)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            class="form-control" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            placeholder="Ulangi password" 
                            required
                        >
                        <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                    </div>
                </div>
            </div>

            <div class="row-compact">
                <div class="form-group">
                    <label for="phone" class="form-label">Nomor HP</label>
                    <input 
                        type="text" 
                        class="form-control @error('phone') is-invalid @enderror" 
                        name="phone" 
                        id="phone" 
                        placeholder="08xxxxxxxxxx" 
                        value="{{ old('phone') }}" 
                        required
                    >
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                    <input 
                        type="date" 
                        class="form-control" 
                        name="birth_date" 
                        id="birth_date" 
                        value="{{ old('birth_date') }}"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Kelamin</label>
                <div class="gender-options">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'checked' : '' }}>
                        <label class="form-check-label" for="male">Laki-laki</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="Perempuan" {{ old('gender') == 'Perempuan' ? 'checked' : '' }}>
                        <label class="form-check-label" for="female">Perempuan</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Alamat Lengkap (Opsional)</label>
                <textarea 
                    class="form-control" 
                    name="address" 
                    id="address" 
                    rows="2" 
                    placeholder="Masukkan alamat lengkap"
                >{{ old('address') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" id="registerBtn">
                <i class="fas fa-user-plus me-2"></i>
                Register
            </button>
        </form>

        <div class="auth-links">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const passwordStrengthFeedback = document.getElementById('passwordStrengthFeedback');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const reqLength = document.getElementById('reqLength');
        const reqUppercase = document.getElementById('reqUppercase');
        const reqLowercase = document.getElementById('reqLowercase'); // Tetap ada untuk update UI
        const reqNumber = document.getElementById('reqNumber');
        const reqSpecial = document.getElementById('reqSpecial');
        const registerForm = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const birthDateInput = document.getElementById('birth_date');

        // ✅ PERBAIKAN UTAMA: Set maximum date for birth date to today
        if (birthDateInput) {
            const today = new Date().toISOString().split('T')[0];
            birthDateInput.setAttribute('max', today);
        }

        let isPasswordStrong = false; // Flag untuk status kekuatan password
        // Jumlah kriteria yang harus dipenuhi untuk 'Sangat Kuat'
        // Jika sebelumnya 5 (panjang, up, low, num, special), sekarang jadi 4
        const requiredCriteriaForStrong = 4; 

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let score = 0; // Untuk menghitung berapa kriteria yang terpenuhi

            // Show feedback div if password is not empty
            if (password.length > 0) {
                passwordStrengthFeedback.classList.remove('d-none');
            } else {
                passwordStrengthFeedback.classList.add('d-none');
                isPasswordStrong = false; // Reset flag
                updateRegisterButtonState();
                return;
            }

            // Check criteria
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password); // Tetap dicek untuk update UI checklist
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(password);

            // Update checklist UI (semua kriteria akan tetap ditampilkan di checklist)
            updateRequirement(reqLength, hasLength);
            updateRequirement(reqUppercase, hasUppercase);
            updateRequirement(reqLowercase, hasLowercase); // Tetap update UI untuk huruf kecil
            updateRequirement(reqNumber, hasNumber);
            updateRequirement(reqSpecial, hasSpecial);

            // Calculate score for actual strength determination
            if (hasLength) score++;
            if (hasUppercase) score++;
            // if (hasLowercase) score++; // BARIS INI DIHAPUS DARI PERHITUNGAN SCORE
            if (hasNumber) score++;
            if (hasSpecial) score++;

            // Jika ada huruf kecil, itu tetap bisa menambah "nilai" password secara keseluruhan
            // namun tidak wajib. Jadi, 'score' yang dihitung di atas sekarang adalah kriteria WAJIB.
            let fulfilledRequiredCriteria = score; 

            let strength = '';
            let barColorClass = '';
            let textColorClass = '';

            // Tentukan kekuatan berdasarkan kriteria yang TERSISA (4 kriteria wajib)
            if (fulfilledRequiredCriteria === requiredCriteriaForStrong) { // Semua 4 kriteria wajib terpenuhi
                strength = 'Sangat Kuat';
                barColorClass = 'strong';
                textColorClass = 'strong';
                isPasswordStrong = true;
            } else if (fulfilledRequiredCriteria >= (requiredCriteriaForStrong - 1)) { // Misalnya 3 dari 4 kriteria wajib
                strength = 'Cukup Kuat';
                barColorClass = 'medium';
                textColorClass = 'medium';
                isPasswordStrong = false; 
            } else {
                strength = 'Lemah';
                barColorClass = 'weak';
                textColorClass = 'weak';
                isPasswordStrong = false;
            }

            // Lebar bar dihitung berdasarkan semua kriteria yang dicek UI (5 kriteria)
            // Ini agar bar tetap menunjukkan progres visual yang lengkap
            let totalCheckedCriteriaForVisual = (hasLength ? 1 : 0) + (hasUppercase ? 1 : 0) + 
                                               (hasLowercase ? 1 : 0) + (hasNumber ? 1 : 0) + 
                                               (hasSpecial ? 1 : 0);
            strengthBar.style.width = (totalCheckedCriteriaForVisual / 5) * 100 + '%';
            strengthBar.className = 'password-strength-bar ' + barColorClass;
            strengthText.textContent = 'Kekuatan: ' + strength;
            strengthText.className = 'password-strength-text ' + textColorClass;

            updateRegisterButtonState();
        });

        function updateRequirement(element, isValid) {
            element.classList.remove('valid', 'invalid');
            element.querySelector('i').className = isValid ? 'fas fa-check-circle' : 'fas fa-times-circle';
            element.classList.add(isValid ? 'valid' : 'invalid');
        }

        function updateRegisterButtonState() {
            // Tombol diaktifkan HANYA jika isPasswordStrong adalah true (semua kriteria wajib terpenuhi)
            if (isPasswordStrong) {
                registerBtn.disabled = false;
            } else {
                registerBtn.disabled = true;
            }
        }

        // --- Fungsi Toggle Password Visibility ---
        function setupPasswordToggle(inputElement, toggleIconElement) {
            // Initial state: hide icon if input is empty
            if (inputElement.value === '') {
                toggleIconElement.classList.add('hidden');
            }

            // Toggle visibility on click
            toggleIconElement.addEventListener('click', function () {
                const type = inputElement.getAttribute('type') === 'password' ? 'text' : 'password';
                inputElement.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            // Show/hide icon based on input content
            inputElement.addEventListener('input', function() {
                if (this.value === '') {
                    toggleIconElement.classList.add('hidden');
                    // Reset icon to eye if it was changed to eye-slash and input becomes empty
                    toggleIconElement.classList.remove('fa-eye-slash');
                    toggleIconElement.classList.add('fa-eye');
                    inputElement.setAttribute('type', 'password'); // Ensure it's hidden when empty
                } else {
                    toggleIconElement.classList.remove('hidden');
                }
            });
            
            // Trigger initial check if field already has value (e.g., from old input)
            if (inputElement.value !== '') {
                toggleIconElement.classList.remove('hidden');
            }
        }

        // Panggil fungsi untuk password utama
        setupPasswordToggle(passwordInput, togglePassword);
        // Panggil fungsi untuk konfirmasi password
        setupPasswordToggle(confirmPasswordInput, toggleConfirmPassword);

        // Inisialisasi status tombol saat halaman dimuat (jika ada nilai old('password'))
        if (passwordInput.value.length > 0) {
            passwordInput.dispatchEvent(new Event('input'));
        } else {
            updateRegisterButtonState();
        }

        // Form submission loading
        registerForm.addEventListener('submit', function(event) {
            passwordInput.dispatchEvent(new Event('input')); // Re-run validation

            if (passwordInput.value !== confirmPasswordInput.value) {
                event.preventDefault();
                alert('Password dan Konfirmasi Password tidak cocok.');
                return;
            }

            if (!isPasswordStrong) { // isPasswordStrong sekarang berdasarkan 4 kriteria wajib
                event.preventDefault();
                alert('Password Anda belum cukup kuat. Harap penuhi semua kriteria yang wajib.');
                return;
            }

            registerBtn.classList.add('btn-loading');
            registerBtn.disabled = true;
        });

        // Auto-hide alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    });
</script>

</body>
</html>