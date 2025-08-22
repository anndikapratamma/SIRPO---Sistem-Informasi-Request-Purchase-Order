@extends('layouttempalte.master')

@section('title', 'Detail Permintaan Perubahan - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-user-edit me-2"></i>Detail Permintaan Perubahan</h2>
                    <p class="text-muted mb-0">Detail permintaan #{{ $changeRequest->id }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.profile-changes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Request Details -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informasi Permintaan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID Permintaan:</strong></td>
                                            <td>#{{ $changeRequest->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipe Perubahan:</strong></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $changeRequest->request_type_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge {{ $changeRequest->status_badge_class }}">
                                                    {{ $changeRequest->status_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Dibuat:</strong></td>
                                            <td>{{ $changeRequest->created_at->format('d M Y H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        @if($changeRequest->approved_at)
                                        <tr>
                                            <td><strong>Tanggal Diproses:</strong></td>
                                            <td>{{ $changeRequest->approved_at->format('d M Y H:i:s') }}</td>
                                        </tr>
                                        @endif
                                        @if($changeRequest->approvedBy)
                                        <tr>
                                            <td><strong>Diproses oleh:</strong></td>
                                            <td>{{ $changeRequest->approvedBy->name }}</td>
                                        </tr>
                                        @endif
                                        @if($changeRequest->admin_notes)
                                        <tr>
                                            <td><strong>Catatan Admin:</strong></td>
                                            <td>{{ $changeRequest->admin_notes }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Informasi Pengguna
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <div class="user-avatar-large mx-auto mb-3">
                                        {{ strtoupper(substr($changeRequest->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Nama:</strong></td>
                                            <td>{{ $changeRequest->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIK Saat Ini:</strong></td>
                                            <td><code>{{ $changeRequest->user->nik ?? 'N/A' }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Role:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $changeRequest->user->role === 'admin' ? 'danger' : 'primary' }}">
                                                    {{ ucfirst($changeRequest->user->role ?? 'N/A') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Divisi:</strong></td>
                                            <td>{{ $changeRequest->user->divisi ?? 'Tidak ada' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bergabung:</strong></td>
                                            <td>{{ $changeRequest->user->created_at ? $changeRequest->user->created_at->format('d M Y') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-exchange-alt me-2"></i>Detail Perubahan
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($changeRequest->request_type === 'nik_change')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="change-section">
                                            <h6 class="text-muted">Data Lama</h6>
                                            <div class="bg-light p-3 rounded">
                                                <strong>NIK:</strong>
                                                <code class="d-block fs-5">{{ $changeRequest->old_data['nik'] ?? 'N/A' }}</code>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="change-section">
                                            <h6 class="text-muted">Data Baru (Diminta)</h6>
                                            <div class="bg-light p-3 rounded border-start border-primary border-3">
                                                <strong>NIK:</strong>
                                                <code class="d-block fs-5 text-primary">{{ $changeRequest->new_data['nik'] ?? 'N/A' }}</code>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($changeRequest->request_type === 'name_change')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="change-section">
                                            <h6 class="text-muted">Data Lama</h6>
                                            <div class="bg-light p-3 rounded">
                                                <strong>Nama:</strong>
                                                <div class="fs-5">{{ $changeRequest->old_data['name'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="change-section">
                                            <h6 class="text-muted">Data Baru (Diminta)</h6>
                                            <div class="bg-light p-3 rounded border-start border-primary border-3">
                                                <strong>Nama:</strong>
                                                <div class="fs-5 text-primary">{{ $changeRequest->new_data['name'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($changeRequest->request_type === 'nik_change')
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Peringatan:</strong> Perubahan NIK akan mempengaruhi sistem autentikasi pengguna. Pastikan NIK yang baru valid dan belum digunakan.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions Sidebar -->
                <div class="col-md-4">
                    @if($changeRequest->isPending())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>Aksi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success" onclick="approveRequest({{ $changeRequest->id }})">
                                    <i class="fas fa-check me-2"></i>Setujui Permintaan
                                </button>
                                <button type="button" class="btn btn-danger" onclick="rejectRequest({{ $changeRequest->id }})">
                                    <i class="fas fa-times me-2"></i>Tolak Permintaan
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Timeline
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Permintaan Dibuat</h6>
                                        <p class="text-muted mb-0">{{ $changeRequest->created_at->format('d M Y H:i') }}</p>
                                        <small class="text-muted">oleh {{ $changeRequest->user->name }}</small>
                                    </div>
                                </div>

                                @if($changeRequest->approved_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $changeRequest->isApproved() ? 'success' : 'danger' }}"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            {{ $changeRequest->isApproved() ? 'Permintaan Disetujui' : 'Permintaan Ditolak' }}
                                        </h6>
                                        <p class="text-muted mb-0">{{ $changeRequest->approved_at->format('d M Y H:i') }}</p>
                                        @if($changeRequest->approvedBy)
                                        <small class="text-muted">oleh {{ $changeRequest->approvedBy->name }}</small>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Menunggu Persetujuan</h6>
                                        <p class="text-muted mb-0">Status saat ini</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Setujui Permintaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Dengan menyetujui permintaan ini, perubahan akan langsung diterapkan ke data pengguna.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan Admin (Opsional)</label>
                            <textarea name="admin_notes" class="form-control" rows="3"
                                      placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Permintaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Permintaan yang ditolak tidak dapat diubah kembali. Pengguna harus membuat permintaan baru.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="admin_notes" class="form-control" rows="3" required
                                      placeholder="Jelaskan alasan penolakan secara detail..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function approveRequest(requestId) {
            document.getElementById('approveForm').action = `/admin/profile-changes/${requestId}/approve`;
            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
            modal.show();
        }

        function rejectRequest(requestId) {
            document.getElementById('rejectForm').action = `/admin/profile-changes/${requestId}/reject`;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }
    </script>
@endsection

@section('styles')
    <style>
        .user-avatar-large {
            width: 80px;
            height: 80px;
            background-color: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 2rem;
        }

        .change-section {
            margin-bottom: 1rem;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -2rem;
            top: 0.25rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 1.5rem;
            width: 2px;
            height: calc(100% + 0.5rem);
            background-color: #dee2e6;
        }

        .timeline-content h6 {
            margin-bottom: 0.25rem;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .border-start {
            border-left: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important;
        }

        .border-3 {
            border-width: 3px !important;
        }
    </style>
@endsection
