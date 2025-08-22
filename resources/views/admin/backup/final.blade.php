@extends('layouttempalte.master')

@section('title', 'Backup System - SIRPO')

@section('content')
    <div class="container-fluid p-4">
        <div class="row">
            <div class="col-12">
                <h2><i class="fas fa-database mr-2"></i>Backup System</h2>
                <p class="text-muted">Kelola backup database dan file sistem SIRPO</p>
            </div>
        </div>

        <!-- Alert untuk status -->
        <div id="alert-container"></div>

        <!-- Create Backup Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-plus-circle mr-2"></i>Buat Backup Baru</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <button id="btn-database" class="btn btn-primary btn-lg btn-block" onclick="createBackup('database')">
                                        <i class="fas fa-database mr-2"></i>
                                        <br>Backup Database
                                    </button>
                                    <small class="text-muted mt-2 d-block">Backup seluruh database MySQL</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <button id="btn-files" class="btn btn-success btn-lg btn-block" onclick="createBackup('files')">
                                        <i class="fas fa-folder mr-2"></i>
                                        <br>Backup Files
                                    </button>
                                    <small class="text-muted mt-2 d-block">Backup file aplikasi & uploads</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <button id="btn-full" class="btn btn-info btn-lg btn-block" onclick="createBackup('full')">
                                        <i class="fas fa-save mr-2"></i>
                                        <br>Backup Lengkap
                                    </button>
                                    <small class="text-muted mt-2 d-block">Backup database + files</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup List Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-list mr-2"></i>Daftar Backup</h5>
                        <button class="btn btn-outline-primary btn-sm" onclick="loadBackupList()">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="backup-list-container">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-2">Memuat daftar backup...</p>
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
$(document).ready(function() {
    // Load backup list on page load
    loadBackupList();

    // Show success message
    showAlert('info', '🚀 Backup System siap digunakan!');
});

function createBackup(type) {
    const button = $(`#btn-${type}`);
    const originalText = button.html();

    // Update button state
    button.prop('disabled', true);
    button.html('<i class="fas fa-spinner fa-spin mr-2"></i>Membuat backup...');

    // Show progress
    showAlert('info', `Sedang membuat backup ${type}...`);

    // AJAX request
    $.ajax({
        url: '/admin/backup/create',
        method: 'POST',
        data: {
            type: type,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', `✅ ${response.message}`);
                if (response.filename) {
                    showAlert('info', `📁 File: ${response.filename} (${response.size || 'Unknown size'})`);
                }
                // Reload backup list
                setTimeout(function() {
                    loadBackupList();
                }, 1000);
            } else {
                showAlert('danger', `❌ ${response.message || 'Gagal membuat backup'}`);
            }
        },
        error: function(xhr, status, error) {
            showAlert('danger', `❌ Error: ${error}`);
        },
        complete: function() {
            // Restore button
            button.prop('disabled', false);
            button.html(originalText);
        }
    });
}

function loadBackupList() {
    const container = $('#backup-list-container');

    // Show loading
    container.html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2">Memuat daftar backup...</p>
        </div>
    `);

    $.ajax({
        url: '/admin/backup/list',
        method: 'GET',
        success: function(response) {
            if (response.success && response.backups) {
                displayBackupList(response.backups);
            } else {
                container.html(`
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h4>Belum ada backup</h4>
                        <p class="text-muted">Buat backup pertama menggunakan tombol di atas</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            container.html(`
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h4>Gagal memuat daftar backup</h4>
                    <p class="text-muted">Error: ${error}</p>
                    <button class="btn btn-primary" onclick="loadBackupList()">Coba Lagi</button>
                </div>
            `);
        }
    });
}

function displayBackupList(backups) {
    let html = '';

    if (backups.length === 0) {
        html = `
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4>Belum ada backup</h4>
                <p class="text-muted">Buat backup pertama menggunakan tombol di atas</p>
            </div>
        `;
    } else {
        html = '<div class="table-responsive"><table class="table table-hover">';
        html += `
            <thead>
                <tr>
                    <th><i class="fas fa-file mr-1"></i>Filename</th>
                    <th><i class="fas fa-calendar mr-1"></i>Created</th>
                    <th><i class="fas fa-hdd mr-1"></i>Size</th>
                    <th><i class="fas fa-tag mr-1"></i>Type</th>
                    <th><i class="fas fa-cogs mr-1"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
        `;

        backups.forEach(function(backup) {
            const typeColors = {
                'database': 'primary',
                'files': 'success',
                'full': 'info'
            };

            const typeColor = typeColors[backup.type] || 'secondary';

            html += `
                <tr>
                    <td>
                        <strong>${backup.filename}</strong>
                    </td>
                    <td>
                        <small class="text-muted">${backup.created_at}</small>
                    </td>
                    <td>
                        <span class="badge badge-light">${backup.size || 'N/A'}</span>
                    </td>
                    <td>
                        <span class="badge badge-${typeColor}">${backup.type}</span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="/admin/backup/download/${backup.filename}" class="btn btn-sm btn-success" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteBackup('${backup.filename}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table></div>';
    }

    $('#backup-list-container').html(html);
}

function deleteBackup(filename) {
    if (!confirm(`Hapus backup "${filename}"?\n\nPerhatian: File backup yang dihapus tidak dapat dikembalikan!`)) {
        return;
    }

    showAlert('warning', `Menghapus backup ${filename}...`);

    $.ajax({
        url: `/admin/backup/delete/${filename}`,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', `✅ Backup berhasil dihapus!`);
                loadBackupList();
            } else {
                showAlert('danger', `❌ ${response.message || 'Gagal menghapus backup'}`);
            }
        },
        error: function(xhr, status, error) {
            showAlert('danger', `❌ Error menghapus backup: ${error}`);
        }
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

    $('#alert-container').prepend(alertHtml);

    // Auto remove after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush
