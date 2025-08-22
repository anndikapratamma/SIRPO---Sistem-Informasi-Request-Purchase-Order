@extends('layouttempalte.master')

@section('title', 'Edit Template - SIRPO')

@section('content')
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
                    <i class="fas fa-file-invoice"></i>{{ auth()->user()->role === 'admin' ? 'Kelola PB' : 'My PB' }}
                </a>
                <a class="nav-link" href="{{ route('pb.create') }}">
                    <i class="fas fa-plus-circle"></i>Buat PB Baru
                </a>
                <a class="nav-link active" href="{{ route('templates.index') }}">
                    <i class="fas fa-file-alt"></i>Templates
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 text-dark">Edit Template</h1>
                    <p class="text-muted">Update template Excel yang sudah ada</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-edit me-2"></i>Form Edit Template
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('templates.update', $template) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Nama Template -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Nama Template <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $template->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Deskripsi -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        Deskripsi
                                    </label>
                                    <textarea id="description"
                                              name="description"
                                              class="form-control @error('description') is-invalid @enderror"
                                              rows="3">{{ old('description', $template->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- File Excel Saat Ini -->
                                <div class="mb-3">
                                    <label class="form-label">File Excel Saat Ini</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-file-excel text-success"></i>
                                        <span>{{ $template->original_filename }}</span>
                                        <a href="{{ route('templates.download', $template) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>

                                <!-- Upload File Excel Baru -->
                                <div class="mb-3">
                                    <label for="excel_file" class="form-label">
                                        Ganti File Excel (Opsional)
                                    </label>
                                    <input type="file"
                                           id="excel_file"
                                           name="excel_file"
                                           class="form-control @error('excel_file') is-invalid @enderror"
                                           accept=".xlsx,.xls">
                                    <div class="form-text">
                                        Kosongkan jika tidak ingin mengganti file. Format: .xlsx, .xls (maksimal 5MB)
                                    </div>
                                    @error('excel_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status Aktif -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               id="is_active"
                                               name="is_active"
                                               value="1"
                                               class="form-check-input @error('is_active') is-invalid @enderror"
                                               {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                        <label for="is_active" class="form-check-label">
                                            Template Aktif (tampil di dropdown user)
                                        </label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('templates.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Template
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Additional scripts can be added here if needed -->
@endsection
