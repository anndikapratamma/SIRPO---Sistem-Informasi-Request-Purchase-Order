@extends('layouts.app')

@section('title', 'Tambah Template Excel')

@section('content')
<div class="container">
    <h3>Tambah Template Excel</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('templates.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Nama Template -->
                <div class="mb-3">
                    <label for="name" class="form-label">
                        Nama Template <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           required
                           placeholder="Contoh: Template Rekening Bank Default">
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
                              rows="3"
                              placeholder="Deskripsi tentang template ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- File Excel -->
                <div class="mb-4">
                    <label for="excel_file" class="form-label">
                        File Excel <span class="text-danger">*</span>
                    </label>
                    <input type="file"
                           id="excel_file"
                           name="excel_file"
                           class="form-control @error('excel_file') is-invalid @enderror"
                           accept=".xlsx,.xls"
                           required>
                    <div class="form-text">
                        Format yang didukung: .xlsx, .xls (maksimal 5MB)
                    </div>
                    @error('excel_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-warning">
                    <h6><i class="fas fa-info-circle"></i> Panduan Template Excel:</h6>
                    <ul class="mb-0">
                        <li>File Excel harus berisi data item AAI untuk rekening bank default</li>
                        <li>Format kolom: Nama Item, Kode Rekening, Deskripsi</li>
                        <li>Template ini akan muncul di dropdown saat user membuat PB</li>
                        <li>Pastikan data sudah lengkap dan akurat</li>
                    </ul>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
