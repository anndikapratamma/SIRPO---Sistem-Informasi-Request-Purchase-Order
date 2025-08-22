@extends('layouttempalte.master')

@section('title', 'Daftar PB - SIRPO')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-dark">Kelola Permintaan Bayar</h1>
            <p class="text-muted">Manage payment requests efficiently - All PBs are visible to all users</p>

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
                               <button class="btn btn-warning dropdown-toggle w-100" type="button"
                                        id="laporanDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                            <div class="col-lg-4 col-md-12">
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
                            <div class="col-lg-2 col-md-4 col-sm-6">
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
                            <div class="col-lg-2 col-md-3 col-sm-6">
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
                            <div class="col-lg-2 col-md-6">
                                <label for="date" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Tanggal
                                </label>
                                <input type="date" class="form-control" id="date" name="date"
                                       value="{{ request('date') }}">
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-lg-3 col-md-6">
                                <div class="d-flex gap-2">
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
                    <div class="row mb-4 g-3">
                        <div class="col-lg-4 col-md-6">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body p-3 d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">Total PB</h6>
                                        <h4 class="mb-0">{{ $summary['total'] ?? $pbs->count() }}</h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-file-invoice fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body p-3 d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">PB Aktif</h6>
                                        <h4 class="mb-0">{{ $summary['active'] ?? $pbs->where('status', 'active')->count() }}</h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body p-3 d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">PB Batal</h6>
                                        <h4 class="mb-0">{{ $summary['cancelled'] ?? $pbs->where('status', 'cancelled')->count() }}</h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-times-circle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PB Table -->
                    @if($pbs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center" style="width: 12%;">Nomor PB</th>
                                        <th class="text-center" style="width: 10%;">Tanggal</th>
                                        <th class="text-center" style="width: 15%;">Divisi</th>
                                        <th class="text-center" style="width: 20%;">Keterangan</th>
                                        <th class="text-center" style="width: 13%;">Nominal</th>
                                        <th class="text-center" style="width: 15%;">Penginput</th>
                                        <th class="text-center" style="width: 10%;">File</th>
                                        <th class="text-center" style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($pbs as $index => $pb)
                                    <tr class="{{ $pb->isCancelled() ? 'table-danger' : '' }}">
                                        <td class="text-center">
                                            <span class="pb-number {{ $pb->isCancelled() ? 'pb-number-cancelled' : '' }}">
                                                {{ $pb->nomor_pb }}
                                            </span>
                                            @if($pb->isCancelled())
                                                <br><small class="text-danger fw-bold">(DIBATALKAN)</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $pb->tanggal->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{
                                                $pb->divisi == 'E-CHANNEL' ? 'info' :
                                                ($pb->divisi == 'TREASURY OPERASIONAL' ? 'warning' :
                                                ($pb->divisi == 'LAYANAN OPERASIONAL' ? 'success' : 'primary'))
                                            }}">
                                                {{ $pb->divisi }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($pb->keterangan, 30) }}</td>
                                        <td class="text-end">
                                            Rp{{ number_format($pb->nominal / 100, 2, '.', ',') }}
                                        </td>

                                        <td class="text-center">
                                            <span class="">
                                                {{ $pb->penginput ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($pb->file_path)
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="{{ asset('storage/' . $pb->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Download">
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
                                                <small class="text-muted d-block mt-1">{{ Str::limit($pb->file_name, 15) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
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

    <!-- Custom Dialog Overlay -->
    <div id="customDialogOverlay" class="custom-dialog-overlay" style="display: none;">
        <div class="custom-dialog-container">
            <div class="custom-dialog-content">
                <div class="custom-dialog-header">
                    <div class="custom-dialog-icon">
                        <i id="dialogIcon" class="fas fa-question-circle"></i>
                    </div>
                    <h4 id="dialogTitle" class="custom-dialog-title">Konfirmasi</h4>
                    <button type="button" class="custom-dialog-close" onclick="closeCustomDialog()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="custom-dialog-body">
                    <p id="dialogMessage" class="custom-dialog-message">Apakah Anda yakin?</p>
                    <div id="dialogInput" class="custom-dialog-input" style="display: none;">
                        <label for="reasonInput" class="custom-input-label">Alasan Pembatalan (Opsional)</label>
                        <textarea id="reasonInput" class="custom-textarea" rows="3" placeholder="Masukkan alasan pembatalan..."></textarea>
                    </div>
                    <div id="dialogWarning" class="custom-dialog-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>PB yang dibatalkan akan ditandai dengan garis merah dan tidak dapat diubah kembali setelah lewat hari ini.</span>
                    </div>
                </div>
                <div class="custom-dialog-footer">
                    <button type="button" class="custom-btn custom-btn-secondary" onclick="closeCustomDialog()">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="button" id="dialogConfirmBtn" class="custom-btn custom-btn-danger" onclick="confirmAction()">
                        <i class="fas fa-check mr-2"></i>Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Dialog Styles -->
    <style>
        .custom-dialog-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .custom-dialog-overlay.show {
            opacity: 1;
        }

        .custom-dialog-container {
            transform: scale(0.7);
            transition: transform 0.3s ease;
        }

        .custom-dialog-overlay.show .custom-dialog-container {
            transform: scale(1);
        }

        .custom-dialog-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 500px;
            max-width: 90vw;
            max-height: 90vh;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }

        .custom-dialog-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .custom-dialog-icon {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }

        .custom-dialog-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            flex: 1;
        }

        .custom-dialog-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .custom-dialog-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .custom-dialog-body {
            padding: 25px;
        }

        .custom-dialog-message {
            font-size: 16px;
            color: #495057;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .custom-dialog-input {
            margin-top: 20px;
        }

        .custom-input-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
            font-size: 14px;
        }

        .custom-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            resize: vertical;
            transition: border-color 0.2s ease;
            font-family: inherit;
        }

        .custom-textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .custom-dialog-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            color: #856404;
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .custom-dialog-warning i {
            margin-right: 10px;
            color: #f39c12;
        }

        .custom-dialog-footer {
            padding: 20px 25px;
            background: #f8f9fa;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid #e9ecef;
        }

        .custom-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .custom-btn-secondary {
            background: #6c757d;
            color: white;
        }

        .custom-btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .custom-btn-danger {
            background: #dc3545;
            color: white;
        }

        .custom-btn-danger:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .custom-btn-success {
            background: #28a745;
            color: white;
        }

        .custom-btn-success:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .custom-btn i {
            margin-right: 6px;
        }

        /* Animation for showing/hiding */
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.7);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeOutScale {
            from {
                opacity: 1;
                transform: scale(1);
            }
            to {
                opacity: 0;
                transform: scale(0.7);
            }
        }

        /* Responsive design */
        @media (max-width: 576px) {
            .custom-dialog-content {
                width: 95vw;
                margin: 10px;
            }

            .custom-dialog-header {
                padding: 15px;
            }

            .custom-dialog-body {
                padding: 20px;
            }

            .custom-dialog-footer {
                padding: 15px 20px;
                flex-direction: column;
            }

            .custom-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</div>
@endsection

@section('styles')
<style>
    /* Styling untuk PB yang dibatalkan */
    .table-danger {
        background-color: #f8d7da !important;
        border-color: #f5c2c7 !important;
    }

    .table-danger td {
        background-color: #f8d7da !important;
        border-color: #f5c2c7 !important;
    }

    .table-danger:hover {
        background-color: #f1aeb5 !important;
    }

    .table-danger:hover td {
        background-color: #f1aeb5 !important;
    }

    /* Styling untuk nomor PB berurutan */
    .pb-number {
        font-weight: bold;
        font-size: 1.1em;
        color: #2563eb;
    }

    .pb-number-cancelled {
        color: #dc2626 !important;
    }

    /* Dashboard cards */
    .dashboard-card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
    }

    /* Table improvements */
    .table-striped > tbody > tr:nth-of-type(odd) > td {
        background-color: rgba(0,0,0,.02);
    }

    .table th {
        font-weight: 600;
        font-size: 0.9rem;
        white-space: nowrap;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    /* Responsive table enhancements */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.8rem;
        }

        .btn-group .btn {
            padding: 0.25rem 0.4rem;
        }

        .badge {
            font-size: 0.7rem;
        }
    }

    /* Form styling */
    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Card enhancements */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        border: none;
    }

    /* Statistics cards */
    .card.bg-primary, .card.bg-success, .card.bg-danger {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: transform 0.2s ease;
    }

    .card.bg-primary:hover, .card.bg-success:hover, .card.bg-danger:hover {
        transform: translateY(-2px);
    }

    /* Modal z-index fixes - PERBAIKAN MODAL */
    .modal {
        z-index: 1050 !important;
    }

    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal-dialog {
        z-index: 1060 !important;
        margin: 1.75rem auto;
    }

    .modal-content {
        position: relative;
        z-index: 1070 !important;
        border: none;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    /* Ensure modal shows properly */
    .modal.show {
        display: block !important;
    }

    .modal.show .modal-dialog {
        transform: none !important;
    }

    /* Modal backdrop fix */
    .modal-backdrop.show {
        opacity: 0.5;
    }

    /* Modal header styling */
    .modal-header {
        border-bottom: 1px solid #dee2e6;
        border-radius: 10px 10px 0 0;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
        border-radius: 0 0 10px 10px;
    }
</style>
@endsection

@section('scripts')
<script>
    // Custom Dialog Variables
    let currentAction = null;
    let currentPbId = null;
    let currentPbNumber = null;

    // Show Custom Dialog
    function showCustomDialog(options) {
        const overlay = document.getElementById('customDialogOverlay');
        const icon = document.getElementById('dialogIcon');
        const title = document.getElementById('dialogTitle');
        const message = document.getElementById('dialogMessage');
        const input = document.getElementById('dialogInput');
        const warning = document.getElementById('dialogWarning');
        const confirmBtn = document.getElementById('dialogConfirmBtn');

        // Set dialog content
        icon.className = options.icon || 'fas fa-question-circle';
        title.textContent = options.title || 'Konfirmasi';
        message.innerHTML = options.message || 'Apakah Anda yakin?';

        // Show/hide input
        if (options.showInput) {
            input.style.display = 'block';
            document.getElementById('reasonInput').value = '';
        } else {
            input.style.display = 'none';
        }

        // Show/hide warning
        if (options.showWarning) {
            warning.style.display = 'flex';
        } else {
            warning.style.display = 'none';
        }

        // Set confirm button
        confirmBtn.innerHTML = options.confirmButton || '<i class="fas fa-check mr-2"></i>Konfirmasi';
        confirmBtn.className = `custom-btn ${options.confirmClass || 'custom-btn-danger'}`;

        // Store current action
        currentAction = options.action;
        currentPbId = options.pbId;
        currentPbNumber = options.pbNumber;

        // Show dialog with animation
        overlay.style.display = 'flex';
        setTimeout(() => {
            overlay.classList.add('show');
        }, 10);

        // Auto focus on textarea if shown
        if (options.showInput) {
            setTimeout(() => {
                document.getElementById('reasonInput').focus();
            }, 300);
        }
    }

    // Close Custom Dialog
    function closeCustomDialog() {
        const overlay = document.getElementById('customDialogOverlay');
        overlay.classList.remove('show');
        setTimeout(() => {
            overlay.style.display = 'none';
            currentAction = null;
            currentPbId = null;
            currentPbNumber = null;
        }, 300);
    }

    // Confirm Action
    function confirmAction() {
        if (currentAction === 'cancel') {
            const reason = document.getElementById('reasonInput').value;
            cancelPbConfirmed(currentPbId, reason);
        } else if (currentAction === 'restore') {
            restorePbConfirmed(currentPbId);
        }
        closeCustomDialog();
    }

    // Cancel PB Function (Updated to use custom dialog)
    function cancelPb(pbId, pbNumber) {
        console.log('cancelPb called with:', pbId, pbNumber);

        showCustomDialog({
            icon: 'fas fa-exclamation-triangle',
            title: 'Batalkan PB',
            message: `Apakah Anda yakin ingin membatalkan PB <strong>${pbNumber}</strong>?`,
            showInput: true,
            showWarning: true,
            confirmButton: '<i class="fas fa-times mr-2"></i>Batalkan PB',
            confirmClass: 'custom-btn-danger',
            action: 'cancel',
            pbId: pbId,
            pbNumber: pbNumber
        });
    }

    // Cancel PB Confirmed
    function cancelPbConfirmed(pbId, reason) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pb/${pbId}/cancel`;

        // CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);

        // Method PATCH
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);

        // Reason
        if (reason && reason.trim()) {
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'cancel_reason';
            reasonInput.value = reason.trim();
            form.appendChild(reasonInput);
        }

        document.body.appendChild(form);
        form.submit();
    }

    // Restore PB Function (Updated to use custom dialog)
    function restorePb(pbId, pbNumber) {
        console.log('restorePb called with:', pbId, pbNumber);

        showCustomDialog({
            icon: 'fas fa-undo',
            title: 'Kembalikan PB',
            message: `Apakah Anda yakin ingin mengembalikan PB <strong>${pbNumber}</strong>?<br><br><small class="text-info"><i class="fas fa-info-circle mr-1"></i>PB akan dikembalikan ke status aktif dan dapat digunakan kembali.</small>`,
            showInput: false,
            showWarning: false,
            confirmButton: '<i class="fas fa-undo mr-2"></i>Kembalikan PB',
            confirmClass: 'custom-btn-success',
            action: 'restore',
            pbId: pbId,
            pbNumber: pbNumber
        });
    }

    // Restore PB Confirmed
    function restorePbConfirmed(pbId) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pb/${pbId}/restore`;

        // CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);

        // Method PATCH
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
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

    // Close dialog when clicking outside
    document.addEventListener('click', function(e) {
        const overlay = document.getElementById('customDialogOverlay');
        if (e.target === overlay) {
            closeCustomDialog();
        }
    });

    // Close dialog with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('customDialogOverlay').style.display === 'flex') {
            closeCustomDialog();
        }
    });

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

        // Save filter state to localStorage
        function saveFilterState() {
            const state = {
                search: searchInput?.value || '',
                divisi: divisiSelect?.value || '',
                status: statusSelect?.value || '',
                date: dateInput?.value || ''
            };
            localStorage.setItem('pbFilterState', JSON.stringify(state));
        }

        // Load filter state from localStorage
        function loadFilterState() {
            try {
                const state = JSON.parse(localStorage.getItem('pbFilterState') || '{}');
                if (state.search && searchInput) searchInput.value = state.search;
                if (state.divisi && divisiSelect) divisiSelect.value = state.divisi;
                if (state.status && statusSelect) statusSelect.value = state.status;
                if (state.date && dateInput) dateInput.value = state.date;
            } catch (e) {
                console.log('No previous filter state found');
            }
        }

        // Save state on form submit
        if (filterForm) {
            filterForm.addEventListener('submit', saveFilterState);
        }

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
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    bootstrap.Alert.getOrCreateInstance(alert).close();
                }
            });
        }, 5000);

        // Check if modals exist and setup event listeners
        const cancelModal = document.getElementById('cancelModal');
        const restoreModal = document.getElementById('restoreModal');
        console.log('Cancel modal found:', !!cancelModal);
        console.log('Restore modal found:', !!restoreModal);

        // Modal cleanup event listeners
        if (cancelModal) {
            $(cancelModal).on('hidden.bs.modal', function () {
                console.log('Cancel modal hidden');
                // Clear form data
                const form = document.getElementById('cancelForm');
                if (form) {
                    form.reset();
                }
            });
        }

        if (restoreModal) {
            $(restoreModal).on('hidden.bs.modal', function () {
                console.log('Restore modal hidden');
                // Clear form data
                const form = document.getElementById('restoreForm');
                if (form) {
                    form.reset();
                }
            });
        }

        // Additional modal debugging
        $('.modal').on('show.bs.modal', function(e) {
            console.log('Modal showing:', e.target.id);
        });

        $('.modal').on('shown.bs.modal', function(e) {
            console.log('Modal shown:', e.target.id);
        });
    });
</script>

    <!-- Bootstrap JS sudah di-load di master template -->

    <script>
        // Basic notification functions (simplified)
        function updateNotificationCount() {
            // Placeholder for notification functionality
            console.log('Notification system ready');
        }

        // Mobile navbar initialization
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Auto-close mobile navbar when clicking on links
                const mobileNavLinks = document.querySelectorAll('#mobileNav .nav-link');
                mobileNavLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        const mobileNav = document.getElementById('mobileNav');
                        if (mobileNav && mobileNav.classList.contains('show')) {
                            const bsCollapse = bootstrap.Collapse.getInstance(mobileNav);
                            if (bsCollapse) {
                                bsCollapse.hide();
                            }
                        }
                    });
                });

                console.log('SIRPO Layout initialized successfully');
            } catch (error) {
                console.log('Error initializing app features:', error);
            }
        });
    </script>
</body>
</html>
