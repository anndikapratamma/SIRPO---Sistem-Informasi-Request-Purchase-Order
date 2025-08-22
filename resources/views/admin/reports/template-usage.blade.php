<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Usage Report - SIRPO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #0d6efd 0%, #0b5ed7 100%);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        .main-content {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            .main-content {
                margin-left: 250px;
            }
        }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar position-fixed top-0 start-0 d-none d-md-block" style="width: 250px; z-index: 1000;">
        <div class="p-3">
            <h4 class="text-white mb-4">
                <i class="fas fa-file-invoice-dollar me-2"></i>SIRPO
            </h4>
            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
                <a class="nav-link" href="{{ route('pb.index') }}">
                    <i class="fas fa-file-invoice"></i>Kelola PB
                </a>
                <a class="nav-link" href="{{ route('templates.index') }}">
                    <i class="fas fa-file-alt"></i>Templates
                </a>
                <a class="nav-link" href="{{ route('admin.backup.index') }}">
                    <i class="fas fa-database"></i>Backup
                </a>
                <a class="nav-link active" href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-chart-bar"></i>Reports
                </a>
                <a class="nav-link" href="{{ route('notifications.index') }}">
                    <i class="fas fa-bell"></i>Notifikasi
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 text-dark">Template Usage Report</h1>
                            <p class="text-muted">Laporan penggunaan template Excel</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url('admin/reports') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button class="btn btn-success" onclick="exportExcel()">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Templates</h5>
                                    <h2 class="mb-0">{{ $totalTemplates ?? 0 }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-file-excel fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Downloads</h5>
                                    <h2 class="mb-0">{{ $totalDownloads ?? 0 }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-download fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">This Month</h5>
                                    <h2 class="mb-0">{{ $monthlyDownloads ?? 0 }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-calendar fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Most Popular</h5>
                                    <h6 class="mb-0">{{ $mostPopular ?? 'N/A' }}</h6>
                                </div>
                                <div>
                                    <i class="fas fa-star fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Templates List -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-file-excel me-2"></i>Template Statistics
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($templates) && $templates->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Template Name</th>
                                                <th>File Size</th>
                                                <th>Upload Date</th>
                                                <th>Downloads</th>
                                                <th>Last Downloaded</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($templates as $template)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-file-excel text-success me-2"></i>
                                                            <div>
                                                                <strong>{{ $template->name ?? 'Untitled' }}</strong><br>
                                                                <small class="text-muted">{{ $template->original_name ?? '' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $template->file_size ? number_format($template->file_size / 1024, 1) . ' KB' : '-' }}</td>
                                                    <td>{{ $template->created_at ? $template->created_at->format('d/m/Y') : '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $template->download_count ?? 0 }}</span>
                                                    </td>
                                                    <td>{{ $template->last_downloaded ? \Carbon\Carbon::parse($template->last_downloaded)->diffForHumans() : 'Never' }}</td>
                                                    <td>
                                                        <span class="badge bg-success">Active</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-excel fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada template yang tersedia</p>
                                    <a href="{{ route('templates.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Upload Template
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportExcel() {
            window.location.href = '{{ url("admin/reports/template-usage") }}?format=excel';
        }
    </script>
</body>
</html>
