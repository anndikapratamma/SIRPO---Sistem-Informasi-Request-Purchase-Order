@extends('layouts.app')

@section('title', 'Dashboard User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Dashboard User</h3>
        <div>
            <span class="text-muted">{{ now()->format('l, d F Y') }}</span>
        </div>
    </div>

    <!-- User Statistics Cards -->
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
                        Semua PB di sistem (shared)
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
                            <h6 class="card-title">Template</h6>
                            <h3 class="mb-0">{{ count($availableTemplates ?? []) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-excel fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-light">
                        Template tersedia
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Minggu Ini</h6>
                            <h3 class="mb-0">{{ collect($weeklyTrend ?? [])->sum('count') }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-week fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-light">
                        PB minggu ini
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Charts Column -->
        <div class="col-md-8">
            <!-- Monthly User PB Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">PB Saya - Statistik Bulanan {{ now()->year }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="userMonthlyChart" width="400" height="150"></canvas>
                </div>
            </div>

            <!-- Recent User PBs -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">PB Terakhir Saya</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nomor PB</th>
                                    <th>Tanggal</th>
                                    <th>Divisi</th>
                                    <th class="text-end">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($recentPbs && count($recentPbs) > 0)
                                    @foreach($recentPbs as $pb)
                                    <tr>
                                        <td>{{ $pb->nomor_pb }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pb->tanggal)->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $pb->divisi == 'OP' ? 'primary' : 'success' }}">
                                                {{ strtoupper($pb->divisi) }}
                                            </span>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($pb->nominal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada PB yang dibuat</td>
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
            <!-- Available Templates -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Template Tersedia</h5>
                    <a href="{{ route('templates.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($availableTemplates && count($availableTemplates) > 0)
                        @foreach($availableTemplates as $template)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-file-excel text-success fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $template->name }}</div>
                                <div class="text-muted small">
                                    {{ Str::limit($template->description, 50) }}
                                </div>
                                <a href="{{ route('templates.download', $template) }}" class="btn btn-sm btn-outline-success mt-1">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> Belum ada template tersedia
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Division Stats -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Statistik Divisi Saya</h5>
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
                        <div class="col-md-3">
                            <a href="{{ route('pb.create') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-plus"></i><br>Tambah PB Baru
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('pb.index') }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-list"></i><br>Lihat Semua PB
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('templates.index') }}" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-file-excel"></i><br>Download Template
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('notifications.index') }}" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-bell"></i><br>Notifikasi
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
    // User Monthly PB Chart
    const userMonthlyCtx = document.getElementById('userMonthlyChart').getContext('2d');
    const monthlyData = @json($monthlyPbs ?? []);

    const monthNames = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ];

    const labels = monthNames;
    const counts = new Array(12).fill(0);
    const amounts = new Array(12).fill(0);

    userMonthlyData.forEach(item => {
        counts[item.month - 1] = item.count;
        amounts[item.month - 1] = item.total / 1000000; // Convert to millions
    });

    new Chart(userMonthlyCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah PB Saya',
                data: counts,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Total Nominal (Juta)',
                data: amounts,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
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
