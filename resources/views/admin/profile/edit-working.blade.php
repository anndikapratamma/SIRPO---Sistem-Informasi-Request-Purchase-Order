<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Admin - SIRPO</title>

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

        /* Mobile responsive */
        @media (max-width: 767.98px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
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

        .admin-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #6f42c1 0%, #007bff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            font-weight: bold;
            margin: 0 auto 1rem;
        }

        .stats-card {
            background: linear-gradient(135deg, #6f42c1 0%, #007bff 100%);
            color: white;
            border: none;
        }

        .info-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
        }

        /* WhatsApp-style profile photo styling */
        .profile-preview-container {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            position: relative;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #25d366;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            transition: all 0.3s ease;
        }

        .profile-preview-container:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
        }

        .profile-preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 50%;
        }

        .profile-default-avatar {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            font-weight: bold;
            border-radius: 50%;
        }

        /* WhatsApp-style rounded profile photos everywhere */
        .rounded-circle {
            border: 2px solid #25d366 !important;
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.2) !important;
            transition: all 0.3s ease !important;
        }

        .rounded-circle:hover {
            transform: scale(1.1) !important;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4) !important;
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
                <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i>Kelola Akun
                </a>
                <a class="nav-link {{ request()->routeIs('admin.profile-changes.index') ? 'active' : '' }}" href="{{ route('admin.profile-changes.index') }}">
                    <i class="fas fa-user-edit"></i>Perubahan Profil
                </a>
                <a class="nav-link {{ request()->routeIs('admin.profile.edit') ? 'active' : '' }}" href="{{ route('admin.profile.edit') }}">
                    <i class="fas fa-user-shield"></i>Profil Admin
                </a>
                @endif

                <!-- Other Menus -->
                <hr class="text-white-50 my-3">
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-user-shield me-2"></i>Profil Admin</h2>
                    <p class="text-muted mb-0">Kelola informasi dan keamanan akun administrator</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-1"></i>Kelola Akun
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Dashboard
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Admin Profile Information -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Informasi Admin</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.profile.update') }}">
                                @csrf
                                @method('PATCH')

                                <div class="row mb-4">
                                    <div class="col-md-3 text-center">
                                        <div class="admin-avatar">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                        <div class="info-badge">
                                            <i class="fas fa-shield-alt me-1"></i>Administrator
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                       value="{{ old('name', $admin->name) }}" required>
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="nik" class="form-label">NIK Admin</label>
                                                <input type="text" class="form-control" id="nik" name="nik"
                                                       value="{{ old('nik', $admin->nik) }}" required>
                                                @error('nik')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="current_nik" class="form-label">NIK Saat Ini (Verifikasi)</label>
                                                <input type="password" class="form-control" id="current_nik" name="current_nik"
                                                       placeholder="Masukkan NIK saat ini untuk verifikasi" required>
                                                @error('current_nik')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="role" class="form-label">Role</label>
                                                <input type="text" class="form-control" value="Administrator" disabled>
                                                <small class="text-muted">Role tidak dapat diubah</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Perubahan informasi admin akan langsung diterapkan.
                                    Pastikan data yang dimasukkan sudah benar.
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-key me-2"></i>Ubah Password Admin</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.profile.password') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="current_password" class="form-label">Password Saat Ini</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        @error('current_password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Tips Keamanan:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Gunakan minimal 8 karakter</li>
                                        <li>Kombinasikan huruf besar, kecil, angka, dan simbol</li>
                                        <li>Jangan gunakan informasi pribadi</li>
                                    </ul>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-lock me-2"></i>Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Admin Info Sidebar -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Akun</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>NIK:</strong></td>
                                    <td><code>{{ $admin->nik }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td><span class="badge bg-danger">Administrator</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Bergabung:</strong></td>
                                    <td>{{ $admin->created_at ? $admin->created_at->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update:</strong></td>
                                    <td>{{ $admin->updated_at ? $admin->updated_at->format('d M Y H:i') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Admin Privileges -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Hak Akses Admin</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Kelola semua akun pengguna
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Setujui perubahan profil
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Akses laporan dan backup
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Konfigurasi sistem
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Reset password pengguna
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Status Sistem</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="stats-card card">
                                        <div class="card-body">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <h5 class="mb-1">{{ \App\Models\User::count() }}</h5>
                                            <small>Total Users</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stats-card card">
                                        <div class="card-body">
                                            <i class="fas fa-clock fa-2x mb-2"></i>
                                            <h5 class="mb-1">{{ \App\Models\ProfileChangeRequest::pending()->count() }}</h5>
                                            <small>Pending</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-users me-1"></i>Kelola Users
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const toggleButtons = document.querySelectorAll('[data-toggle-password]');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-toggle-password');
                    const passwordField = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        passwordField.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            console.log('Admin profile page initialized successfully');
        });
    </script>
</body>
</html>
