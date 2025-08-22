@extends('layouttempalte.master')

@section('title', 'Edit Profil - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-user-edit me-2"></i>Edit Profil</h2>
                    <p class="text-muted mb-0">Kelola informasi profil dan keamanan akun Anda</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @error('general')
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @enderror

            <div class="row">
                <!-- Profile Information -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Profil</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <div class="row mb-4">
                                    <div class="col-md-3 text-center">
                                        <div class="profile-preview-container mb-3"
                                             style="width: 150px; height: 150px; margin: 0 auto; border-radius: 50% !important; overflow: hidden !important; border: 4px solid #25d366 !important;">
                                            <!-- Current photo display -->
                                            @if($user->profile_photo)
                                                <img src="{{ asset('storage/profile-photos/' . $user->profile_photo) }}"
                                                     alt="Profile Photo"
                                                     style="width: 100% !important; height: 100% !important; object-fit: cover !important; border-radius: 50% !important; display: block;">
                                            @else
                                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; font-weight: bold; border-radius: 50%;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- File input - visible and simple -->
                                        <div class="mt-2">
                                            <div class="mb-2">
                                                <label for="profile_photo" class="form-label">Upload Foto Baru:</label>
                                                <input type="file"
                                                       id="profile_photo"
                                                       name="profile_photo"
                                                       class="form-control form-control-sm"
                                                       accept="image/*">
                                            </div>

                                            @if($user->profile_photo)
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           id="remove_photo"
                                                           name="remove_photo"
                                                           value="1">
                                                    <label class="form-check-label text-danger" for="remove_photo">
                                                        <i class="fas fa-trash me-1"></i>Hapus Foto Saat Ini
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block mt-2">JPG, PNG, GIF, WEBP (Max: 2MB)</small>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                       value="{{ old('name', $user->name) }}" required>
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="nik" class="form-label">NIK Baru</label>
                                                <input type="text" class="form-control" id="nik" name="nik"
                                                       value="{{ old('nik', $user->nik) }}" required>
                                                <div class="form-text">Masukkan NIK sesuai kebutuhan</div>
                                                @error('nik')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="current_nik" class="form-label">NIK Saat Ini (Verifikasi)</label>
                                                <input type="text" class="form-control" id="current_nik" name="current_nik"
                                                       placeholder="Masukkan NIK saat ini untuk verifikasi" required>
                                                <div class="form-text">Masukkan NIK yang sedang aktif untuk verifikasi</div>
                                                @error('current_nik')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="role" class="form-label">Role</label>
                                                <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><strong>Perubahan Nama:</strong> Dapat dilakukan langsung</li>
                                        <li><strong>Perubahan NIK:</strong> Memerlukan persetujuan admin</li>
                                    </ul>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Ubah Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="current_password" class="form-label">Password Saat Ini</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        @error('current_password', 'updatePassword')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        @error('password', 'updatePassword')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i>Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Account Info Sidebar -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Akun</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>NIK:</strong></td>
                                    <td>{{ $user->nik }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td><span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">{{ ucfirst($user->role) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Bergabung:</strong></td>
                                    <td>{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update:</strong></td>
                                    <td>{{ $user->updated_at ? $user->updated_at->format('d M Y H:i') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Security Tips -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Tips Keamanan</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Gunakan password yang kuat
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Jangan bagikan NIK Anda
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Logout setelah selesai
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Laporkan aktivitas mencurigakan
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    <style>
        .profile-preview-container {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            position: relative;
            border-radius: 50% !important;
            overflow: hidden !important;
            border: 4px solid #25d366 !important;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3) !important;
            transition: all 0.3s ease;
        }

        .profile-preview-container:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4) !important;
        }

        .profile-preview-image {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            display: block !important;
            border-radius: 50% !important;
            border: none !important;
        }

        .profile-default-avatar {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            font-weight: bold;
            border-radius: 50% !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6b4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        .card {
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: none;
            border-radius: 10px;
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            border-radius: 10px 10px 0 0 !important;
        }

        /* WhatsApp-style rounded profile photos everywhere */
        .rounded-circle {
            border: 2px solid #25d366 !important;
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.2) !important;
            transition: all 0.3s ease !important;
            border-radius: 50% !important;
        }

        .rounded-circle:hover {
            transform: scale(1.1) !important;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4) !important;
        }

        /* File input styling to match WhatsApp green theme */
        .form-control:focus {
            border-color: #25d366;
            box-shadow: 0 0 0 0.2rem rgba(37, 211, 102, 0.25);
        }

        .form-check-input:checked {
            background-color: #25d366;
            border-color: #25d366;
        }
    </style>
@endsection
