@extends('layouttempalte.master')

@section('title', 'Detail Pengguna - SIRPO')

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 text-dark">
                    <i class="ti ti-user me-2"></i>Detail Pengguna
                </h1>
                <p class="text-muted">Informasi lengkap pengguna {{ $user->name }}</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                    <i class="ti ti-edit me-1"></i>Edit
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- User Information Card -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-user-circle me-2"></i>Informasi Pengguna
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        @if($user->profile_photo)
                            <img src="{{ asset('storage/profile-photos/' . $user->profile_photo) }}"
                                 alt="Profile Photo" class="profile-photo-large">
                        @else
                            <div class="profile-avatar-large">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <small class="text-muted d-block mt-2">Foto Profil</small>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Nama Lengkap</label>
                                    <p class="fw-bold">{{ $user->name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">NIK</label>
                                    <p class="fw-bold">{{ $user->nik }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Role</label>
                                    <p>
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'primary' }} fs-6">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Divisi</label>
                                    <p class="fw-bold">{{ $user->divisi ?? 'Tidak Ditentukan' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Bergabung Pada</label>
                                    <p class="fw-bold">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-settings me-2"></i>Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                        <i class="ti ti-edit me-2"></i>Edit Pengguna
                    </a>

                    @if($user->id !== auth()->id())
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                            <i class="ti ti-key me-2"></i>Reset Password
                        </button>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="ti ti-trash me-2"></i>Hapus Pengguna
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PB Statistics (if user has PBs) -->
@if(!empty($pbStats))
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-chart-bar me-2"></i>Statistik PB
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="mb-2">
                                <i class="ti ti-file-invoice fs-1 text-primary"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format($pbStats['total_pb']) }}</h4>
                            <p class="text-muted mb-0">Total PB</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="mb-2">
                                <i class="ti ti-check-circle fs-1 text-success"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format($pbStats['approved_pb']) }}</h4>
                            <p class="text-muted mb-0">Disetujui</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="mb-2">
                                <i class="ti ti-clock fs-1 text-warning"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format($pbStats['pending_pb']) }}</h4>
                            <p class="text-muted mb-0">Pending</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="mb-2">
                                <i class="ti ti-x-circle fs-1 text-danger"></i>
                            </div>
                            <h4 class="mb-0">{{ number_format($pbStats['rejected_pb']) }}</h4>
                            <p class="text-muted mb-0">Ditolak</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Anda akan mereset password untuk pengguna <strong>{{ $user->name }}</strong>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                    </div>
                    <p>Apakah Anda yakin ingin menghapus pengguna <strong>{{ $user->name }}</strong>?</p>
                    <p class="text-muted">NIK: {{ $user->nik }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto hide alerts after 5 seconds
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
.profile-photo-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e9ecef;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.profile-avatar-large {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    border: 3px solid #e9ecef;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin: 0 auto;
}
</style>
@endsection
