@extends('layouttempalte.master')

@section('title', 'Backup - SIRPO')

@section('content')
    <div class="container-fluid p-4">
        <h2><i class="fas fa-database mr-2"></i>Backup System</h2>
        <p class="text-muted">Kelola backup database dan file sistem SIRPO</p>

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
            <div class="card-header">
                <h5><i class="fas fa-list mr-2"></i>Daftar Backup</h5>
                <button class="btn btn-sm btn-outline-primary float-right" onclick="loadBackups()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div id="backup-list">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Loading backups...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    loadBackups();
});

function createBackup(type) {
    const button = event.target;
    const originalText = button.innerHTML;

    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';

    $.ajax({
        url: '{{ route("admin.backup.create") }}',
        method: 'POST',
        data: {
            type: type,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            button.disabled = false;
            button.innerHTML = originalText;

            if (response.success) {
                alert('Backup created successfully!');
                loadBackups();
            } else {
                alert('Failed to create backup: ' + response.message);
            }
        },
        error: function() {
            button.disabled = false;
            button.innerHTML = originalText;
            alert('Error creating backup!');
        }
    });
}

function loadBackups() {
    $('#backup-list').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2">Loading backups...</p>
        </div>
    `);

    $.ajax({
        url: '{{ route("admin.backup.list") }}',
        method: 'GET',
        success: function(response) {
            console.log('Response:', response);

            if (response.success && response.backups && response.backups.length > 0) {
                let html = '';
                response.backups.forEach(function(backup) {
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
                $('#backup-list').html(html);
            } else {
                $('#backup-list').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h4>No backups found</h4>
                        <p class="text-muted">Create your first backup using the buttons above</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            $('#backup-list').html(`
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h4>Failed to load backups</h4>
                    <p class="text-muted">Error: ${error}</p>
                    <button class="btn btn-primary" onclick="loadBackups()">Try Again</button>
                </div>
            `);
        }
    });
}

function deleteBackup(filename) {
    if (!confirm(`Delete backup "${filename}"?`)) return;

    $.ajax({
        url: `{{ route('admin.backup.delete', '') }}/${filename}`,
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                alert('Backup deleted successfully!');
                loadBackups();
            } else {
                alert('Failed to delete backup!');
            }
        },
        error: function() {
            alert('Error deleting backup!');
        }
    });
}
</script>
@endsection
