@extends('layouttempalte.master')

@section('title', 'Tambah Template Excel - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 text-dark">Tambah Template Excel</h1>
                    <p class="text-muted">Upload template Excel untuk digunakan dalam sistem PB</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-plus-circle me-2"></i>Form Upload Template
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

                            <form method="POST" action="{{ route('templates.store') }}" enctype="multipart/form-data">
                                @csrf

                                <!-- Nama Template -->
                                <div class="mb-4">
                                    <label for="name" class="form-label">
                                        Nama Template <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           placeholder="Contoh: Template PB Operasional"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Masukkan nama yang mudah dikenali untuk template ini</div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="mb-4">
                                    <label for="description" class="form-label">
                                        Deskripsi Template
                                    </label>
                                    <textarea id="description"
                                              name="description"
                                              class="form-control @error('description') is-invalid @enderror"
                                              rows="3"
                                              placeholder="Jelaskan kegunaan template ini...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Deskripsi opsional untuk menjelaskan kegunaan template</div>
                                </div>

                                <!-- File Upload -->
                                <div class="mb-4">
                                    <label for="excel_file" class="form-label">
                                        Upload File Excel <span class="text-danger">*</span>
                                    </label>

                                    <!-- Drop Zone -->
                                    <div class="drop-zone" id="dropZone" onclick="document.getElementById('excel_file').click()">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Klik atau drag & drop file Excel di sini</h5>
                                        <p class="text-muted mb-2">Format yang didukung: .xlsx, .xls</p>
                                        <small class="text-muted">Maksimal ukuran file: 5MB</small>
                                    </div>

                                    <input type="file"
                                           id="excel_file"
                                           name="excel_file"
                                           class="form-control d-none @error('excel_file') is-invalid @enderror"
                                           accept=".xlsx,.xls"
                                           required>

                                    @error('excel_file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    <!-- File Info Display -->
                                    <div id="fileInfo" class="file-info d-none">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-excel text-success me-3 fa-2x"></i>
                                            <div>
                                                <h6 class="mb-1" id="fileName">filename.xlsx</h6>
                                                <small class="text-muted" id="fileSize">0 KB</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="removeFile()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Aktif -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               id="is_active"
                                               name="is_active"
                                               value="1"
                                               class="form-check-input @error('is_active') is-invalid @enderror"
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label for="is_active" class="form-check-label">
                                            <strong>Aktifkan Template</strong>
                                        </label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Template aktif akan ditampilkan kepada user untuk dipilih</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Template
                                    </button>
                                    <a href="{{ route('templates.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Panduan Upload Template
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-success">✓ Format yang Didukung</h6>
                                <ul class="small mb-0">
                                    <li>Microsoft Excel (.xlsx)</li>
                                    <li>Excel 97-2003 (.xls)</li>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-info">📋 Struktur Template Yang Baik</h6>
                                <ul class="small mb-0">
                                    <li>Gunakan header yang jelas</li>
                                    <li>Format data konsisten</li>
                                    <li>Tidak ada merged cells</li>
                                    <li>Data mulai dari baris pertama</li>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-warning">⚠️ Batasan</h6>
                                <ul class="small mb-0">
                                    <li>Maksimal ukuran: 5MB</li>
                                    <li>Hanya satu file per template</li>
                                    <li>File akan disimpan dengan aman</li>
                                </ul>
                            </div>

                            <div class="alert alert-light">
                                <small>
                                    <i class="fas fa-lightbulb text-warning me-1"></i>
                                    <strong>Tips:</strong> Pastikan template Excel Anda sudah berisi contoh data dan format yang sesuai dengan kebutuhan PB.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
    // File upload handling
    const fileInput = document.getElementById('excel_file');
    const dropZone = document.getElementById('dropZone');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    // Handle file input change
    fileInput.addEventListener('change', handleFileSelect);

    // Handle drag and drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect();
        }
    });

    function handleFileSelect() {
        const file = fileInput.files[0];
        if (file) {
            // Validate file type
            const allowedTypes = ['.xlsx', '.xls'];
            const fileExtension = file.name.toLowerCase().substr(file.name.lastIndexOf('.'));

            if (!allowedTypes.includes(fileExtension)) {
                alert('File harus berformat Excel (.xlsx atau .xls)');
                fileInput.value = '';
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB');
                fileInput.value = '';
                return;
            }

            // Show file info
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('d-none');
            dropZone.style.display = 'none';
        }
    }

    function removeFile() {
        fileInput.value = '';
        fileInfo.classList.add('d-none');
        dropZone.style.display = 'block';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    </script>
@endsection

@section('styles')
    <style>
        .drop-zone {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .drop-zone:hover,
        .drop-zone.dragover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }

        .file-info {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
@endsection
