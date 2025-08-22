@extends('layouttempalte.master')

@section('title', 'Dashboard User - SIRPO')

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 text-dark">System Settings</h1>
                    <p class="text-muted">Pengaturan dan konfigurasi sistem SIRPO</p>
                </div>
            </div>
            <!-- Settings Tabs -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-pills card-header-pills" id="settingsTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" role="tab">
                                        <i class="fas fa-cog mr-2"></i>General
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="email-tab" data-toggle="pill" href="#email" role="tab">
                                        <i class="fas fa-envelope mr-2"></i>Email
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="security-tab" data-toggle="pill" href="#security" role="tab">
                                        <i class="fas fa-shield-alt mr-2"></i>Security
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="system-tab" data-toggle="pill" href="#system" role="tab">
                                        <i class="fas fa-server mr-2"></i>System
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="settingsTabContent">
                                <!-- General Settings -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <form action="{{ route('admin.settings.update') }}" method="POST">
                                        @csrf
                                        <div class="setting-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1">Application Name</h6>
                                                    <p class="text-muted mb-0">Nama aplikasi yang ditampilkan</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" name="app_name" value="{{ config('app.name', 'SIRPO') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="setting-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1">Application URL</h6>
                                                    <p class="text-muted mb-0">URL utama aplikasi</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="url" class="form-control" name="app_url" value="{{ config('app.url') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="setting-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1">Default Language</h6>
                                                    <p class="text-muted mb-0">Bahasa default sistem</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="app_locale">
                                                        <option value="id" selected>Indonesia</option>
                                                        <option value="en">English</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="setting-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1">Timezone</h6>
                                                    <p class="text-muted mb-0">Zona waktu aplikasi</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="app_timezone">
                                                        <option value="Asia/Jakarta" selected>Asia/Jakarta</option>
                                                        <option value="UTC">UTC</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save mr-2"></i>Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <!-- Email Settings -->
                                <div class="tab-pane fade" id="email" role="tabpanel">
                                    <form action="{{ route('admin.settings.update-email') }}" method="POST">
                                        @csrf
                                        <div class="setting-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1">SMTP Host</h6>
                                                    <p class="text-muted mb-0">Server SMTP untuk pengiriman email</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" name="mail_host" value="{{ config('mail.mailers.smtp.host') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="setting-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1">SMTP Port</h6>
                                                    <p class="text-muted mb-0">Port SMTP</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" class="form-control" name="mail_port" value="{{ config('mail.mailers.smtp.port') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="setting-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1">From Email</h6>
                                                    <p class="text-muted mb-0">Email pengirim default</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="email" class="form-control" name="mail_from_address" value="{{ config('mail.from.address') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save mr-2"></i>Save Email Settings
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <!-- Security Settings -->
                                <div class="tab-pane fade" id="security" role="tabpanel">
                                    <div class="setting-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">Session Timeout</h6>
                                                <p class="text-muted mb-0">Waktu timeout session (menit)</p>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="session_timeout" value="120">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setting-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">Force HTTPS</h6>
                                                <p class="text-muted mb-0">Paksa menggunakan koneksi HTTPS</p>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="forceHttps" name="force_https" {{ config('app.force_https') ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setting-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">Two Factor Authentication</h6>
                                                <p class="text-muted mb-0">Aktifkan 2FA untuk admin</p>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="twoFactor" name="two_factor" {{ config('app.two_factor') ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i>Save Security Settings
                                        </button>
                                    </div>
                                </div>
                                <!-- System Settings -->
                                <div class="tab-pane fade" id="system" role="tabpanel">
                                    <div class="setting-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">Debug Mode</h6>
                                                <p class="text-muted mb-0">Mode debug untuk development</p>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="debugMode" {{ config('app.debug') ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setting-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">Cache System</h6>
                                                <p class="text-muted mb-0">Driver cache yang digunakan</p>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control" name="cache_driver">
                                                    <option value="file" {{ config('cache.default') == 'file' ? 'selected' : '' }}>File</option>
                                                    <option value="redis" {{ config('cache.default') == 'redis' ? 'selected' : '' }}>Redis</option>
                                                    <option value="database" {{ config('cache.default') == 'database' ? 'selected' : '' }}>Database</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setting-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">Queue Driver</h6>
                                                <p class="text-muted mb-0">Driver untuk antrian job</p>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control" name="queue_driver">
                                                    <option value="sync" {{ config('queue.default') == 'sync' ? 'selected' : '' }}>Sync</option>
                                                    <option value="database" {{ config('queue.default') == 'database' ? 'selected' : '' }}>Database</option>
                                                    <option value="redis" {{ config('queue.default') == 'redis' ? 'selected' : '' }}>Redis</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setting-item">
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="mb-3">Maintenance Actions</h6>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-warning" onclick="clearCache()">
                                                        <i class="fas fa-broom mr-2"></i>Clear Cache
                                                    </button>
                                                    <button type="button" class="btn btn-info" onclick="clearLogs()">
                                                        <i class="fas fa-file-alt mr-2"></i>Clear Logs
                                                    </button>
                                                    <button type="button" class="btn btn-success" onclick="optimizeApp()">
                                                        <i class="fas fa-rocket mr-2"></i>Optimize App
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- System Info -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle mr-2"></i>System Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Laravel Version:</strong></td>
                                            <td>{{ app()->version() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>PHP Version:</strong></td>
                                            <td>{{ phpversion() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Environment:</strong></td>
                                            <td>{{ app()->environment() }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Database:</strong></td>
                                            <td>{{ config('database.default') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cache Driver:</strong></td>
                                            <td>{{ config('cache.default') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Queue Driver:</strong></td>
                                            <td>{{ config('queue.default') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize Bootstrap 4 tabs
        $(document).ready(function() {
            // Activate tab switching
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href");
                console.log("Switched to tab: " + target);
            });
        });

        function clearCache() {
            if (confirm('Are you sure you want to clear application cache?')) {
                fetch('{{ route("admin.clear-cache") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    alert(data.message || 'Cache cleared successfully!');
                });
            }
        }

        function clearLogs() {
            if (confirm('Are you sure you want to clear application logs?')) {
                fetch('{{ route("admin.clear-logs") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    alert(data.message || 'Logs cleared successfully!');
                });
            }
        }

        function optimizeApp() {
            if (confirm('This will optimize the application. Continue?')) {
                fetch('{{ route("admin.optimize") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    alert(data.message || 'Application optimized successfully!');
                });
            }
        }
    </script>
@endpush
