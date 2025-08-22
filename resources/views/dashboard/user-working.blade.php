@extends('layouttempalte.master')

@section('title', 'Dashboard User - SIRPO')

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 text-dark">Dashboard User</h1>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}! Kelola PB Anda dengan mudah.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">Total PB</h5>
                        <h2 class="mb-0">{{ number_format($totalUserPbs) }}</h2>
                    </div>
                    <div class="col-auto">
                        <i class="ti ti-file-invoice fs-1"></i>
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
                        <h5 class="card-title mb-0">Pending</h5>
                        <h2 class="mb-0">{{ number_format($pendingPbs) }}</h2>
                    </div>
                    <div class="col-auto">
                        <i class="ti ti-clock fs-1"></i>
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
                        <h5 class="card-title mb-0">Approved</h5>
                        <h2 class="mb-0">{{ number_format($approvedPbs) }}</h2>
                    </div>
                    <div class="col-auto">
                        <i class="ti ti-check-circle fs-1"></i>
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
                        <h5 class="card-title mb-0">Total Amount</h5>
                        <h2 class="mb-0">Rp {{ number_format($totalUserAmount) }}</h2>
                    </div>
                    <div class="col-auto">
                        <i class="ti ti-currency-dollar fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Recent PBs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-history me-2"></i>PB Saya Terbaru
                </h5>
            </div>
            <div class="card-body">
                @if($userPbs->count() > 0)
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
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userPbs as $index => $pb)
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
                                    <td>Rp {{ number_format($pb->nominal) }}</td>
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
                                    <td>
                                        <a href="{{ route('pb.show', $pb->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-inbox fs-1 text-muted mb-3"></i>
                        <p class="text-muted">Anda belum membuat PB apapun.</p>
                        <a href="{{ route('pb.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Buat PB Baru
                        </a>
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
                    <i class="ti ti-bolt me-2"></i>Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('pb.create') }}" class="btn btn-success w-100">
                            <i class="ti ti-plus me-2"></i>Buat PB Baru
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('pb.index') }}" class="btn btn-primary w-100">
                            <i class="ti ti-list me-2"></i>Lihat Semua PB
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('templates.index') }}" class="btn btn-info w-100">
                            <i class="ti ti-file-text me-2"></i>Template
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-warning w-100">
                            <i class="ti ti-user-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
