{{-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Backup Management - SIRPO</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #6f42c1;
            --sidebar-text: #ffffff;
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --sidebar-active: rgba(255, 255, 255, 0.2);
            --main-bg: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--main-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, #6f42c1 0%, #007bff 100%);
            color: var(--sidebar-text);
            overflow-y: auto;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            margin-bottom: 2px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
            opacity: 0.8;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: var(--sidebar-text);
            opacity: 1;
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            background-color: var(--sidebar-active);
            color: var(--sidebar-text);
            opacity: 1;
            font-weight: 600;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        /* Main content area */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background-color: var(--main-bg);
        }

        /* Mobile navbar - hidden by default */
        .navbar-mobile {
            display: none;
        }

        /* Mobile responsive */
        @media (max-width: 767.98px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
            }

            .navbar-mobile {
                display: block;
            }
        }

        /* Custom card styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid var(--border-color);
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem;
        }

        /* Navigation divider */
        .nav-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 10px;
        }

        /* Dashboard cards */
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
        }

        /* Backup specific styles */
        .backup-item {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }

        .backup-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .progress {
            height: 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-none d-md-block">
        <div class="p-3">
            <h4 class="text-white mb-4">
                <i class="fas fa-file-invoice-dollar me-2"></i>SIRPO
            </h4>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('pb.index') ? 'active' : '' }}" href="{{ route('pb.index') }}">
                    <i class="fas fa-file-invoice"></i>Kelola PB
                </a>
                <a class="nav-link {{ request()->routeIs('pb.create') ? 'active' : '' }}" href="{{ route('pb.create') }}">
                    <i class="fas fa-plus"></i>Tambah PB
                </a>

                @if(Auth::check() && Auth::user()->role === 'admin')
                <!-- Admin Only Menu -->
                <hr class="text-white-50 my-3">
                <h6 class="text-white-50 mb-2 px-3">ADMIN</h6>
                <a class="nav-link {{ request()->routeIs('pb.laporan.bulanan') ? 'active' : '' }}" href="{{ route('pb.laporan.bulanan') }}">
                    <i class="fas fa-calendar"></i>Laporan Bulanan
                </a>
                <a class="nav-link {{ request()->routeIs('pb.laporan.mingguan') ? 'active' : '' }}" href="{{ route('pb.laporan.mingguan') }}">
                    <i class="fas fa-calendar-alt"></i>Laporan Mingguan
                </a>
                <a class="nav-link {{ request()->routeIs('admin.backup.index') ? 'active' : '' }}" href="{{ route('admin.backup.index') }}">
                    <i class="fas fa-database"></i>Backup Management
                </a>
                @endif

                <!-- Other Menus -->
                <hr class="text-white-50 my-3">
                <a class="nav-link" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user"></i>Profil
                </a>
                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </div>

        <!-- User Info at Bottom -->
        @if(Auth::check())
        <div class="position-absolute bottom-0 w-100 p-3">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="text-white fw-bold">{{ Auth::user()->name }}</div>
                    <div class="text-white-50 small">{{ Auth::user()->role }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Mobile Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary navbar-mobile">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-file-invoice-dollar me-2"></i>SIRPO
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mobileNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pb.index') }}">
                            <i class="fas fa-file-invoice me-2"></i>Kelola PB
                        </a>
                    </li>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.backup.index') }}">
                            <i class="fas fa-database me-2"></i>Backup
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-database me-2"></i>Backup Management</h2>
                    <p class="text-muted mb-0">Kelola backup database dan file sistem SIRPO</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Create Backup Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Buat Backup Baru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-primary btn-lg" onclick="createBackup('database')">
                                    <i class="fas fa-database me-2"></i>Backup Database
                                </button>
                                <small class="text-muted mt-1">Backup seluruh database termasuk data PB, user, dan pengaturan</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-success btn-lg" onclick="createBackup('files')">
                                    <i class="fas fa-folder me-2"></i>Backup Files
                                </button>
                                <small class="text-muted mt-1">Backup file sistem, uploads, dan konfigurasi</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-grid">
                                <button class="btn btn-info btn-lg" onclick="createBackup('full')">
                                    <i class="fas fa-save me-2"></i>Backup Lengkap
                                </button>
                                <small class="text-muted mt-1">Backup database dan files dalam satu paket</small>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div id="backup-progress" class="mt-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span id="backup-status">Memproses backup...</span>
                            <span id="backup-percentage">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Backup List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Backup
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshBackupList()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="backup-list">
                        <!-- Loading state -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat daftar backup...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Set CSRF token for all AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Load backup list on page load
            refreshBackupList();

            console.log('Backup Management initialized successfully');
        });

        // Create backup function
        function createBackup(type) {
            const button = event.target.closest('button');
            const originalText = button.innerHTML;

            // Show progress
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Membuat backup...';

            showProgress('Memulai proses backup...');

            // Simulate progress
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 20;
                if (progress > 90) progress = 90;
                updateProgress(progress, `Memproses backup ${type}...`);
            }, 500);

            fetch(`{{ route('admin.backup.create') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ type: type })
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(progressInterval);
                updateProgress(100, 'Backup selesai!');

                setTimeout(() => {
                    hideProgress();
                    button.disabled = false;
                    button.innerHTML = originalText;

                    if (data.success) {
                        showAlert('success', data.message || 'Backup berhasil dibuat!');
                        refreshBackupList();
                    } else {
                        showAlert('danger', data.message || 'Gagal membuat backup!');
                    }
                }, 1000);
            })
            .catch(error => {
                clearInterval(progressInterval);
                hideProgress();
                button.disabled = false;
                button.innerHTML = originalText;
                showAlert('danger', 'Terjadi kesalahan saat membuat backup!');
                console.error('Error:', error);
            });
        }

        // Refresh backup list
        function refreshBackupList() {
            const listContainer = document.getElementById('backup-list');

            listContainer.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat daftar backup...</p>
                </div>
            `;

            fetch(`{{ route('admin.backup.list') }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.backups.length > 0) {
                    let html = '';
                    data.backups.forEach(backup => {
                        html += createBackupItem(backup);
                    });
                    listContainer.innerHTML = html;
                } else {
                    listContainer.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h4>Belum ada backup</h4>
                            <p class="text-muted">Buat backup pertama Anda menggunakan tombol di atas</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                listContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>Gagal memuat daftar backup</h4>
                        <p class="text-muted">Terjadi kesalahan saat mengambil data backup</p>
                        <button class="btn btn-primary" onclick="refreshBackupList()">
                            <i class="fas fa-retry me-2"></i>Coba Lagi
                        </button>
                    </div>
                `;
                console.error('Error:', error);
            });
        }

        // Create backup item HTML
        function createBackupItem(backup) {
            const typeIcon = backup.type === 'database' ? 'fas fa-database' :
                           backup.type === 'files' ? 'fas fa-folder' : 'fas fa-save';
            const typeColor = backup.type === 'database' ? 'primary' :
                            backup.type === 'files' ? 'success' : 'info';

            return `
                <div class="backup-item">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="${typeIcon} fa-2x text-${typeColor} me-3"></i>
                                <div>
                                    <h6 class="mb-1">${backup.filename}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>${backup.created_at}
                                        <i class="fas fa-hdd ms-3 me-1"></i>${backup.size}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-${typeColor}">${backup.type.toUpperCase()}</span>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.backup.download', '') }}/${backup.filename}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteBackup('${backup.filename}')">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Delete backup function
        function deleteBackup(filename) {
            if (!confirm(`Apakah Anda yakin ingin menghapus backup "${filename}"?`)) {
                return;
            }

            fetch(`{{ route('admin.backup.delete', '') }}/${filename}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Backup berhasil dihapus!');
                    refreshBackupList();
                } else {
                    showAlert('danger', data.message || 'Gagal menghapus backup!');
                }
            })
            .catch(error => {
                showAlert('danger', 'Terjadi kesalahan saat menghapus backup!');
                console.error('Error:', error);
            });
        }

        // Progress functions
        function showProgress(status) {
            document.getElementById('backup-progress').style.display = 'block';
            document.getElementById('backup-status').textContent = status;
            updateProgress(0, status);
        }

        function updateProgress(percentage, status) {
            const progressBar = document.querySelector('.progress-bar');
            const percentageSpan = document.getElementById('backup-percentage');
            const statusSpan = document.getElementById('backup-status');

            progressBar.style.width = percentage + '%';
            percentageSpan.textContent = Math.round(percentage) + '%';
            if (status) statusSpan.textContent = status;
        }

        function hideProgress() {
            document.getElementById('backup-progress').style.display = 'none';
        }

        // Alert function
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            const container = document.querySelector('.container-fluid');
            const firstCard = container.querySelector('.card');
            firstCard.insertAdjacentHTML('beforebegin', alertHtml);

            // Auto hide after 5 seconds
            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }
    </script>
</body>
</html> --}}
