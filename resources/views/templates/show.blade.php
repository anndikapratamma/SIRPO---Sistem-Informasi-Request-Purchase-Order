@extends('layouttempalte.master')

@section('title', 'Detail Template - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            @if(!$template)
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Template tidak ditemukan.
                </div>
                <a href="{{ route('templates.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Templates
                </a>
            @else
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 text-dark">Detail Template</h1>
                                <p class="text-muted">Informasi lengkap template Excel</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('templates.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('templates.edit', $template) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-excel text-success me-2"></i>{{ $template->name ?? 'Nama tidak tersedia' }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Deskripsi:</strong></div>
                                    <div class="col-sm-9">{{ $template->description ?? '-' }}</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>File:</strong></div>
                                    <div class="col-sm-9">
                                        <i class="fas fa-file-excel text-success me-2"></i>
                                        {{ $template->original_filename ?? 'Tidak tersedia' }}
                                        @if($template->original_filename)
                                            <a href="{{ route('templates.download', $template) }}"
                                               class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Status:</strong></div>
                                    <div class="col-sm-9">
                                        <span class="badge {{ $template->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Tanggal Dibuat:</strong></div>
                                    <div class="col-sm-9">{{ $template->created_at ? $template->created_at->format('d F Y, H:i') : 'Tidak tersedia' }} WIB</div>
                                </div>

                                @if(auth()->user()->role !== 'admin')
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Cara Menggunakan:</strong>
                                        <ol class="mb-0 mt-2">
                                            <li>Klik "Buat PB Baru" di menu</li>
                                            <li>Pilih template "{{ $template->name }}" jika tersedia</li>
                                            <li>Form akan otomatis terisi sesuai template</li>
                                            <li>Lengkapi data dan submit PB Anda</li>
                                        </ol>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Additional scripts can be added here if needed -->
@endsection
