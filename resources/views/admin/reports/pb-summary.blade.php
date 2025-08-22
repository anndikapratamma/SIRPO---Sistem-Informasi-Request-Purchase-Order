<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PB Summary Report - SIRPO</title>
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
        .chart-container { position: relative; height: 400px; }
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
                            <h1 class="h3 text-dark">PB Summary Report</h1>
                            <p class="text-muted">Laporan ringkasan permintaan bayar</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url('admin/reports') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button class="btn btn-danger" onclick="exportPDF()">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </button>
                            <button class="btn btn-success" onclick="exportExcel()">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-filter me-2"></i>Filter Report
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ url('admin/reports/pb-summary') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Divisi</label>
                                        <select class="form-select" name="divisi">
                                            <option value="">Semua Divisi</option>
                                            <option value="OP" {{ request('divisi') == 'OP' ? 'selected' : '' }}>OP</option>
                                            <option value="ADM" {{ request('divisi') == 'ADM' ? 'selected' : '' }}>ADM</option>
                                            <option value="FIN" {{ request('divisi') == 'FIN' ? 'selected' : '' }}>FIN</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="">Semua Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-2"></i>Filter
                                        </button>
                                        <a href="{{ url('admin/reports/pb-summary') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-undo me-2"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
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
                                    <h5 class="card-title">Total PB</h5>
                                    <h2 class="mb-0">{{ $totalPbs ?? 0 }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-file-invoice fa-3x opacity-75"></i>
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
                                    <h5 class="card-title">Approved</h5>
                                    <h2 class="mb-0">{{ $approvedPbs ?? 0 }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-check-circle fa-3x opacity-75"></i>
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
                                    <h5 class="card-title">Pending</h5>
                                    <h2 class="mb-0">{{ $pendingPbs ?? 0 }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-clock fa-3x opacity-75"></i>
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
                                    <h5 class="card-title">Total Nilai</h5>
                                    <h2 class="mb-0">Rp {{ number_format($totalAmount ?? 0, 0, ',', '.') }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-money-bill-wave fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PB List Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Daftar PB
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($pbs) && $pbs->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nomor PB</th>
                                                <th>Tanggal</th>
                                                <th>Penginput</th>
                                                <th>Divisi</th>
                                                <th>Nominal</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pbs as $index => $pb)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $pb->nomor_pb ?? '-' }}</td>
                                                    <td>{{ $pb->tanggal ? \Carbon\Carbon::parse($pb->tanggal)->format('d/m/Y') : '-' }}</td>
                                                    <td>{{ $pb->penginput ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge {{ ($pb->divisi ?? '') === 'OP' ? 'bg-success' : 'bg-info' }}">
                                                            {{ $pb->divisi ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>Rp {{ number_format($pb->nominal ?? 0, 0, ',', '.') }}</td>
                                                    <td>
                                                        @php
                                                            $statusClass = match($pb->status ?? 'pending') {
                                                                'approved' => 'bg-success',
                                                                'rejected' => 'bg-danger',
                                                                default => 'bg-warning'
                                                            };
                                                            $statusText = match($pb->status ?? 'pending') {
                                                                'approved' => 'Disetujui',
                                                                'rejected' => 'Ditolak',
                                                                default => 'Pending'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('pb.show', $pb->id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if(method_exists($pbs, 'links'))
                                    {{ $pbs->links() }}
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data PB sesuai filter yang dipilih</p>
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
        function exportPDF() {
            const params = new URLSearchParams(window.location.search);
            params.set('format', 'pdf');
            window.open('{{ url("admin/reports/pb-summary") }}?' + params.toString(), '_blank');
        }

        function exportExcel() {
            const params = new URLSearchParams(window.location.search);
            params.set('format', 'excel');
            window.location.href = '{{ url("admin/reports/pb-summary") }}?' + params.toString();
        }
    </script>
</body>
</html>
