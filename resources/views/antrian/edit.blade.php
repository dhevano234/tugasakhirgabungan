@extends('layouts.main')

@section('title', 'Edit Antrian')

@section('content')
<!-- Main Content -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header animate">
        <h1><i class="fas fa-edit"></i> Edit Antrian</h1>
        <p>Ubah informasi antrian Anda</p>
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

    <!-- Form Card -->
    <div class="form-card animate">
        <form action="{{ route('antrian.update', $queue->id) }}" method="POST" id="editAntrianForm">
            @csrf
            @method('PUT')

            <!-- Queue Information Section -->
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="fas fa-ticket-alt"></i>
                    Informasi Antrian
                </h6>

                <div class="form-grid">
                    <!-- Nomor Antrian - READONLY -->
                    <div class="form-group full-width">
                        <label for="no_antrian" class="form-label">Nomor Antrian</label>
                        <input type="text"
                               class="form-input readonly-input"
                               id="no_antrian"
                               value="{{ $queue->number }}"
                               readonly
                               tabindex="-1">
                        <small class="form-help">Nomor antrian tidak dapat diubah</small>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="fas fa-user"></i>
                    Informasi Personal
                </h6>

                <div class="form-grid">
                    <!-- Nama Lengkap - READONLY -->
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text"
                               class="form-input readonly-input"
                               id="name"
                               name="name"
                               value="{{ old('name', $queue->name) }}"
                               readonly
                               tabindex="-1">
                    </div>

                    <!-- Nomor HP - READONLY -->
                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor HP</label>
                        <input type="text"
                               class="form-input readonly-input"
                               id="phone"
                               name="phone"
                               value="{{ old('phone', $queue->phone) }}"
                               readonly
                               tabindex="-1">
                    </div>

                    <!-- Jenis Kelamin - READONLY -->
                    <div class="form-group">
                        <label for="gender" class="form-label">Jenis Kelamin</label>
                        <input type="text"
                               class="form-input readonly-input"
                               id="gender"
                               name="gender"
                               value="{{ old('gender', $queue->gender) }}"
                               readonly
                               tabindex="-1">
                    </div>
                </div>
            </div>

            <!-- Medical Information Section -->
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="fas fa-stethoscope"></i>
                    Informasi Medis
                </h6>

                <div class="form-grid">
                    <!-- Poli - EDITABLE -->
                    <div class="form-group">
                        <label for="poli" class="form-label">Poli</label>
                        <select class="form-select @error('poli') is-invalid @enderror"
                                id="poli"
                                name="poli"
                                required>
                            <option value="">-- Pilih Poli --</option>
                            @foreach($services as $p)
                                <option value="{{ $p->nama }}"
                                        {{ old('poli', $queue->poli) == $p->nama ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('poli')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dokter - EDITABLE -->
                    <div class="form-group">
                        <label for="doctor_id" class="form-label">Dokter</label>
                        <select class="form-select @error('doctor_id') is-invalid @enderror"
                                id="doctor_id"
                                name="doctor_id"
                                required>
                            <option value="">-- Pilih Dokter --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->doctor_id }}"
                                        data-specialization="{{ $doctor->spesialisasi }}"
                                        {{ old('doctor_id', $queue->doctor_id) == $doctor->doctor_id ? 'selected' : '' }}>
                                    {{ $doctor->nama }} - {{ $doctor->spesialisasi }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tanggal Antrian - CUSTOM PICKER -->
                    <div class="form-group">
                        <label for="tanggal_antrian_display" class="form-label">Tanggal Antrian</label>
                        <div id="tanggal-antrian-picker" class="tanggal-antrian-picker">
                            {{-- Date options will be rendered here by JavaScript --}}
                        </div>
                        {{-- Hidden input to store the selected date value for form submission --}}
                        <input type="hidden" name="tanggal" id="tanggal" value="{{ old('tanggal', $queue->tanggal->format('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg" id="updateBtn">
                    <i class="fas fa-save"></i>
                    Update Antrian
                </button>
                <a href="/antrian" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </form>
    </div>
</main>

<!-- Form Styles -->
<style>
/* Add custom styles for the new date picker */
.tanggal-antrian-picker {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 10px;
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.date-option {
    padding: 10px 15px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    cursor: pointer;
    background-color: #f8f9fa;
    color: #34495e;
    font-size: 14px;
    text-align: center;
    transition: all 0.2s ease;
    flex: 1 1 auto; /* Allow items to grow and shrink */
    min-width: 80px; /* Minimum width for each date option */
}

.date-option:hover {
    background-color: #e9ecef;
    border-color: #ced4da;
}

.date-option.selected {
    background-color: #3498db;
    color: white;
    border-color: #3498db;
    font-weight: 600;
    box-shadow: 0 2px 5px rgba(52, 152, 219, 0.2);
}

.date-option.disabled {
    background-color: #e9ecef;
    color: #adb5bd;
    cursor: not-allowed;
    opacity: 0.7;
}

/* Ensure the date picker fills the grid column */
.form-group:has(.tanggal-antrian-picker) {
    grid-column: span 2; /* Span full width on larger screens */
}

@media (max-width: 768px) {
    .form-group:has(.tanggal-antrian-picker) {
        grid-column: span 1; /* Back to single column on mobile */
    }
}

/* Existing styles */
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
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
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
.form-select {
    padding: 12px 15px;
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
    background: white;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.readonly-input {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    border-color: #dee2e6 !important;
    cursor: not-allowed !important;
    opacity: 0.8;
    pointer-events: none;
}

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

/* Enhanced Mobile Responsive */
@media (max-width: 768px) {
    .page-header {
        padding: 20px;
        margin-bottom: 20px;
    }

    .page-header h1 {
        font-size: 1.5rem;
    }

    .form-grid {
        grid-template-columns: 1fr !important;
        gap: 15px;
    }

    .form-card {
        padding: 20px;
        margin: 0 10px;
        border-radius: 10px;
    }

    .form-section {
        margin-bottom: 25px;
    }

    .form-section-title {
        font-size: 1rem;
        margin-bottom: 15px;
        padding-bottom: 8px;
    }

    .form-input,
    .form-select {
        padding: 14px 15px;
        font-size: 16px; /* Prevents zoom on iOS */
        border-radius: 8px;
    }

    .form-actions {
        flex-direction: column;
        gap: 12px;
        margin-top: 25px;
    }

    .btn-lg {
        padding: 16px 20px;
        font-size: 16px;
        width: 100%;
        justify-content: center;
    }

    .alert {
        margin: 0 10px 20px 10px;
        padding: 15px;
    }

    .alert-content {
        font-size: 14px;
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

/* Additional Mobile Fixes */
@media (max-width: 480px) {
    .main-content {
        padding: 15px 10px;
    }

    .page-header {
        padding: 15px;
        margin-bottom: 15px;
    }

    .page-header h1 {
        font-size: 1.3rem;
        margin-bottom: 5px;
    }

    .page-header p {
        font-size: 13px;
    }

    .form-card {
        padding: 15px;
        margin: 0 5px;
    }

    .form-section-title {
        font-size: 0.95rem;
        gap: 8px;
    }

    .form-label {
        font-size: 13px;
        margin-bottom: 6px;
    }

    .form-input,
    .form-select {
        padding: 12px;
        font-size: 16px;
    }

    .form-help {
        font-size: 11px;
    }

    .form-error {
        font-size: 11px;
    }

    .btn-lg {
        padding: 14px 18px;
        font-size: 15px;
    }

    .alert {
        margin: 0 5px 15px 5px;
        padding: 12px;
    }

    .alert-close {
        top: 10px;
        right: 10px;
        font-size: 16px;
    }
}

/* Tablet Responsive */
@media (min-width: 769px) and (max-width: 1024px) {
    .form-grid {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 18px;
    }

    .form-card {
        padding: 25px;
    }

    .main-content {
        padding: 25px;
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editAntrianForm');
    const updateBtn = document.getElementById('updateBtn');
    const doctorSelect = document.getElementById('doctor_id');

    // Disable semua readonly input
    const readonlyInputs = document.querySelectorAll('.readonly-input');
    readonlyInputs.forEach(function(input) {
        input.addEventListener('click', function(e) {
            e.preventDefault();
            return false;
        });

        input.addEventListener('focus', function(e) {
            e.preventDefault();
            this.blur();
            return false;
        });
    });

    // Show doctor specialization when selected
    if (doctorSelect) {
        doctorSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const specialization = selectedOption.getAttribute('data-specialization');
                console.log('Dokter dipilih:', selectedOption.text);
            }
        });
    }

    // Prevent double submission
    if (form) {
        form.addEventListener('submit', function() {
            updateBtn.disabled = true;
            updateBtn.classList.add('btn-loading');
            updateBtn.innerHTML = '<i class="fas fa-spinner"></i> Memproses...';
        });
    }

    // Close alert functionality
    document.querySelectorAll('.alert-close').forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });

    // --- Custom Date Picker Logic ---
    const tanggalAntrianPicker = document.getElementById('tanggal-antrian-picker');
    // The actual hidden input for form submission, retrieve its initial value
    const hiddenTanggalInput = document.getElementById('tanggal');
    const initialSelectedDate = hiddenTanggalInput.value ? new Date(hiddenTanggalInput.value) : null;

    function formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`; // YYYY-MM-DD format
    }

    function formatDisplayDate(date) {
        const options = { weekday: 'short', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('id-ID', options); // e.g., "Kam, 20 Jun"
    }

    function renderDateOptions() {
        tanggalAntrianPicker.innerHTML = ''; // Clear previous options
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Normalize to start of day for comparison

        for (let i = 0; i < 7; i++) { // Generate options for the next 7 days
            const date = new Date(today);
            date.setDate(today.getDate() + i);

            const dateString = formatDateForInput(date);
            const displayString = formatDisplayDate(date);

            const dateOption = document.createElement('div');
            dateOption.classList.add('date-option');
            dateOption.setAttribute('data-date', dateString);
            dateOption.textContent = displayString;

            // Check if this date option should be selected
            if (initialSelectedDate && formatDateForInput(initialSelectedDate) === dateString) {
                dateOption.classList.add('selected');
            } else if (!initialSelectedDate && i === 0) { // If no initial date, select today by default
                dateOption.classList.add('selected');
                hiddenTanggalInput.value = dateString; // Set hidden input to today's date
            }

            dateOption.addEventListener('click', function() {
                // Remove selected class from all options
                tanggalAntrianPicker.querySelectorAll('.date-option').forEach(option => {
                    option.classList.remove('selected');
                });
                // Add selected class to the clicked option
                this.classList.add('selected');
                // Update the hidden input value
                hiddenTanggalInput.value = this.getAttribute('data-date');
            });
            tanggalAntrianPicker.appendChild(dateOption);
        }
    }

    // Initial render of date options
    renderDateOptions();

    // Re-select the `old('tanggal')` value (or the one from $queue) if it exists
    // This part is crucial for making sure the pre-filled date gets selected on the custom picker.
    if (hiddenTanggalInput.value) {
        const previouslySelected = tanggalAntrianPicker.querySelector(`[data-date="${hiddenTanggalInput.value}"]`);
        if (previouslySelected) {
            tanggalAntrianPicker.querySelectorAll('.date-option').forEach(option => {
                option.classList.remove('selected');
            });
            previouslySelected.classList.add('selected');
        } else {
            // If the $queue date is not within the next 7 days, we might need to handle it.
            // For now, it will default to today. You might consider adding a message or
            // dynamically adding that specific date as an option if it's outside the 7-day range.
            console.warn("Tanggal antrian yang ada tidak ditemukan di pilihan 7 hari ke depan.");
        }
    }
});
</script>
@endsection