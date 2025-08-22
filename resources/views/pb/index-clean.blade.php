@extends('layouts.app')

@section('title', 'Daftar PB - SIRPO')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-dark">{{ auth()->user()->role === 'admin' ? 'Kelola' : 'My' }} Permintaan Bayar</h1>
            <p class="text-muted">Manage your payment requests efficiently</p>

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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <a href="{{ route('pb.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>Tambah PB
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('pb.export.excel') }}" class="btn btn-success w-100">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('pb.export.pdf') }}" class="btn btn-danger w-100">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </a>
                        </div>
                        @if(auth()->user()->role === 'admin')
                        <div class="col-md-2">
                            <div class="dropdown w-100">
                                <button class="btn btn-warning dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-chart-bar me-2"></i>Laporan
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('pb.laporan.bulanan') }}">
                                        <i class="fas fa-calendar-alt me-2"></i>Bulanan
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('pb.laporan.mingguan') }}">
                                        <i class="fas fa-calendar-week me-2"></i>Mingguan
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PB List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Daftar PB
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Filter and Search Form -->
                    <form method="GET" action="{{ route('pb.index') }}" class="mb-4" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <!-- Search Bar -->
                            <div class="col-md-4">
                                <label for="search" class="form-label">
                                    <i class="fas fa-search me-1"></i>Pencarian
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" name="search"
                                           value="{{ request('search') }}"
                                           placeholder="Cari nomor PB, keterangan, nominal, penginput...">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Tekan Enter untuk pencarian cepat</small>
                            </div>

                            <!-- Division Filter -->
                            <div class="col-md-2">
                                <label for="divisi" class="form-label">
                                    <i class="fas fa-building me-1"></i>Divisi
                                </label>
                                <select class="form-select" id="divisi" name="divisi">
                                    <option value="">Semua Divisi</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division }}"
                                                {{ request('divisi') == $division ? 'selected' : '' }}
                                                data-color="{{
                                                    $division == 'E-CHANNEL' ? 'info' :
                                                    ($division == 'TREASURY OPERASIONAL' ? 'warning' :
                                                    ($division == 'LAYANAN OPERASIONAL' ? 'success' : 'primary'))
                                                }}">
                                            {{ $division }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2">
                                <label for="status" class="form-label">
                                    <i class="fas fa-flag me-1"></i>Status
                                </label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                        <i class="fas fa-check"></i> Aktif
                                    </option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        <i class="fas fa-times"></i> Dibatalkan
                                    </option>
                                </select>
                            </div>

                            <!-- Date Filter -->
                            <div class="col-md-2">
                                <label for="date" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Tanggal
                                </label>
                                <input type="date" class="form-control" id="date" name="date"
                                       value="{{ request('date') }}">
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-md-2">
                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="fas fa-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('pb.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-refresh"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Filters (Collapsible) -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <button class="btn btn-link text-decoration-none p-0" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                                    <i class="fas fa-sliders-h me-1"></i>Filter Lanjutan
                                    <i class="fas fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <div class="collapse mt-3" id="advancedFilters">
                            <div class="card card-body bg-light">
                                <div class="row g-3">
                                    <!-- Date Range -->
                                    <div class="col-md-3">
                                        <label for="date_from" class="form-label">Dari Tanggal</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from"
                                               value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to"
                                               value="{{ request('date_to') }}">
                                    </div>

                                    <!-- Nominal Range -->
                                    <div class="col-md-3">
                                        <label for="nominal_min" class="form-label">Nominal Minimum</label>
                                        <input type="number" class="form-control" id="nominal_min" name="nominal_min"
                                               value="{{ request('nominal_min') }}" placeholder="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="nominal_max" class="form-label">Nominal Maximum</label>
                                        <input type="number" class="form-control" id="nominal_max" name="nominal_max"
                                               value="{{ request('nominal_max') }}" placeholder="Tidak terbatas">
                                    </div>
                                </div>

                                <!-- Quick Date Filters -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="form-label">Filter Cepat:</label>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm quick-filter"
                                                    data-filter="today">Hari Ini</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm quick-filter"
                                                    data-filter="week">Minggu Ini</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm quick-filter"
                                                    data-filter="month">Bulan Ini</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm quick-filter"
                                                    data-filter="quarter">3 Bulan Terakhir</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Results Summary -->
                    @if(request()->hasAny(['search', 'divisi', 'status', 'date', 'date_from', 'date_to', 'nominal_min', 'nominal_max']))
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                Menampilkan {{ $pbs->count() }} PB
                                @if(request('search'))
                                    untuk pencarian "<strong>{{ request('search') }}</strong>"
                                @endif
                                @if(request('divisi'))
                                    di divisi <strong>{{ request('divisi') }}</strong>
                                @endif
                                @if(request('status'))
                                    dengan status <strong>{{ request('status') == 'active' ? 'Aktif' : 'Dibatalkan' }}</strong>
                                @endif
                            </div>
                            <a href="{{ route('pb.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Hapus Filter
                            </a>
                        </div>
                    @endif

                    <!-- Statistics Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title mb-1">Total PB</h6>
                                            <h4 class="mb-0">{{ $summary['total'] ?? $pbs->count() }}</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-file-invoice fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title mb-1">PB Aktif</h6>
                                            <h4 class="mb-0">{{ $summary['active'] ?? $pbs->where('status', 'active')->count() }}</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title mb-1">PB Batal</h6>
                                            <h4 class="mb-0">{{ $summary['cancelled'] ?? $pbs->where('status', 'cancelled')->count() }}</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PB Table -->
                    @if($pbs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Nomor PB</th>
                                        <th>Tanggal</th>
                                        <th>Divisi</th>
                                        <th>Keterangan</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>File</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($pbs as $pb)
                                    <tr class="{{ $pb->isCancelled() ? 'text-decoration-line-through text-danger' : '' }}">
                                        <td>{{ $pb->nomor_pb }}</td>
                                        <td>{{ $pb->tanggal->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{
                                                $pb->divisi == 'E-CHANNEL' ? 'info' :
                                                ($pb->divisi == 'TREASURY OPERASIONAL' ? 'warning' :
                                                ($pb->divisi == 'LAYANAN OPERASIONAL' ? 'success' : 'primary'))
                                            }}">
                                                {{ $pb->divisi }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($pb->keterangan, 30) }}</td>
                                        <td>Rp {{ number_format($pb->nominal, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $pb->status == 'active' ? 'success' : 'danger' }}">
                                                {{ $pb->status == 'active' ? 'Aktif' : 'Dibatalkan' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($pb->file_path)
                                                <div class="d-flex align-items-center gap-2">
                                                <a href="{{ asset('storage/' . $pb->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($pb->canBeEditedBy(auth()->user()->role ?? 'user') && !$pb->isCancelled())
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            title="Hapus File"
                                                            onclick="deleteFile({{ $pb->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                                </div>
                                                <small class="text-muted d-block">{{ Str::limit($pb->file_name, 20) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('pb.show', $pb->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($pb->canBeEditedBy(auth()->user()->role ?? 'user'))
                                                    @if(!$pb->isCancelled())
                                                        <a href="{{ route('pb.edit', $pb->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                title="Batalkan PB"
                                                                onclick="cancelPb({{ $pb->id }}, '{{ $pb->nomor_pb }}'); console.log('Button clicked for PB {{ $pb->id }}');">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @else
                                                        @if(auth()->user()->role === 'admin')
                                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                                    title="Kembalikan PB" onclick="restorePb({{ $pb->id }}, '{{ $pb->nomor_pb }}')">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                @else
                                                    <small class="text-muted">Tidak dapat diubah</small>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data PB</h5>
                            <p class="text-muted">Mulai dengan menambahkan PB baru</p>
                            <a href="{{ route('pb.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah PB Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel PB Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Batalkan PB</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="cancelForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin membatalkan PB <strong id="cancelPbNumber"></strong>?</p>
                        <div class="mb-3">
                            <label for="cancel_reason" class="form-label">Alasan Pembatalan (Opsional)</label>
                            <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3"
                                    placeholder="Masukkan alasan pembatalan..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            PB yang dibatalkan akan ditandai dengan garis merah dan tidak dapat diubah kembali setelah lewat hari ini.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>Batalkan PB
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Restore PB Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kembalikan PB</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="restoreForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin mengembalikan PB <strong id="restorePbNumber"></strong>?</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            PB akan dikembalikan ke status aktif dan dapat digunakan kembali.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-undo me-2"></i>Kembalikan PB
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function cancelPb(pbId, pbNumber) {
        console.log('cancelPb called with:', pbId, pbNumber); // Debug log

        try {
            document.getElementById('cancelPbNumber').textContent = pbNumber;
            document.getElementById('cancelForm').action = `/pb/${pbId}/cancel`;

            const modalElement = document.getElementById('cancelModal');
            if (!modalElement) {
                console.error('Cancel modal not found!');
                return;
            }

            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } catch (error) {
            console.error('Error in cancelPb:', error);
            alert('Error: ' + error.message);
        }
    }

    function restorePb(pbId, pbNumber) {
        console.log('restorePb called with:', pbId, pbNumber); // Debug log

        try {
            document.getElementById('restorePbNumber').textContent = pbNumber;
            document.getElementById('restoreForm').action = `/pb/${pbId}/restore`;

            const modalElement = document.getElementById('restoreModal');
            if (!modalElement) {
                console.error('Restore modal not found!');
                return;
            }

            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } catch (error) {
            console.error('Error in restorePb:', error);
            alert('Error: ' + error.message);
        }
    }

    async function deleteFile(pbId) {
        if (!confirm('Apakah Anda yakin ingin menghapus file ini?')) {
            return;
        }

        try {
            const response = await fetch(`/pb/${pbId}/delete-file`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                // Reload page to reflect changes
                location.reload();
            } else {
                alert(result.message || 'Gagal menghapus file');
            }
        } catch (error) {
            console.error('Error deleting file:', error);
            alert('Terjadi kesalahan saat menghapus file');
        }
    }

    // Enhanced Filter and Search Functionality
    document.addEventListener('DOMContentLoaded', function() {
        console.log('PB Index page loaded');

        // Get form elements
        const filterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('search');
        const clearSearchBtn = document.getElementById('clearSearch');
        const divisiSelect = document.getElementById('divisi');
        const statusSelect = document.getElementById('status');
        const dateInput = document.getElementById('date');
        const quickFilters = document.querySelectorAll('.quick-filter');

        // Real-time search (debounced)
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    // Auto-submit form after 1 second of no typing
                    if (this.value.length >= 3 || this.value.length === 0) {
                        filterForm.submit();
                    }
                }, 1000);
            });

            // Submit on Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    filterForm.submit();
                }
            });
        }

        // Clear search functionality
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterForm.submit();
            });
        }

        // Auto-submit on filter changes
        [divisiSelect, statusSelect, dateInput].forEach(element => {
            if (element) {
                element.addEventListener('change', function() {
                    filterForm.submit();
                });
            }
        });

        // Quick date filters
        quickFilters.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;
                const today = new Date();
                let fromDate, toDate;

                switch(filter) {
                    case 'today':
                        fromDate = toDate = today.toISOString().split('T')[0];
                        break;
                    case 'week':
                        const weekStart = new Date(today);
                        weekStart.setDate(today.getDate() - today.getDay());
                        fromDate = weekStart.toISOString().split('T')[0];
                        toDate = today.toISOString().split('T')[0];
                        break;
                    case 'month':
                        fromDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                        toDate = today.toISOString().split('T')[0];
                        break;
                    case 'quarter':
                        const quarterStart = new Date(today);
                        quarterStart.setMonth(today.getMonth() - 3);
                        fromDate = quarterStart.toISOString().split('T')[0];
                        toDate = today.toISOString().split('T')[0];
                        break;
                }

                if (fromDate && toDate) {
                    document.getElementById('date_from').value = fromDate;
                    document.getElementById('date_to').value = toDate;

                    // Highlight active button
                    quickFilters.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Submit form
                    filterForm.submit();
                }
            });
        });

        // Export with current filters
        function exportWithFilters(format) {
            const params = new URLSearchParams();

            if (searchInput?.value) params.append('search', searchInput.value);
            if (divisiSelect?.value) params.append('divisi', divisiSelect.value);
            if (statusSelect?.value) params.append('status', statusSelect.value);
            if (dateInput?.value) params.append('date', dateInput.value);
            if (document.getElementById('date_from')?.value) params.append('date_from', document.getElementById('date_from').value);
            if (document.getElementById('date_to')?.value) params.append('date_to', document.getElementById('date_to').value);
            if (document.getElementById('nominal_min')?.value) params.append('nominal_min', document.getElementById('nominal_min').value);
            if (document.getElementById('nominal_max')?.value) params.append('nominal_max', document.getElementById('nominal_max').value);

            const baseUrl = format === 'excel' ? '/pb/export-excel' : '/pb/export-pdf';
            window.location.href = `${baseUrl}?${params.toString()}`;
        }

        // Update export links to include current filters
        const excelExportBtn = document.querySelector('a[href*="export-excel"]');
        const pdfExportBtn = document.querySelector('a[href*="export-pdf"]');

        if (excelExportBtn) {
            excelExportBtn.addEventListener('click', function(e) {
                e.preventDefault();
                exportWithFilters('excel');
            });
        }

        if (pdfExportBtn) {
            pdfExportBtn.addEventListener('click', function(e) {
                e.preventDefault();
                exportWithFilters('pdf');
            });
        }

        // Visual feedback for active filters
        function updateFilterIndicators() {
            const hasFilters = [searchInput, divisiSelect, statusSelect, dateInput].some(el => el?.value);
            const filterBtn = document.querySelector('button[type="submit"]');

            if (hasFilters && filterBtn) {
                filterBtn.classList.add('btn-warning');
                filterBtn.classList.remove('btn-primary');
                filterBtn.innerHTML = '<i class="fas fa-filter me-1"></i>Filter Aktif';
            } else if (filterBtn) {
                filterBtn.classList.add('btn-primary');
                filterBtn.classList.remove('btn-warning');
                filterBtn.innerHTML = '<i class="fas fa-search me-1"></i>Filter';
            }
        }

        // Update indicators on page load and filter changes
        updateFilterIndicators();
        [searchInput, divisiSelect, statusSelect, dateInput].forEach(element => {
            if (element) {
                element.addEventListener('change', updateFilterIndicators);
                element.addEventListener('input', updateFilterIndicators);
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);

        // Check if modals exist
        const cancelModal = document.getElementById('cancelModal');
        const restoreModal = document.getElementById('restoreModal');
        console.log('Cancel modal found:', !!cancelModal);
        console.log('Restore modal found:', !!restoreModal);
    });
</script>
@endsection
