@extends('layouttempalte.master')

@section('title', 'Template Management - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 text-dark">Template Management</h1>
                            <p class="text-muted">Kelola template Excel untuk sistem PB</p>
                        </div>
                        @if(auth()->user()->role === 'admin')
                            <div>
                                <a href="{{ route('templates.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Tambah Template
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Statistics Cards (Admin Only) -->
            @if(auth()->user()->role === 'admin')
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-alt fa-2x mb-3"></i>
                                <h4 class="mb-1">{{ isset($templates) ? $templates->count() : 0 }}</h4>
                                <p class="mb-0 small opacity-75">Total Templates</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-3"></i>
                                <h4 class="mb-1">{{ isset($templates) ? $templates->where('is_active', true)->count() : 0 }}</h4>
                                <p class="mb-0 small opacity-75">Templates Aktif</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-pause-circle fa-2x mb-3"></i>
                                <h4 class="mb-1">{{ isset($templates) ? $templates->where('is_active', false)->count() : 0 }}</h4>
                                <p class="mb-0 small opacity-75">Templates Nonaktif</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-download fa-2x mb-3"></i>
                                <h4 class="mb-1">0</h4>
                                <p class="mb-0 small opacity-75">Total Download</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Templates Grid -->
            <div class="row">
                @if(!isset($templates) || $templates->count() === 0)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-file-alt fa-4x text-muted mb-4"></i>
                                <h5 class="text-muted">Tidak ada template tersedia</h5>
                                @if(auth()->user()->role === 'admin')
                                    <p class="text-muted mb-4">Mulai dengan menambahkan template Excel pertama Anda</p>
                                    <a href="{{ route('templates.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah Template Pertama
                                    </a>
                                @else
                                    <p class="text-muted">Template akan muncul di sini setelah admin mengupload</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    @foreach($templates as $template)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card template-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-excel text-success me-2"></i>
                                        {{ $template->name ?? 'Template' }}
                                    </h6>
                                    <span class="badge {{ ($template->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ($template->is_active ?? true) ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <p class="card-text text-muted small">
                                        {{ $template->description ?? 'Tidak ada deskripsi' }}
                                    </p>

                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-file me-1"></i>
                                            {{ $template->original_filename ?? 'Tidak ada file' }}
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ isset($template->created_at) ? $template->created_at->format('d M Y') : 'Tidak tersedia' }}
                                        </small>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('templates.show', $template->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Lihat Detail
                                        </a>

                                        @if(isset($template->original_filename))
                                            <a href="{{ route('templates.download', $template->id) }}" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        @endif

                                        @if(auth()->user()->role === 'admin')
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-outline-warning btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <form action="{{ route('templates.destroy', $template->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Yakin ingin menghapus template ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i>Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Pagination -->
            @if(isset($templates) && method_exists($templates, 'hasPages') && $templates->hasPages())
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $templates->links() }}
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
