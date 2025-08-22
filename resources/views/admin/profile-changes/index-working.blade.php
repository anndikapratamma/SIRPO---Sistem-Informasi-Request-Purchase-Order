@extends('layouttempalte.master')

@section('title', 'Kelola Perubahan Profil - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-user-edit me-2"></i>Kelola Perubahan Profil</h2>
                    <p class="text-muted mb-0">Persetujuan permintaan perubahan NIK dan profil pengguna</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-1"></i>Kelola Akun
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Dashboard
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

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-1">{{ $stats['pending_count'] ?? 0 }}</h3>
                            <p class="mb-0">Menunggu Persetujuan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h3 class="mb-1">{{ $stats['approved_count'] ?? 0 }}</h3>
                            <p class="mb-0">Disetujui</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-times-circle fa-2x mb-2"></i>
                            <h3 class="mb-1">{{ $stats['rejected_count'] ?? 0 }}</h3>
                            <p class="mb-0">Ditolak</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-id-card fa-2x mb-2"></i>
                            <h3 class="mb-1">{{ $stats['nik_change_count'] ?? 0 }}</h3>
                            <p class="mb-0">Perubahan NIK</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.profile-changes.index') }}">
                        <div class="row align-items-center">
                            <div class="col-md-3 mb-3">
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}" placeholder="Cari nama atau NIK...">
                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select name="type" class="form-select">
                                    <option value="">Semua Tipe</option>
                                    <option value="nik_change" {{ request('type') === 'nik_change' ? 'selected' : '' }}>Perubahan NIK</option>
                                    <option value="name_change" {{ request('type') === 'name_change' ? 'selected' : '' }}>Perubahan Nama</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                    <a href="{{ route('admin.profile-changes.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Requests Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Permintaan Perubahan</h5>
                    <div class="text-muted">
                        Total: {{ $changeRequests->total() }} permintaan
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($changeRequests->count() > 0)
                        <!-- Bulk Actions -->
                        <div class="p-3 border-bottom">
                            <form id="bulkActionForm" method="POST" action="{{ route('admin.profile-changes.bulk-action') }}">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                            <label class="form-check-label" for="selectAll">
                                                Pilih Semua
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="btn-group">
                                            <select name="action" class="form-select form-select-sm" style="width: auto;">
                                                <option value="">Pilih Aksi</option>
                                                <option value="approve">Setujui</option>
                                                <option value="reject">Tolak</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirmBulkAction()">
                                                Jalankan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <textarea name="bulk_admin_notes" class="form-control form-control-sm"
                                                  placeholder="Catatan admin (opsional)" rows="2"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input" id="selectAllTable">
                                        </th>
                                        <th>Pengguna</th>
                                        <th>Tipe Perubahan</th>
                                        <th>Perubahan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($changeRequests as $request)
                                    <tr>
                                        <td>
                                            @if($request->isPending())
                                            <input type="checkbox" class="form-check-input request-checkbox"
                                                   name="request_ids[]" value="{{ $request->id }}" form="bulkActionForm">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    {{ strtoupper(substr($request->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $request->user->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">NIK: {{ $request->user->nik ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $request->request_type_display }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="request-preview">
                                                @if($request->request_type === 'nik_change')
                                                    <div class="change-highlight">
                                                        <small class="text-muted">Dari:</small>
                                                        <code>{{ $request->old_data['nik'] ?? 'N/A' }}</code>
                                                    </div>
                                                    <div class="change-highlight">
                                                        <small class="text-muted">Ke:</small>
                                                        <code>{{ $request->new_data['nik'] ?? 'N/A' }}</code>
                                                    </div>
                                                @elseif($request->request_type === 'name_change')
                                                    <div class="change-highlight">
                                                        <small class="text-muted">Dari:</small>
                                                        {{ $request->old_data['name'] ?? 'N/A' }}
                                                    </div>
                                                    <div class="change-highlight">
                                                        <small class="text-muted">Ke:</small>
                                                        {{ $request->new_data['name'] ?? 'N/A' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $request->status_badge_class }}">
                                                {{ $request->status_display }}
                                            </span>
                                            @if($request->approvedBy)
                                                <br><small class="text-muted">oleh {{ $request->approvedBy->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $request->created_at->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.profile-changes.show', $request->id) }}"
                                                   class="btn btn-outline-primary btn-sm" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($request->isPending())
                                               <!-- Form untuk Approve -->
                                                <form method="POST" 
                                                    action="{{ route('admin.profile-changes.approve', $request->id) }}" 
                                                    style="display: inline;" 
                                                    onsubmit="return confirmApprove(this)">
                                                    @csrf
                                                    <input type="hidden" name="admin_notes" value="">
                                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Setujui">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                
                                                <!-- Form untuk Reject -->
                                                <form method="POST" action="{{ route('admin.profile-changes.reject', $request->id) }}" 
                                                      style="display: inline;" onsubmit="return confirmReject(this)">
                                                    @csrf
                                                    <input type="hidden" name="admin_notes" class="reject-notes-{{ $request->id }}">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Tolak">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($changeRequests->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Menampilkan {{ $changeRequests->firstItem() }} sampai {{ $changeRequests->lastItem() }}
                                    dari {{ $changeRequests->total() }} permintaan
                                </div>
                                <div>
                                    {{ $changeRequests->links() }}
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-edit fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada permintaan perubahan</h5>
                            <p class="text-muted mb-0">Belum ada permintaan perubahan profil yang masuk.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const selectAllTableCheckbox = document.getElementById('selectAllTable');
            const requestCheckboxes = document.querySelectorAll('.request-checkbox');

            function updateSelectAll() {
                const checkedCount = document.querySelectorAll('.request-checkbox:checked').length;
                const allChecked = checkedCount === requestCheckboxes.length && requestCheckboxes.length > 0;
                const someChecked = checkedCount > 0;

                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }

                if (selectAllTableCheckbox) {
                    selectAllTableCheckbox.checked = allChecked;
                    selectAllTableCheckbox.indeterminate = someChecked && !allChecked;
                }
            }

            function toggleAll(checked) {
                requestCheckboxes.forEach(checkbox => {
                    checkbox.checked = checked;
                });
                updateSelectAll();
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    toggleAll(this.checked);
                });
            }

            if (selectAllTableCheckbox) {
                selectAllTableCheckbox.addEventListener('change', function() {
                    toggleAll(this.checked);
                });
            }

            requestCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectAll);
            });

            updateSelectAll();
        });

        function confirmBulkAction() {
            const selectedRequests = document.querySelectorAll('.request-checkbox:checked');
            const action = document.querySelector('select[name="action"]').value;

            if (selectedRequests.length === 0) {
                alert('Pilih minimal satu permintaan untuk diproses.');
                return false;
            }

            if (!action) {
                alert('Pilih aksi yang ingin dilakukan.');
                return false;
            }

            const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
            return confirm(`Apakah Anda yakin ingin ${actionText} ${selectedRequests.length} permintaan?`);
        }

            function confirmApprove(form) {
                const result = confirm('Apakah Anda yakin ingin menyetujui permintaan ini?');
                if (result) {
                    const notes = prompt('Catatan admin (opsional):');
                    if (notes !== null) {
                        form.querySelector('input[name="admin_notes"]').value = notes;
                    }
                }
                return result;
        }

        function confirmReject(form) {
            const notes = prompt('Alasan penolakan (wajib diisi):');
            if (notes && notes.trim() !== '') {
                form.querySelector('input[name="admin_notes"]').value = notes;
                return confirm('Apakah Anda yakin ingin menolak permintaan ini?');
            } else {
                alert('Alasan penolakan wajib diisi!');
                return false;
            }
        }
    </script>
@endpush

@push('styles')
<style>
    .stats-card {
        transition: all 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    .stats-card i {
        color: #6c757d;
    }
    .user-avatar {
        width: 36px;
        height: 36px;
        background-color: #f0f0f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .change-highlight {
        margin-bottom: 4px;
    }
    .request-preview {
        max-width: 250px;
    }
</style>
@endpush