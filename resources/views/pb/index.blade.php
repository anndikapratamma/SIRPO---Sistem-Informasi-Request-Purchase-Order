@extends('layouts.app')

@section('title', 'Daftar PB')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-dark">Daftar Permintaan Bayar</h1>
            <p class="text-muted">Kelola dan pantau semua permintaan bayar</p>
        </div>
    </div>

    {{-- aksi --}}
    <div class="row row-cols-1 row-cols-md-4 g-3 mb-4">
        <div class="col">
            <a href="{{ route('pb.create') }}" class="btn btn-primary w-100">
                <i class="fas fa-plus me-2"></i>Tambah PB
            </a>
        </div>
        <div class="col">
            <a href="{{ route('pb.export.excel') }}" class="btn btn-success w-100">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
        </div>
        <div class="col">
            <a href="{{ route('pb.export.pdf') }}" class="btn btn-danger w-100">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
        </div>
        @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
        <div class="col">
            <div class="dropdown w-100">
                <button class="btn btn-warning dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-chart-bar me-2"></i>Laporan
                </button>
                <ul class="dropdown-menu w-100">
                    <li><a class="dropdown-item" href="{{ route('pb.laporan.bulanan') }}">
                        <i class="fas fa-calendar me-2"></i>Bulanan
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('pb.laporan.mingguan') }}">
                        <i class="fas fa-calendar-week me-2"></i>Mingguan
                    </a></li>
                </ul>
            </div>
        </div>
        @endif
    </div>

    {{-- Filter  --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Filter Data</h5>
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Divisi</label>
                    <select name="divisi" class="form-select">
                        <option value="">-- Semua Divisi --</option>
                        <option value="E-CHANNEL" {{ request('divisi') == 'E-CHANNEL' ? 'selected' : '' }}>E-Channel</option>
                        <option value="TREASURY OPERASIONAL" {{ request('divisi') == 'TREASURY OPERASIONAL' ? 'selected' : '' }}>Treasury Operasional</option>
                        <option value="LAYANAN OPERASIONAL" {{ request('divisi') == 'LAYANAN OPERASIONAL' ? 'selected' : '' }}>Layanan Operasional</option>
                        <option value="AKUNTANSI & TAX MANAGEMENT" {{ request('divisi') == 'AKUNTANSI & TAX MANAGEMENT' ? 'selected' : '' }}>Akuntansi & Tax Management</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Data Permintaan Bayar</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nomor PB</th>
                            @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                            <th>Tanggal</th>
                            @endif
                            <th>Penginput</th>
                            @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                            <th>Nominal</th>
                            <th>Keterangan</th>
                            @endif
                            <th>Divisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @forelse($pbs as $pb)
                            @php $total += $pb->nominal; @endphp
                            <tr class="{{ $pb->status === 'cancelled' ? 'table-danger' : '' }}">
                                <td>
                                    <strong>{{ $pb->nomor_pb }}</strong>
                                    @if($pb->status === 'cancelled')
                                        <i class="fas fa-times-circle text-danger ms-1" title="Dibatalkan"></i>
                                    @endif
                                </td>
                                @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                                <td>{{ \Carbon\Carbon::parse($pb->tanggal)->format('d/m/Y') }}</td>
                                @endif
                                <td>{{ $pb->penginput }}</td>
                                @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                                <td>
                                    <span class="badge bg-success">
                                        Rp {{ number_format($pb->nominal, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($pb->keterangan, 50) }}</td>
                                @endif
                                <td>
                                    <span class="badge bg-primary">{{ $pb->divisi }}</span>
                                </td>
                                <td>
                                    @if($pb->status === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('pb.show', $pb->id) }}" class="btn btn-info btn-sm" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($pb->status === 'active')
                                        <a href="{{ route('pb.edit', $pb->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                        @if(Auth::user()->role === 'admin')
                                            @if($pb->status === 'active')
                                            <button type="button" class="btn btn-danger btn-sm" onclick="cancelPb({{ $pb->id }}, '{{ $pb->nomor_pb }}')" title="Batalkan">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-success btn-sm" onclick="restorePb({{ $pb->id }}, '{{ $pb->nomor_pb }}')" title="Kembalikan">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (Auth::user()->role === 'admin' || Auth::user()->is_admin) ? '8' : '5' }}" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>Tidak ada data PB yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(Auth::user()->role === 'admin' || Auth::user()->is_admin && count($pbs) > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="{{ (Auth::user()->role === 'admin' || Auth::user()->is_admin) ? '3' : '2' }}">Total Nominal</th>
                            <th>
                                <span class="badge bg-success fs-6">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </span>
                            </th>
                            <th colspan="{{ (Auth::user()->role === 'admin' || Auth::user()->is_admin) ? '4' : '2' }}"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
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
                        PB yang dibatalkan akan ditandai dan tidak dapat diubah kembali.
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
@endsection

@section('scripts')
<script>
    function cancelPb(pbId, pbNumber) {
        try {
            document.getElementById('cancelPbNumber').textContent = pbNumber;
            document.getElementById('cancelForm').action = `/pb/${pbId}/cancel`;

            const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
            modal.show();
        } catch (error) {
            console.error('Error in cancelPb:', error);
            alert('Error: ' + error.message);
        }
    }

    function restorePb(pbId, pbNumber) {
        try {
            document.getElementById('restorePbNumber').textContent = pbNumber;
            document.getElementById('restoreForm').action = `/pb/${pbId}/restore`;

            const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
            modal.show();
        } catch (error) {
            console.error('Error in restorePb:', error);
            alert('Error: ' + error.message);
        }
    }

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                if (bootstrap.Alert.getOrCreateInstance) {
                    bootstrap.Alert.getOrCreateInstance(alert).close();
                }
            });
        }, 5000);
    });
</script>
