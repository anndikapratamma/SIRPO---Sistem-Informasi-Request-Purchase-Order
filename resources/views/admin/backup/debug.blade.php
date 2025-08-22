@extends('layouttempalte.master')

@section('title', 'Backup Debug - SIRPO')

@section('content')
    <div class="container-fluid p-4">
        <div class="alert alert-warning">
            <h4><i class="fas fa-bug mr-2"></i>BACKUP DEBUG MODE</h4>
            <p>Halaman ini untuk debugging masalah backup yang tidak berfungsi</p>
        </div>

        <!-- Test Buttons -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-test-tube mr-2"></i>Test Functions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <button id="test-api" class="btn btn-primary btn-block" onclick="testAPI()">
                            <i class="fas fa-bug mr-2"></i>Test API
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button id="test-routes" class="btn btn-success btn-block" onclick="testRoutes()">
                            <i class="fas fa-route mr-2"></i>Test Routes
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button id="test-create" class="btn btn-warning btn-block" onclick="testCreate()">
                            <i class="fas fa-plus mr-2"></i>Test Create
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button id="clear-log" class="btn btn-danger btn-block" onclick="clearLog()">
                            <i class="fas fa-trash mr-2"></i>Clear Log
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Output -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-terminal mr-2"></i>Debug Log</h5>
            </div>
            <div class="card-body">
                <div id="debug-log" style="background: #f8f9fa; padding: 15px; border-radius: 5px; height: 400px; overflow-y: auto; font-family: monospace;">
                    <div class="text-muted">Ready for debugging... Click buttons above to test.</div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-link mr-2"></i>Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="btn-group mr-2">
                    <a href="/admin/backup/list" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt mr-1"></i>API List
                    </a>
                    <a href="/admin/backup-test" target="_blank" class="btn btn-outline-secondary">
                        <i class="fas fa-external-link-alt mr-1"></i>Backup Test
                    </a>
                    <a href="/backup-simple" target="_blank" class="btn btn-outline-info">
                        <i class="fas fa-external-link-alt mr-1"></i>Simple Backup
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let logCount = 0;

function log(message, type = 'info') {
    logCount++;
    const timestamp = new Date().toLocaleTimeString();
    const logDiv = document.getElementById('debug-log');

    const colors = {
        'info': '#17a2b8',
        'success': '#28a745',
        'error': '#dc3545',
        'warning': '#ffc107'
    };

    const color = colors[type] || '#6c757d';

    logDiv.innerHTML += `
        <div style="margin-bottom: 8px; padding: 5px; border-left: 3px solid ${color};">
            <small style="color: #666;">[${logCount}] ${timestamp}</small><br>
            <span style="color: ${color};">[${type.toUpperCase()}]</span> ${message}
        </div>
    `;

    logDiv.scrollTop = logDiv.scrollHeight;
}

function testAPI() {
    log('🧪 Testing API endpoint...', 'info');

    const btn = document.getElementById('test-api');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';

    fetch('/admin/backup/list', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        log(`📡 Response Status: ${response.status} ${response.statusText}`, response.ok ? 'success' : 'error');
        return response.json();
    })
    .then(data => {
        log(`✅ API Response received successfully!`, 'success');
        log(`📊 Data: ${JSON.stringify(data)}`, 'info');

        if (data.backups) {
            log(`📁 Found ${data.backups.length} backup files`, 'success');
            data.backups.forEach((backup, index) => {
                log(`   ${index + 1}. ${backup.filename} (${backup.type})`, 'info');
            });
        }
    })
    .catch(error => {
        log(`❌ API Error: ${error.message}`, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-bug mr-2"></i>Test API';
    });
}

function testRoutes() {
    log('🛣️ Testing route accessibility...', 'info');

    const routes = [
        '/admin/backup',
        '/admin/backup/list',
        '/backup-simple'
    ];

    routes.forEach(route => {
        fetch(route)
        .then(response => {
            log(`${route} → Status: ${response.status}`, response.ok ? 'success' : 'error');
        })
        .catch(error => {
            log(`${route} → Error: ${error.message}`, 'error');
        });
    });
}

function testCreate() {
    log('🔨 Testing backup creation...', 'info');

    const btn = document.getElementById('test-create');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';

    fetch('/admin/backup/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ type: 'database' })
    })
    .then(response => {
        log(`📤 Create Response: ${response.status}`, response.ok ? 'success' : 'error');
        return response.json();
    })
    .then(data => {
        log(`✅ Create response: ${JSON.stringify(data)}`, 'success');
    })
    .catch(error => {
        log(`❌ Create Error: ${error.message}`, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus mr-2"></i>Test Create';
    });
}

function clearLog() {
    document.getElementById('debug-log').innerHTML = '<div class="text-muted">Log cleared. Ready for new tests.</div>';
    logCount = 0;
}

// Auto-start with jQuery ready
$(document).ready(function() {
    log('🚀 Backup Debug System loaded!', 'success');
    log('💡 Click buttons above to test different functions', 'info');
    log('🔧 JavaScript functions are now properly loaded', 'success');
});
</script>
@endpush
