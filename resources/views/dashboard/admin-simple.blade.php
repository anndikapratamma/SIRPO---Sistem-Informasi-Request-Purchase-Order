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
            <!-- Weekly Trend Table (Simple Version) -->
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

            <!-- Recent PBs -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">PB Terakhir</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nomor PB</th>
                                    <th>Tanggal</th>
                                    <th>Penginput</th>
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
                                        <td>{{ $pb->penginput }}</td>
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
                                        <td colspan="5" class="text-center text-muted">Belum ada PB</td>
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
                            <a href="{{ route('templates.index') }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-file-excel"></i><br>Template
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.backup.index') }}" class="btn btn-warning w-100 mb-2">
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
@endsection
