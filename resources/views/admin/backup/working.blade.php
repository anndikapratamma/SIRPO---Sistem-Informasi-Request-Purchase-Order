@extends('layouttempalte.master')

@section('title', 'Backup - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="container-fluid p-4">
        <h2><i class="fas fa-database mr-2"></i>Backup</h2>
        <p class="text-muted">Kelola backup database dan file sistem SIRPO</p>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <!-- Create Backup -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-plus-circle mr-2"></i>Buat Backup Baru</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-primary btn-block" onclick="createBackup('database')">
                            <i class="fas fa-database mr-2"></i>Backup Database
                        </button>
                        <small class="text-muted">Backup seluruh database</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-success btn-block" onclick="createBackup('files')">
                            <i class="fas fa-folder mr-2"></i>Backup Files
                        </button>
                        <small class="text-muted">Backup file sistem</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-info btn-block" onclick="createBackup('full')">
                            <i class="fas fa-save mr-2"></i>Backup Lengkap
                        </button>
                        <small class="text-muted">Backup database & files</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5><i class="fas fa-list mr-2"></i>Daftar Backup</h5>
                <div>
                    <a href="{{ route('admin.backup.list') }}" class="btn btn-sm btn-outline-secondary mr-2" target="_blank">
                        <i class="fas fa-bug"></i> Test API
                    </a>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshBackupList()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="backup-list">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Memuat daftar backup...</p>
                    </div>
                </div>

                <!-- Fallback if JavaScript fails -->
                <noscript>
                    <div class="alert alert-warning">
                        <strong>JavaScript dibutuhkan</strong><br>
                        Silakan aktifkan JavaScript atau
                        <a href="{{ route('admin.backup.list') }}" target="_blank">klik di sini untuk melihat API backup</a>
                    </div>
                </noscript>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            refreshBackupList();
        });

        function createBackup(type) {
            const button = event.target;
            const originalText = button.innerHTML;

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Membuat backup...';

            fetch('{{ route("admin.backup.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ type: type })
            })
            .then(response => response.json())
            .then(data => {
                button.disabled = false;
                button.innerHTML = originalText;

                if (data.success) {
                    showAlert('success', data.message || 'Backup berhasil dibuat!');
                    refreshBackupList();
                } else {
                    showAlert('danger', data.message || 'Gagal membuat backup!');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.innerHTML = originalText;
                showAlert('danger', 'Terjadi kesalahan!');
                console.error('Error:', error);
            });
        }

        function refreshBackupList() {
            console.log('Starting refreshBackupList...');
            const listContainer = document.getElementById('backup-list');

            listContainer.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Memuat daftar backup...</p>
                </div>
            `;

            // Test the route URL first
            const apiUrl = '{{ route("admin.backup.list") }}';
            console.log('API URL:', apiUrl);

            // Add timeout for fetch request
            const controller = new AbortController();
            const timeoutId = setTimeout(() => {
                console.log('Request timeout!');
                controller.abort();
            }, 15000); // 15 second timeout

            fetch(apiUrl, {
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                clearTimeout(timeoutId);
                console.log('Response received!');
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success && data.backups && data.backups.length > 0) {
                    let html = '';
                    data.backups.forEach(backup => {
                        html += `
                            <div class="row align-items-center mb-3 p-3 border rounded">
                                <div class="col-md-6">
                                    <h6>${backup.filename}</h6>
                                    <small class="text-muted">${backup.created_at} - ${backup.size || 'N/A'}</small>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge badge-primary">${backup.type}</span>
                                </div>
                                <div class="col-md-3 text-right">
                                    <a href="{{ route('admin.backup.download', '') }}/${backup.filename}" class="btn btn-sm btn-success mr-1">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteBackup('${backup.filename}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    listContainer.innerHTML = html;
                } else if (data.success) {
                    // Success but no backups
                    listContainer.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h4>Belum ada backup</h4>
                            <p class="text-muted">Buat backup pertama Anda menggunakan tombol di atas</p>
                        </div>
                    `;
                } else {
                    // Error response
                    listContainer.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h4>Error: ${data.message || 'Gagal memuat backup'}</h4>
                            <button class="btn btn-primary" onclick="refreshBackupList()">Coba Lagi</button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                console.error('Fetch error:', error);

                let errorMessage = 'Terjadi kesalahan saat memuat daftar backup';
                if (error.name === 'AbortError') {
                    errorMessage = 'Request timeout - coba lagi';
                } else if (error.message) {
                    errorMessage = error.message;
                }

                listContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>Gagal memuat daftar backup</h4>
                        <p class="text-muted">${errorMessage}</p>
                        <button class="btn btn-primary" onclick="refreshBackupList()">Coba Lagi</button>
                    </div>
                `;
            });
        }

        function deleteBackup(filename) {
            if (!confirm(`Hapus backup "${filename}"?`)) return;

            fetch(`{{ route('admin.backup.delete', '') }}/${filename}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Backup berhasil dihapus!');
                    refreshBackupList();
                } else {
                    showAlert('danger', 'Gagal menghapus backup!');
                }
            })
            .catch(error => {
                showAlert('danger', 'Terjadi kesalahan!');
                console.error('Error:', error);
            });
        }

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;
            document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', alertHtml);
        }
    </script>
@endsection
