@extends('layouts.app')

@section('title', 'Template Excel Tersedia')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Template Excel Tersedia</h3>
        <div>
            <a href="{{ route('pb.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Buat PB Baru
            </a>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Info:</strong> Berikut adalah template Excel yang tersedia untuk membantu Anda membuat Permintaan Bayar. Pilih template yang sesuai saat membuat PB baru.
    </div>

    <div class="row">
        @forelse($templates as $template)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-excel"></i>
                            {{ $template->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            {{ $template->description ?? 'Tidak ada deskripsi' }}
                        </p>

                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-file"></i> {{ $template->original_filename }}
                            </small>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-user"></i> Dibuat oleh {{ $template->creator->name }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> {{ $template->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('templates.download', $template) }}"
                               class="btn btn-sm btn-outline-info">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <a href="{{ route('templates.show', $template) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                        <h5>Belum Ada Template</h5>
                        <p class="text-muted">Admin belum menambahkan template Excel. Silakan hubungi admin untuk menambahkan template.</p>
                        <a href="{{ route('pb.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Input PB Manual
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Riwayat PB User -->
    <div class="mt-5">
        <h4>Riwayat PB Anda</h4>
        <div class="card">
            <div class="card-body">
                @php
                    $userPbs = App\Models\Pbs::where('penginput', Auth::user()->name)
                                            ->latest()
                                            ->limit(5)
                                            ->get();
                @endphp

                @if($userPbs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                    <th>Divisi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userPbs as $pb)
                                    <tr>
                                        <td>{{ $pb->tanggal ? date('d/m/Y', strtotime($pb->tanggal)) : '-' }}</td>
                                        <td>Rp {{ number_format($pb->nominal, 0, ',', '.') }}</td>
                                        <td>{{ Str::limit($pb->keterangan, 50) }}</td>
                                        <td>
                                            <span class="badge {{ $pb->divisi === 'OP' ? 'bg-success' : 'bg-info' }}">
                                                {{ $pb->divisi === 'OP' ? 'Operasional' : 'Akuntansi' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">Submitted</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('pb.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua PB <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Anda belum membuat PB apapun</p>
                        <a href="{{ route('pb.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Buat PB Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
