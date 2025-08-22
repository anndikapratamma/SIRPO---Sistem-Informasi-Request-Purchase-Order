@extends('layouttempalte.master')

@section('title', 'Dashboard Admin - SIRPO')

@section('content')


    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 text-dark">My PB</h1>
                <p class="text-muted">Selamat datang, {{ $user->name }}! Berikut ringkasan sistem SIRPO.</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">Total Users</h5>
                                <h2 class="mb-0">{{ number_format($totalUsers) }}</h2>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">Total PB</h5>
                                <h2 class="mb-0">{{ number_format($totalPbs) }}</h2>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">Templates</h5>
                                <h2 class="mb-0">{{ number_format($totalTemplates) }}</h2>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">Total Amount</h5>
                                <h4 class="mb-0 total-amount-text">Rp {{ number_format($totalAmount) }}</h4>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent PBs with Filter -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>PB Terbaru
                            @if(request('sort_by') || request('filter_divisi'))
                                <small class="text-muted ms-2">
                                    ({{ request('filter_divisi') ? 'Divisi: ' . request('filter_divisi') : '' }}
                                    {{ request('sort_by') ? ' | Urut: ' . ucfirst(str_replace('_', ' ', request('sort_by'))) : '' }})
                                </small>
                            @endif
                        </h5>
                    </div>

                    <!-- Filter Section -->
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('dashboard') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="sort_by" class="form-label">Urutkan Berdasarkan</label>
                                <select name="sort_by" id="sort_by" class="form-select">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                                    <option value="nominal" {{ request('sort_by') == 'nominal' ? 'selected' : '' }}>Nominal</option>
                                    <option value="nomor_pb" {{ request('sort_by') == 'nomor_pb' ? 'selected' : '' }}>Nomor PB</option>
                                    <option value="tanggal" {{ request('sort_by') == 'tanggal' ? 'selected' : '' }}>Tanggal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort_order" class="form-label">Urutan</label>
                                <select name="sort_order" id="sort_order" class="form-select">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Tertinggi ke Terendah</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terendah ke Tertinggi</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_divisi" class="form-label">Filter Divisi</label>
                                <select name="filter_divisi" id="filter_divisi" class="form-select">
                                    <option value="">Semua Divisi</option>
                                    <option value="OP" {{ request('filter_divisi') == 'OP' ? 'selected' : '' }}>OP</option>
                                    <option value="AKT" {{ request('filter_divisi') == 'AKT' ? 'selected' : '' }}>AKT</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- PB Data Display -->
                    <div class="card-body">
                        @if($recentPbs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No PB</th>
                                            <th>Pemohon</th>
                                            <th>Divisi</th>
                                            <th>Nominal</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentPbs as $index => $pb)
                                        <tr class="{{ $pb->isCancelled() ? 'table-danger' : '' }}">
                                            <td>
                                                <span class="pb-number {{ $pb->isCancelled() ? 'pb-number-cancelled' : '' }}">
                                                    {{ $pb->nomor_pb }}
                                                </span>
                                                @if($pb->isCancelled())
                                                    <br><small class="text-danger fw-bold">(DIBATALKAN)</small>
                                                @endif
                                            </td>
                                            <td>{{ $pb->user->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $pb->divisi == 'OP' ? 'info' : 'warning' }}">
                                                    {{ $pb->divisi ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-success">Rp {{ number_format($pb->nominal, 0, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $pb->status == 'approved' ? 'success' : ($pb->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($pb->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <small class="text-muted">Dibuat:</small><br>
                                                    {{ $pb->created_at->format('d/m/Y H:i') }}
                                                    @if($pb->tanggal)
                                                        <br><small class="text-muted">Tanggal PB:</small><br>
                                                        {{ date('d/m/Y', strtotime($pb->tanggal)) }}
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    Menampilkan 5 PB terbaru
                                    @if(request('filter_divisi'))
                                        untuk divisi {{ request('filter_divisi') }}
                                    @endif
                                    @if(request('sort_by'))
                                        diurutkan berdasarkan {{ ucfirst(str_replace('_', ' ', request('sort_by'))) }}
                                    @endif
                                </small>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">
                                    @if(request('filter_divisi'))
                                        Tidak ada PB untuk divisi {{ request('filter_divisi') }}.
                                    @else
                                        Belum ada PB yang dibuat.
                                    @endif
                                </p>
                                @if(request()->anyFilled(['sort_by', 'sort_order', 'filter_divisi']))
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-times me-1"></i>Reset Filter
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('pb.index') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-list me-2"></i>Lihat Semua PB
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('admin.profile-changes.index') }}" class="btn btn-warning w-100">
                                    <i class="fas fa-user-edit me-2"></i>Approval Profil
                                    @if(isset($pendingProfileChanges) && $pendingProfileChanges > 0)
                                        <span class="badge bg-danger ms-1">{{ $pendingProfileChanges }}</span>
                                    @endif
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('templates.index') }}" class="btn btn-success w-100">
                                    <i class="fas fa-file-alt me-2"></i>Kelola Template
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('admin.reports.index') }}" class="btn btn-info w-100">
                                    <i class="fas fa-chart-bar me-2"></i>Laporan
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-cog me-2"></i>Pengaturan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto submit form when sort options change
        document.getElementById('sort_by').addEventListener('change', function() {
            this.form.submit();
        });

        document.getElementById('sort_order').addEventListener('change', function() {
            this.form.submit();
        });

        document.getElementById('filter_divisi').addEventListener('change', function() {
            this.form.submit();
        });

        // Show loading indicator
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memfilter...';
            submitBtn.disabled = true;

            // Re-enable after 3 seconds to prevent stuck state
            setTimeout(() => {
                submitBtn.innerHTML = originalHtml;
                submitBtn.disabled = false;
            }, 3000);
        });
    </script>

    <style>
        /* Custom styling for Total Amount card */
        .total-amount-text {
            font-size: 1.3rem !important;
            line-height: 1.2 !important;
            word-break: break-word !important;
            white-space: normal !important;
            font-weight: 600 !important;
        }

        /* Ensure stat cards have consistent height */
        .stat-card {
            height: 120px !important;
        }

        .stat-card .card-body {
            padding: 1rem !important;
            display: flex !important;
            align-items: center !important;
        }

        /* Make card text responsive */
        @media (max-width: 768px) {
            .total-amount-text {
                font-size: 1.1rem !important;
            }
        }

        @media (max-width: 576px) {
            .total-amount-text {
                font-size: 1rem !important;
            }
        }
    </style>
@endsection
