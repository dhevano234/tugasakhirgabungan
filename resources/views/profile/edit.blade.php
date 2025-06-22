@extends('layouts.main')

@section('title', 'Edit Profile')

@section('content')
<main class="main-content">
    <div class="page-header animate">
        <h1><i class="fas fa-user-edit"></i> Edit Profile</h1>
        <p>Kelola informasi akun dan keamanan Anda</p>
    </div>

    {{-- Alert untuk error --}}
    @if ($errors->any())
        <div class="alert alert-danger animate">
            <i class="fas fa-exclamation-circle"></i>
            <div class="alert-content">
                <strong>Oops!</strong> Ada masalah dengan input Anda:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="alert-close">&times;</button>
        </div>
    @endif

    {{-- Alert untuk success --}}
    @if (session('success'))
        <div class="alert alert-success animate">
            <i class="fas fa-check-circle"></i>
            <div class="alert-content">
                {{ session('success') }}
            </div>
            <button type="button" class="alert-close">&times;</button>
        </div>
    @endif

    <div class="form-card animate">
        <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
            @csrf
            @method('PUT')
            
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="fas fa-user"></i>
                    Informasi Personal
                </h6>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" 
                               class="form-input readonly-input" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', Auth::user()->name) }}" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" 
                               class="form-input readonly-input"
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor Telepon *</label>
                        <input type="text" 
                               class="form-input readonly-input" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label for="nomor_ktp" class="form-label">No. KTP *</label>
                        <input type="text" 
                               class="form-input readonly-input" 
                               id="nomor_ktp" 
                               name="nomor_ktp" 
                               value="{{ old('nomor_ktp', $user->nomor_ktp) }}" 
                               maxlength="16"
                               readonly>
                    </div>

                    <div class="form-group">
                        <label for="birth_date" class="form-label">Tanggal Lahir *</label>
                        <input type="date" 
                               class="form-input readonly-input" 
                               id="birth_date" 
                               name="birth_date" 
                               value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}"
                               readonly>
                    </div>

                    <div class="form-group full-width">
                        <label for="address" class="form-label">Alamat *</label>
                        <textarea class="form-textarea @error('address') is-invalid @enderror" 
                                   id="address" 
                                   name="address" 
                                   rows="3" 
                                   placeholder="Masukkan alamat lengkap"
                                   readonly>{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="form-error">{{ $message }}</div>
                        @enderror

                        {{-- Konten baru untuk pesan admin --}}
                        <div class="admin-contact-info">
                            <small class="form-text text-danger">
                                Jika ingin merubah data diri, silakan hubungi admin via WhatsApp: 
                                <a href="https://wa.me/6289678784190?text=Halo%20admin%2C%0ASaya%20{{ Auth::user()->name }}%2C%20dengan%20NIK%20{{ Auth::user()->nomor_ktp }}%2C%20ingin%20mengajukan%20perubahan%20data%20diri%3A%0A%0AData%20yang%20ingin%20saya%20ubah%20(mohon%20sebutkan%20data%20apa%20diri%20apa%20yang%20ingin%20diubah.%20Misalnya%20%3A%20Nama%20Lengkap%2C%20Email%2C%20Nomor%20Telepon%2C%20Nomor%20KTP%2FNIK%2C%20Tanggal%20Lahir%2C%20Alamat)"
   target="_blank" 
   class="whatsapp-link">
   <i class="fab fa-whatsapp"></i> 0896-7878-4190
</a>
                            </small>
                            <small class="form-text text-danger admin-hours">
                                Jam Operasional Admin: 08:00 WIB - 20:00 WIB
                            </small>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>

    <div class="form-card animate">
        <form action="{{ route('password.update') }}" method="POST" id="passwordForm">
            @csrf
            @method('PUT')
            
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="fas fa-lock"></i>
                    Ubah Password
                </h6>
                
                <div class="form-grid password-grid">
                    <div class="form-group">
                        <label for="current_password" class="form-label">Password Saat Ini *</label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   class="form-input @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            <i class="fas fa-eye password-toggle" id="toggleCurrentPassword"></i>
                        </div>
                        @error('current_password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru *</label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   class="form-input @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <i class="fas fa-eye password-toggle" id="toggleNewPassword"></i>
                        </div>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
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
                                    <li id="reqNumber"><i class="fas fa-times-circle"></i> Angka (0-9)</li>
                                    <li id="reqSpecial"><i class="fas fa-times-circle"></i> Karakter spesial (!@#$...)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru *</label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   class="form-input" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                            <i class="fas fa-eye password-toggle" id="toggleConfirmNewPassword"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-danger btn-lg" id="passwordBtn">
                    <i class="fas fa-key"></i>
                    Ubah Password
                </button>
            </div>
        </form>
    </div>
</main>

<style>
/* CSS yang sudah ada */
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

.readonly-input,
.form-textarea { /* Digabungkan untuk gaya readonly */
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    border-color: #dee2e6 !important;
    cursor: not-allowed !important;
    opacity: 0.8;
    pointer-events: none; /* Mencegah interaksi mouse */
}


.alert {
    background: white;
    border: none;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    display: flex;
    align-items: flex-start;
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

.alert-content {
    flex: 1;
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

.form-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.form-section {
    margin-bottom: 30px;
}

.form-section:last-of-type {
    margin-bottom: 20px;
}

.form-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #ecf0f1;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section-title i {
    color: #3498db;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.password-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-input,
.form-select,
.form-textarea {
    padding: 12px 15px;
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
    background: white;
    font-family: inherit;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 80px;
}

/* --- Start Password Input Group Styles (untuk ikon mata di dalam input) --- */
.password-input-wrapper { 
    position: relative;
    width: 100%;
}

.password-input-wrapper .form-input {
    padding-right: 2.5rem; /* Beri ruang untuk ikon mata */
}

.password-toggle { 
    position: absolute;
    right: 0.75rem; /* Posisi ikon dari kanan input */
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #7f8c8d;
    font-size: 1.1rem;
    padding: 0.25rem;
    z-index: 2; /* Pastikan ikon di atas input */
    background: transparent; /* Pastikan tidak ada background bawaan button */
    border: none; /* Pastikan tidak ada border bawaan button */
}

.password-toggle:hover {
    color: #3498db;
}

.password-toggle.hidden {
    display: none; /* Untuk menyembunyikan ikon saat input kosong */
}
/* --- End Password Input Group Styles --- */


.form-help {
    color: #7f8c8d;
    font-size: 12px;
    margin-top: 5px;
    font-style: italic;
}

.form-error {
    color: #e74c3c;
    font-size: 12px;
    margin-top: 5px;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ecf0f1;
}

.btn-lg {
    padding: 15px 30px;
    font-size: 16px;
}

.btn-danger {
    background: linear-gradient(45deg, #e74c3c, #c0392b);
    color: white;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
}

/* --- Start Password Strength Indicator Styles (copied from register.blade.php) --- */
.password-strength-feedback {
    margin-top: 0.5rem;
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

.password-strength-bar.weak { background-color: #dc3545; }
.password-strength-text.weak { color: #dc3545; }

.password-strength-bar.medium { background-color: #ffc107; }
.password-strength-text.medium { color: #ffc107; }

.password-strength-bar.strong { background-color: #28a745; }
.password-strength-text.strong { color: #28a745; }

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
    width: 1em;
    text-align: center;
}

.password-requirements li.valid i {
    color: #28a745;
}

.password-requirements li.invalid i {
    color: #dc3545;
}
/* --- End Password Strength Indicator Styles --- */

/* Style tambahan untuk link WhatsApp */
.whatsapp-link {
    color: #25D366; /* Warna hijau WhatsApp */
    font-weight: bold;
    text-decoration: none;
    display: inline-flex; /* Agar ikon dan teks sejajar */
    align-items: center;
    gap: 5px; /* Jarak antara ikon dan teks */
    transition: color 0.2s ease;
}

.whatsapp-link:hover {
    color: #1DA851; /* Warna hijau sedikit lebih gelap saat hover */
    text-decoration: underline;
}

.whatsapp-link i.fab.fa-whatsapp {
    font-size: 1.1em; /* Ukuran ikon sedikit lebih besar */
}

/* Penyesuaian baru untuk jam operasional */
.admin-contact-info {
    margin-top: 10px; /* Memberi sedikit jarak dari input di atasnya */
}

.admin-contact-info .form-text {
    display: block; /* Memastikan setiap small di baris baru */
}

.admin-hours {
    margin-top: 5px; /* Memberi jarak antara link WA dan jam operasional */
    font-weight: normal; /* Pastikan tidak bold */
}

@media (max-width: 768px) {
    .form-grid,
    .password-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-card {
        padding: 20px;
    }
}

/* Loading state */
.btn-loading {
    opacity: 0.6;
    pointer-events: none;
}

.btn-loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
// Fungsi yang diadaptasi dari register.blade.php
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
    
    // Trigger initial check if field already has value
    if (inputElement.value !== '') {
        toggleIconElement.classList.remove('hidden');
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const passwordForm = document.getElementById('passwordForm');
    const passwordBtn = document.getElementById('passwordBtn'); // Pastikan ID ini ada di form password

    // Get password elements for strength validation
    const newPasswordInput = document.getElementById('password'); // Ini adalah "Password Baru"
    const confirmNewPasswordInput = document.getElementById('password_confirmation'); // Ini adalah "Konfirmasi Password Baru"
    const passwordStrengthFeedback = document.getElementById('passwordStrengthFeedback');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const reqLength = document.getElementById('reqLength');
    const reqUppercase = document.getElementById('reqUppercase');
    const reqNumber = document.getElementById('reqNumber');
    const reqSpecial = document.getElementById('reqSpecial');

    // Get toggle icons for all password fields
    const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const toggleConfirmNewPassword = document.getElementById('toggleConfirmNewPassword');

    let isNewPasswordStrong = false; // Flag untuk status kekuatan password baru
    const requiredCriteriaForStrong = 4; // Minimal 4 kriteria wajib untuk "Sangat Kuat"

    // Apply toggle visibility to all password fields
    setupPasswordToggle(document.getElementById('current_password'), toggleCurrentPassword);
    setupPasswordToggle(newPasswordInput, toggleNewPassword);
    setupPasswordToggle(confirmNewPasswordInput, toggleConfirmNewPassword);


    // Logic for New Password Strength Validation
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            let score = 0;

            // Show feedback div if password is not empty
            if (password.length > 0) {
                passwordStrengthFeedback.classList.remove('d-none');
            } else {
                passwordStrengthFeedback.classList.add('d-none');
                isNewPasswordStrong = false; // Reset flag
                // Tombol submit akan diupdate oleh fungsi updatePasswordFormButtonState()
                return;
            }

            // Check criteria
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(password);

            // Update checklist UI
            updateRequirement(reqLength, hasLength);
            updateRequirement(reqUppercase, hasUppercase);
            updateRequirement(reqNumber, hasNumber);
            updateRequirement(reqSpecial, hasSpecial);

            // Calculate score for actual strength determination
            if (hasLength) score++;
            if (hasUppercase) score++;
            if (hasNumber) score++;
            if (hasSpecial) score++;

            let fulfilledRequiredCriteria = score;

            let strength = '';
            let barColorClass = '';
            let textColorClass = '';

            // Determine strength based on required criteria
            if (fulfilledRequiredCriteria === requiredCriteriaForStrong) {
                strength = 'Sangat Kuat';
                barColorClass = 'strong';
                textColorClass = 'strong';
                isNewPasswordStrong = true;
            } else if (fulfilledRequiredCriteria >= (requiredCriteriaForStrong - 1)) { // e.g., 3 out of 4 criteria
                strength = 'Cukup Kuat';
                barColorClass = 'medium';
                textColorClass = 'medium';
                isNewPasswordStrong = false;
            } else {
                strength = 'Lemah';
                barColorClass = 'weak';
                textColorClass = 'weak';
                isNewPasswordStrong = false;
            }

            // Update strength bar and text
            strengthBar.style.width = (score / requiredCriteriaForStrong) * 100 + '%';
            strengthBar.className = 'password-strength-bar ' + barColorClass;
            strengthText.textContent = 'Kekuatan: ' + strength;
            strengthText.className = 'password-strength-text ' + textColorClass;

            updatePasswordFormButtonState(); // Update submit button state
        });
    }

    function updateRequirement(element, isValid) {
        element.classList.remove('valid', 'invalid');
        element.querySelector('i').className = isValid ? 'fas fa-check-circle' : 'fas fa-times-circle';
        element.classList.add(isValid ? 'valid' : 'invalid');
    }

    // Fungsi untuk mengaktifkan/menonaktifkan tombol Ubah Password
    function updatePasswordFormButtonState() {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = newPasswordInput.value;
        const confirmNewPassword = confirmNewPasswordInput.value;

        // Tombol aktif jika:
        // 1. Password saat ini tidak kosong
        // 2. Password baru cukup kuat (isNewPasswordStrong)
        // 3. Password baru dan konfirmasi password baru cocok
        if (currentPassword.length > 0 && isNewPasswordStrong && (newPassword === confirmNewPassword)) {
            passwordBtn.disabled = false;
        } else {
            passwordBtn.disabled = true;
        }
    }

    // Trigger initial check for new password strength if field is pre-filled (unlikely for new pass)
    if (newPasswordInput && newPasswordInput.value.length > 0) {
        newPasswordInput.dispatchEvent(new Event('input'));
    } else {
        updatePasswordFormButtonState(); // Initial state disabled if new pass empty
    }

    // Listen for changes on current password and confirm new password as well
    document.getElementById('current_password').addEventListener('input', updatePasswordFormButtonState);
    confirmNewPasswordInput.addEventListener('input', updatePasswordFormButtonState);


    // Password form submission validation
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(event) {
            // Re-run validation for new password just before submission
            if (newPasswordInput) {
                newPasswordInput.dispatchEvent(new Event('input'));
            }
            
            // Check if current password is empty
            if (document.getElementById('current_password').value.length === 0) {
                event.preventDefault();
                alert('Password Saat Ini tidak boleh kosong.');
                return;
            }

            // Check if new password and confirm new password match
            if (newPasswordInput.value !== confirmNewPasswordInput.value) {
                event.preventDefault();
                alert('Password Baru dan Konfirmasi Password Baru tidak cocok.');
                return;
            }

            // Check if new password is strong enough
            if (!isNewPasswordStrong) {
                event.preventDefault();
                alert('Password Baru Anda belum cukup kuat. Harap penuhi semua kriteria yang wajib.');
                return;
            }

            // If all validations pass, disable button and show loading state
            passwordBtn.disabled = true;
            passwordBtn.classList.add('btn-loading');
            passwordBtn.innerHTML = '<i class="fas fa-spinner"></i> Mengubah...';
        });
    }

    // Close alert functionality
    document.querySelectorAll('.alert-close').forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
});
</script>
@endsection