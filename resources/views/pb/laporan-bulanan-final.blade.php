@extends('layouttempalte.master')

@section('title', 'Laporan Bulanan PB - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-line me-2"></i>Laporan Bulanan PB</h2>
                <div class="btn-group">
                    <a href="{{ route('pb.laporan.mingguan') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-week me-1"></i>Laporan Mingguan
                    </a>
                    <a href="{{ route('pb.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke PB
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('pb.laporan.bulanan') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="bulan" class="form-label">Bulan</label>
                                <select name="bulan" id="bulan" class="form-select">
                                    @if(isset($months))
                                        @foreach($months as $key => $month)
                                            <option value="{{ $key }}" {{ ($bulan ?? date('n')) == $key ? 'selected' : '' }}>
                                                {{ $month }}
                                            </option>
                                        @endforeach
                                    @else
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ (date('n')) == $i ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                            </option>
                                        @endfor
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <select name="tahun" id="tahun" class="form-select">
                                    @if(isset($years))
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ ($tahun ?? date('Y')) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    @else
                                        @for($y = 2020; $y <= date('Y') + 1; $y++)
                                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="divisi" class="form-label">Divisi</label>
                                <select name="divisi" id="divisi" class="form-select">
                                    <option value="">Semua Divisi</option>
                                    @if(isset($divisions))
                                        @foreach($divisions as $division)
                                            <option value="{{ $division }}" {{ ($divisi ?? '') == $division ? 'selected' : '' }}>
                                                {{ $division }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="E-CHANNEL">E-CHANNEL</option>
                                        <option value="TREASURY OPERASIONAL">TREASURY OPERASIONAL</option>
                                        <option value="LAYANAN OPERASIONAL">LAYANAN OPERASIONAL</option>
                                        <option value="AKUNTANSI & TAX MANAGEMENT">AKUNTANSI & TAX MANAGEMENT</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">{{ number_format($stats['total_pb'] ?? 0) }}</h5>
                            <p class="card-text">Total PB</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title">{{ number_format($stats['total_aktif'] ?? 0) }}</h5>
                            <p class="card-text">PB Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                            <h5 class="card-title">{{ number_format($stats['total_batal'] ?? 0) }}</h5>
                            <p class="card-text">PB Batal</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                            <h5 class="card-title">Rp {{ number_format($stats['total_nominal_aktif'] ?? 0) }}</h5>
                            <p class="card-text">Total Nominal Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-ban fa-2x text-danger mb-2"></i>
                            <h5 class="card-title">Rp {{ number_format($stats['total_nominal_batal'] ?? 0) }}</h5>
                            <p class="card-text">Total Nominal Batal</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-download me-2"></i>Export Laporan {{ $periode ?? 'Bulanan' }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="btn-group">
                                <a href="{{ route('pb.export.pdf', request()->query()) }}" class="btn btn-danger">
                                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                                </a>
                                <a href="{{ route('pb.export.excel', request()->query()) }}" class="btn btn-success">
                                    <i class="fas fa-file-excel me-1"></i>Export Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Breakdown by Division -->
            @if(isset($byDivisi) && $byDivisi->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Breakdown per Divisi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Divisi</th>
                                    <th>Total PB</th>
                                    <th>PB Aktif</th>
                                    <th>PB Batal</th>
                                    <th>Nominal Aktif</th>
                                    <th>Nominal Batal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($byDivisi as $divisiName => $data)
                                <tr>
                                    <td><strong>{{ $divisiName }}</strong></td>
                                    <td>{{ number_format($data['total'] ?? 0) }}</td>
                                    <td><span class="badge bg-success">{{ number_format($data['aktif'] ?? 0) }}</span></td>
                                    <td><span class="badge bg-danger">{{ number_format($data['batal'] ?? 0) }}</span></td>
                                    <td>Rp {{ number_format($data['nominal_aktif'] ?? 0) }}</td>
                                    <td>Rp {{ number_format($data['nominal_batal'] ?? 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Detail PB -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detail PB {{ $periode ?? 'Bulanan' }}</h5>
                </div>
                <div class="card-body">
                    @if(isset($pbs) && $pbs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nomor PB</th>
                                        <th>Tanggal</th>
                                        <th>Penginput</th>
                                        <th>Divisi</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pbs as $pb)
                                    <tr>
                                        <td>
                                            <a href="{{ route('pb.show', $pb->id) }}" class="text-decoration-none">
                                                {{ $pb->nomor_pb }}
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($pb->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ $pb->penginput ?? ($pb->user->name ?? 'Unknown') }}</td>
                                        <td>
                                            @if($pb->divisi == 'E-CHANNEL')
                                                <span class="badge bg-info">E-CHANNEL</span>
                                            @elseif($pb->divisi == 'TREASURY OPERASIONAL')
                                                <span class="badge bg-warning">TREASURY OPR</span>
                                            @elseif($pb->divisi == 'LAYANAN OPERASIONAL')
                                                <span class="badge bg-primary">LAYANAN OPR</span>
                                            @else
                                                <span class="badge bg-secondary">AKUNTANSI & TAX</span>
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($pb->nominal) }}</td>
                                        <td>
                                            @if($pb->status == 'active')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Batal</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($pb->keterangan ?? '-', 50) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h4>Tidak ada data PB</h4>
                            <p class="text-muted">Tidak ada PB yang ditemukan untuk periode {{ $periode ?? 'yang dipilih' }}</p>
                            <a href="{{ route('pb.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah PB Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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

                console.log('SIRPO Laporan Bulanan initialized successfully');
            } catch (error) {
                console.log('Error initializing app features:', error);
            }
        });
    </script>
@endsection
