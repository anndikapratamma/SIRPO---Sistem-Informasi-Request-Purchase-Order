@extends('layouttempalte.master')

@section('title', 'Kelola Akun - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-users me-2"></i>Kelola Akun</h2>
                    <p class="text-muted mb-0">Manajemen akun pengguna sistem SIRPO</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Akun
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
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

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1">{{ $stats['total_users'] ?? 0 }}</h3>
                            <p class="mb-0">Total Pengguna</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user-shield fa-2x mb-2"></i>
                            <h3 class="mb-1">{{ $stats['admin_count'] ?? 0 }}</h3>
                            <p class="mb-0">Admin</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user fa-2x mb-2"></i>
                            <h3 class="mb-1">{{ $stats['user_count'] ?? 0 }}</h3>
                            <p class="mb-0">User</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <h3 class="mb-1">{{ $stats['recent_registrations'] ?? 0 }}</h3>
                            <p class="mb-0">Registrasi 7 Hari</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label small text-muted">Pencarian</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="search" name="search"
                                           value="{{ request('search') }}" placeholder="Cari nama, NIK, atau role...">
                                    {{-- <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i> --}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="role" class="form-label small text-muted">Role</label>
                                <select name="role" id="role" class="form-select">
                                    <option value="">Semua Role</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort_by" class="form-label small text-muted">Urutkan Berdasarkan</label>
                                <select name="sort_by" id="sort_by" class="form-select">
                                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Tanggal Daftar</option>
                                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Nama</option>
                                    <option value="role" {{ request('sort_by') === 'role' ? 'selected' : '' }}>Role</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="sort_order" class="form-label small text-muted">Urutan</label>
                                <select name="sort_order" id="sort_order" class="form-select">
                                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Terbaru</option>
                                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Terlama</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-primary btn-sm" title="Cari">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Pengguna</h5>
                    <div class="text-muted">
                        Total: {{ $users->total() }} pengguna
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($users->count() > 0)


                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Pengguna</th>
                                        <th>NIK</th>
                                        <th>Role</th>
                                        <th>Terdaftar</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($user->profile_photo)
                                                    <img src="{{ asset('storage/profile-photos/' . $user->profile_photo) }}"
                                                         alt="Profile"
                                                         style="width: 40px !important; height: 40px !important; border-radius: 50% !important; object-fit: cover !important; border: 2px solid #e9ecef !important;"
                                                         class="me-3">
                                                @else
                                                    <div class="user-avatar me-3"
                                                         style="width: 40px !important; height: 40px !important; border-radius: 50% !important; background: linear-gradient(45deg, #007bff, #0056b3) !important; display: flex !important; align-items: center !important; justify-content: center !important; color: white !important; font-weight: bold !important; font-size: 14px !important;">
                                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $user->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code>{{ $user->nik ?? 'N/A' }}</code>
                                        </td>
                                        <td>
                                            <span class="badge role-badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                                {{ ucfirst($user->role ?? 'user') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</div>
                                            <small class="text-muted">{{ $user->created_at ? $user->created_at->format('H:i') : '' }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.users.show', $user->id) }}"
                                                   class="btn btn-outline-primary btn-sm" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                   class="btn btn-outline-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                        title="Hapus" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($users->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }}
                                    dari {{ $users->total() }} pengguna
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        {{-- Previous Page Link --}}
                                        @if ($users->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">&laquo;</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev">&laquo;</a>
                                            </li>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                            @if ($page == $users->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        @if ($users->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next">&raquo;</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">&raquo;</span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada pengguna ditemukan</h5>
                            <p class="text-muted mb-3">Belum ada pengguna yang terdaftar atau sesuai dengan pencarian.</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Pengguna Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pengguna <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const selectAllTableCheckbox = document.getElementById('selectAllTable');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            const selectedCountSpan = document.getElementById('selectedCount');

            function updateSelectAll() {
                const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                const allChecked = checkedCount === userCheckboxes.length && userCheckboxes.length > 0;
                const someChecked = checkedCount > 0;

                // Update select all checkboxes
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }

                if (selectAllTableCheckbox) {
                    selectAllTableCheckbox.checked = allChecked;
                    selectAllTableCheckbox.indeterminate = someChecked && !allChecked;
                }

                // Update count display
                if (selectedCountSpan) {
                    selectedCountSpan.textContent = `(${checkedCount} dipilih)`;
                }
            }

            function toggleAll(checked) {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = checked;
                });
                updateSelectAll();
            }

            // Event listeners
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

            userCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectAll);
            });

            // Initialize
            updateSelectAll();

            console.log('User management page initialized successfully');
        });

        function confirmDelete(userId, userName) {
            console.log('confirmDelete called with:', userId, userName);

            try {
                // Set nama user di modal
                const userNameElement = document.getElementById('deleteUserName');
                if (userNameElement) {
                    userNameElement.textContent = userName;
                } else {
                    console.error('Element deleteUserName tidak ditemukan');
                    return;
                }

                // Set action form dengan route Laravel yang benar
                const deleteForm = document.getElementById('deleteForm');
                if (deleteForm) {
                    deleteForm.action = "{{ route('admin.users.destroy', ':id') }}".replace(':id', userId);
                    console.log('Form action set to:', deleteForm.action);
                } else {
                    console.error('Element deleteForm tidak ditemukan');
                    return;
                }

                // Tampilkan modal
                const modalElement = document.getElementById('deleteModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                    console.log('Modal shown successfully');
                } else {
                    console.error('Element deleteModal tidak ditemukan');
                }

            } catch (error) {
                console.error('Error in confirmDelete:', error);
                alert('Terjadi kesalahan saat membuka modal hapus: ' + error.message);
            }
        }

        function confirmBulkAction() {
            const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
            const action = document.querySelector('select[name="action"]').value;

            if (selectedUsers.length === 0) {
                alert('Pilih minimal satu pengguna untuk diproses.');
                return false;
            }

            if (!action) {
                alert('Pilih aksi yang ingin dilakukan.');
                return false;
            }

            const actionText = {
                'activate': 'mengaktifkan',
                'deactivate': 'menonaktifkan',
                'delete': 'menghapus'
            };

            return confirm(`Apakah Anda yakin ingin ${actionText[action]} ${selectedUsers.length} pengguna?`);
        }
    </script>
@endsection

@section('styles')
<style>
/* Custom pagination styling */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border: 1px solid #dee2e6;
    color: #6c757d;
    background-color: #fff;
    border: 1px solid #dee2e6;
    text-decoration: none;
}

.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Make pagination buttons smaller */
.pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Navigation arrows styling */
.pagination .page-link svg {
    width: 16px;
    height: 16px;
}

/* Filter form improvements */
.form-label.small {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

/* Search box icon positioning */
.position-relative .fa-search {
    pointer-events: none;
    z-index: 10;
}

/* Stats cards */
.stats-card {
    border-left: 4px solid #007bff;
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Table improvements */
.table th {
    border-top: none;
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
}

/* Checkbox styling */
.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.form-check-input:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Badge styling */
.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

/* User avatar styling */
.user-avatar-img {
    width: 40px !important;
    height: 40px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    border: 2px solid #e9ecef !important;
}

/* User avatar for initials - removed, using inline styles */

/* Button group improvements */
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Form spacing */
.row.g-3 > * {
    margin-bottom: 0;
}
</style>
@endsection
