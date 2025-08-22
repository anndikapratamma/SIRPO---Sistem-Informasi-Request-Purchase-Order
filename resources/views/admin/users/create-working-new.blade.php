@extends('layouttempalte.master')

@section('title', 'Tambah Pengguna - SIRPO')

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 text-dark">
                    <i class="ti ti-user-plus me-2"></i>Tambah Pengguna Baru
                </h1>
                <p class="text-muted">Buat akun pengguna baru untuk sistem SIRPO</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>

<!-- Create User Form -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-form me-2"></i>Form Pengguna Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror"
                                       id="nik" name="nik" value="{{ old('nik') }}" required maxlength="16">
                                <div class="form-text">NIK harus terdiri dari 16 digit</div>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="divisi" class="form-label">Divisi <span class="text-danger">*</span></label>
                                <select class="form-select @error('divisi') is-invalid @enderror" id="divisi" name="divisi" required>
                                    <option value="">Pilih Divisi</option>
                                    <option value="OP" {{ old('divisi') == 'OP' ? 'selected' : '' }}>OP (Operasional)</option>
                                    <option value="AKT" {{ old('divisi') == 'AKT' ? 'selected' : '' }}>AKT (Akuntansi)</option>
                                    <option value="IT" {{ old('divisi') == 'IT' ? 'selected' : '' }}>IT (Information Technology)</option>
                                    <option value="HR" {{ old('divisi') == 'HR' ? 'selected' : '' }}>HR (Human Resources)</option>
                                    <option value="FINANCE" {{ old('divisi') == 'FINANCE' ? 'selected' : '' }}>FINANCE</option>
                                </select>
                                @error('divisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="ti ti-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Password minimal 8 karakter</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="ti ti-eye" id="toggleConfirmIcon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Ulangi password yang sama</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>Simpan Pengguna
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Information Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="ti ti-info-circle me-2"></i>Informasi Penting
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Catatan untuk Admin:</h6>
                    <ul class="mb-0 small">
                        <li>NIK harus unik dan terdiri dari 16 digit</li>
                        <li>Password minimal 8 karakter</li>
                        <li>Role menentukan akses sistem</li>
                        <li>Divisi digunakan untuk filter data</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6 class="alert-heading">Role Explanation:</h6>
                    <ul class="mb-0 small">
                        <li><strong>Admin:</strong> Akses penuh ke semua fitur</li>
                        <li><strong>User:</strong> Akses terbatas sesuai divisi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        toggleIcon.className = type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
    });

    // Toggle confirm password visibility
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPassword = document.getElementById('password_confirmation');
    const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');

    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPassword.setAttribute('type', type);
        toggleConfirmIcon.className = type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
    });

    // NIK validation (only numbers, max 16 digits)
    const nikInput = document.getElementById('nik');
    nikInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
    });
});
</script>
@endsection
