@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Dashboard Admin</h3>
        <div>
            <span class="text-muted">{{ now()->format('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total PB</h6>
                            <h3 class="mb-0">{{ number_format($totalPbs ?? 0) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-light">
                        <i class="fas fa-arrow-up"></i>
                        {{ $weeklyTrend ? $weeklyTrend->sum('count') : 0 }} minggu ini
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Nominal</h6>
                            <h3 class="mb-0">{{ number_format(($totalAmount ?? 0) / 1000000, 1) }}M</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-light">
                        Rp {{ number_format($totalAmount ?? 0, 0, ',', '.') }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total User</h6>
                            <h3 class="mb-0">{{ $totalUsers ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-light">
                        Pengguna aktif
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Template</h6>
                            <h3 class="mb-0">{{ $totalTemplates ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-excel fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-light">
                        Template Excel
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Charts Column -->
        <div class="col-md-8">
            <!-- Monthly PB Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Statistik PB Bulanan ({{ now()->year }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="400" height="150"></canvas>
                </div>
            </div>

            <!-- Weekly Trend -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tren 7 Hari Terakhir</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Jumlah PB</th>
                                    <th class="text-end">Total Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($weeklyTrend && count($weeklyTrend) > 0)
                                    @foreach($weeklyTrend as $day)
                                    <tr>
                                        <td>{{ $day['day'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($day['date'])->format('d/m/Y') }}</td>
                                        <td class="text-end">{{ $day['count'] }}</td>
                                        <td class="text-end">Rp {{ number_format($day['total'], 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Tidak ada data tren mingguan</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-md-4">
            <!-- Filter & Sort -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filter & Urutkan Data</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-3">
                        <div class="mb-3">
                            <label for="sort_by" class="form-label">Urutkan berdasarkan:</label>
                            <select name="sort_by" id="sort_by" class="form-select">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                                <option value="nominal" {{ request('sort_by') == 'nominal' ? 'selected' : '' }}>Nominal</option>
                                <option value="nomor_pb" {{ request('sort_by') == 'nomor_pb' ? 'selected' : '' }}>Nomor PB</option>
                                <option value="tanggal" {{ request('sort_by') == 'tanggal' ? 'selected' : '' }}>Tanggal PB</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Urutan:</label>
                            <select name="sort_order" id="sort_order" class="form-select">
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Tertinggi ke Terendah</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terendah ke Tertinggi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="filter_divisi" class="form-label">Filter Divisi:</label>
                            <select name="filter_divisi" id="filter_divisi" class="form-select">
                                <option value="">Semua Divisi</option>
                                <option value="OP" {{ request('filter_divisi') == 'OP' ? 'selected' : '' }}>Operasional</option>
                                <option value="AKT" {{ request('filter_divisi') == 'AKT' ? 'selected' : '' }}>Akuntansi</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Terapkan Filter
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- PB Terbaru dengan Filter -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>PB Terbaru
                        @if(request('sort_by') || request('filter_divisi'))
                            <small class="text-muted">(Terfilter)</small>
                        @endif
                    </h5>
                    <a href="{{ route('pb.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($recentPbs) && count($recentPbs) > 0)
                        @foreach($recentPbs as $pb)
                        <div class="d-flex justify-content-between align-items-start mb-3 p-2 border rounded">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">{{ $pb->nomor_pb }}</span>
                                    <span class="badge bg-{{ $pb->divisi == 'OP' ? 'info' : 'success' }}">
                                        {{ strtoupper($pb->divisi ?? 'UMUM') }}
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <strong>{{ $pb->penginput ?? 'Unknown' }}</strong>
                                    <div class="text-muted small">{{ Str::limit($pb->keterangan ?? '', 50) }}</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="fw-bold text-success">
                                        Rp {{ number_format($pb->nominal ?? 0, 0, ',', '.') }}
                                    </span>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($pb->tanggal ?? $pb->created_at)->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <div>Tidak ada data PB</div>
                            @if(request('sort_by') || request('filter_divisi'))
                                <small>dengan filter yang dipilih</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Users -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Top User (PB Terbanyak)</h5>
                </div>
                <div class="card-body">
                    @if($topUsers && count($topUsers) > 0)
                        @foreach($topUsers as $index => $user)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                <strong>{{ $user->penginput }}</strong>
                            </div>
                            <div class="text-end">
                                <div>{{ $user->count }} PB</div>
                                <small class="text-muted">
                                    {{ number_format($user->total / 1000000, 1) }}M
                                </small>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> Belum ada data user
                        </div>
                    @endif
                </div>
            </div>

            <!-- Division Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Statistik per Divisi</h5>
                </div>
                <div class="card-body">
                    @if($divisionStats && count($divisionStats) > 0)
                        @foreach($divisionStats as $division)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-{{ $division->divisi == 'OP' ? 'primary' : 'success' }}">
                                    {{ strtoupper($division->divisi) }}
                                </span>
                            </div>
                            <div class="text-end">
                                <div><strong>{{ $division->count }}</strong> PB</div>
                                <small class="text-muted">
                                    Rp {{ number_format($division->total, 0, ',', '.') }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> Belum ada data divisi
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                    <a href="{{ route('admin.reports.user-activity') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($recentActivities && count($recentActivities) > 0)
                        @foreach($recentActivities as $activity)
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <i class="{{ $activity->getActionIcon() }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $activity->user->name ?? 'System' }}</div>
                                <div class="text-muted small">
                                    {{ $activity->getActionLabel() }}
                                    @if($activity->description)
                                        - {{ Str::limit($activity->description, 30) }}
                                    @endif
                                </div>
                                <div class="text-muted smaller">
                                    {{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> Belum ada aktivitas
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
                    <h5 class="mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <a href="{{ route('pb.create') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-plus"></i><br>Tambah PB
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('templates.create') }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-file-excel"></i><br>Upload Template
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.backup.create') }}" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-database"></i><br>Backup Sistem
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-chart-bar"></i><br>Generate Report
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('pb.export.excel') }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-download"></i><br>Export Excel
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary w-100 mb-2">
                                <i class="fas fa-cog"></i><br>Pengaturan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly PB Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = @json($monthlyPbs ?? []);

    const monthNames = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ];

    const labels = monthNames;
    const counts = new Array(12).fill(0);
    const amounts = new Array(12).fill(0);

    monthlyData.forEach(item => {
        counts[item.month - 1] = item.count;
        amounts[item.month - 1] = item.total / 1000000; // Convert to millions
    });

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah PB',
                data: counts,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Total Nominal (Juta)',
                data: amounts,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Jumlah PB'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Nominal (Juta)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
});
</script>
@endsection
