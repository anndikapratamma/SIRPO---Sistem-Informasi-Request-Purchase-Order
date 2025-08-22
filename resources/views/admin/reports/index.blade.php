@extends('layouttempalte.master')

@section('title', 'Reports & Analytics - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 text-dark">Reports & Analytics</h1>
                    <p class="text-muted">Laporan dan analisis sistem SIRPO</p>
                </div>
            </div>

            <!-- Report Cards -->
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card report-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-file-invoice-dollar fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">PB Summary Report</h5>
                            <p class="card-text text-muted">
                                Ringkasan data permintaan bayar berdasarkan periode dan divisi
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.reports.pb-summary') }}" class="btn btn-primary">
                                    <i class="fas fa-chart-pie me-2"></i>Lihat Report
                                </a>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.reports.export-pb-summary-pdf') }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </a>
                                    <a href="{{ route('admin.reports.export-pb-summary-excel') }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-excel me-1"></i>Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card report-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">User Activity Report</h5>
                            <p class="card-text text-muted">
                                Aktivitas user dalam sistem, login terakhir, dan statistik penggunaan
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.reports.user-activity') }}" class="btn btn-success">
                                    <i class="fas fa-chart-line me-2"></i>Lihat Report
                                </a>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.reports.export-user-activity-pdf') }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </a>
                                    <a href="{{ route('admin.reports.export-user-activity-excel') }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-excel me-1"></i>Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card report-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-file-excel fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title">Template Usage Report</h5>
                            <p class="card-text text-muted">
                                Statistik penggunaan template Excel dan download count
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.reports.template-usage') }}" class="btn btn-info">
                                    <i class="fas fa-chart-bar me-2"></i>Lihat Report
                                </a>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.reports.export-template-usage-pdf') }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </a>
                                    <a href="{{ route('admin.reports.export-template-usage-excel') }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-excel me-1"></i>Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>Statistik Cepat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-file-invoice fa-2x text-primary mb-2"></i>
                                        <h4 class="mb-1">{{ $totalPbs ?? 0 }}</h4>
                                        <small class="text-muted">Total PB</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-users fa-2x text-success mb-2"></i>
                                        <h4 class="mb-1">{{ $totalUsers ?? 0 }}</h4>
                                        <small class="text-muted">Total Users</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-file-excel fa-2x text-info mb-2"></i>
                                        <h4 class="mb-1">{{ $totalTemplates ?? 0 }}</h4>
                                        <small class="text-muted">Total Templates</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
                                        <h4 class="mb-1">Rp {{ number_format($totalAmount ?? 0, 0, ',', '.') }}</h4>
                                        <small class="text-muted">Total Nominal</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Filters -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-filter me-2"></i>Filter Laporan
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="reportFilter" class="row g-3">
                                <div class="col-md-3">
                                    <label for="date_from" class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_to" class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to">
                                </div>
                                <div class="col-md-3">
                                    <label for="report_type" class="form-label">Jenis Laporan</label>
                                    <select class="form-select" id="report_type" name="report_type">
                                        <option value="">Semua Laporan</option>
                                        <option value="pb-summary">PB Summary</option>
                                        <option value="user-activity">User Activity</option>
                                        <option value="template-usage">Template Usage</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" onclick="applyFilter()">
                                        <i class="fas fa-search me-2"></i>Terapkan Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Aktivitas Terbaru
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($recentActivities) && $recentActivities->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($recentActivities as $activity)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $activity->description ?? 'Aktivitas' }}</h6>
                                                    <small class="text-muted">
                                                        oleh {{ $activity->user->name ?? 'System' }}
                                                    </small>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $activity->created_at ? $activity->created_at->diffForHumans() : 'Baru saja' }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center">Belum ada aktivitas tercatat</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function applyFilter() {
            const form = document.getElementById('reportFilter');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            const reportType = formData.get('report_type');

            if (reportType) {
                // Redirect to specific report with filters
                let url = '';
                switch(reportType) {
                    case 'pb-summary':
                        url = '{{ route("admin.reports.pb-summary") }}';
                        break;
                    case 'user-activity':
                        url = '{{ route("admin.reports.user-activity") }}';
                        break;
                    case 'template-usage':
                        url = '{{ route("admin.reports.template-usage") }}';
                        break;
                }

                if (url) {
                    window.location.href = url + '?' + params.toString();
                }
            } else {
                alert('Silakan pilih jenis laporan terlebih dahulu');
            }
        }

        // Set default dates (last 30 days)
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const lastMonth = new Date();
            lastMonth.setDate(today.getDate() - 30);

            document.getElementById('date_to').value = today.toISOString().split('T')[0];
            document.getElementById('date_from').value = lastMonth.toISOString().split('T')[0];
        });
    </script>
@endsection

@section('styles')
    <style>
        .report-card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
    </style>
@endsection
