<!DOCTYPE html>
<html>
<head>
    <title>Backup Test - SIRPO</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid p-4">
        <div class="alert alert-info">
            <h4><i class="fas fa-tools mr-2"></i>BACKUP SYSTEM TESTER</h4>
            <p>Halaman simpel untuk test backup tanpa dependency layout</p>
        </div>

        <!-- Test Buttons -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Test Functions</h5>
            </div>
            <div class="card-body">
                <button id="test-api" class="btn btn-primary mr-2" onclick="testAPI()">
                    <i class="fas fa-bug mr-1"></i>Test API
                </button>
                <button id="test-simple" class="btn btn-success mr-2" onclick="testSimple()">
                    <i class="fas fa-check mr-1"></i>Test Simple
                </button>
                <button class="btn btn-warning mr-2" onclick="showBackups()">
                    <i class="fas fa-list mr-1"></i>Show Backups
                </button>
                <button class="btn btn-danger" onclick="clearResults()">
                    <i class="fas fa-trash mr-1"></i>Clear
                </button>
            </div>
        </div>

        <!-- Results -->
        <div class="card">
            <div class="card-header">
                <h5>Test Results</h5>
            </div>
            <div class="card-body">
                <div id="results" style="background: #f8f9fa; padding: 15px; border-radius: 5px; min-height: 200px; font-family: monospace; white-space: pre-wrap;"></div>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="mt-4">
            <h6>Quick Access Links:</h6>
            <a href="/admin/backup/list" target="_blank" class="btn btn-sm btn-outline-primary mr-2">API Direct</a>
            <a href="/backup-simple" target="_blank" class="btn btn-sm btn-outline-secondary mr-2">Simple Backup</a>
            <a href="/admin/backup-test" target="_blank" class="btn btn-sm btn-outline-info">Debug Page</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        function log(message) {
            const time = new Date().toLocaleTimeString();
            const results = document.getElementById('results');
            results.textContent += `[${time}] ${message}\n`;
            results.scrollTop = results.scrollHeight;
        }

        function testAPI() {
            log('🧪 Testing API...');

            fetch('/admin/backup/list')
            .then(response => {
                log(`📡 Response: ${response.status} ${response.statusText}`);
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
            })
            .then(data => {
                log('✅ API SUCCESS!');
                log(`📊 Found ${data.backups ? data.backups.length : 0} backups`);
                if (data.backups) {
                    data.backups.forEach(backup => {
                        log(`   📁 ${backup.filename} (${backup.type})`);
                    });
                }
            })
            .catch(error => {
                log(`❌ API ERROR: ${error.message}`);
            });
        }

        function testSimple() {
            log('🔧 Testing simple request...');

            $.get('/admin/backup/list')
            .done(function(data) {
                log('✅ JQUERY SUCCESS!');
                log(`📊 Response: ${JSON.stringify(data).substring(0, 100)}...`);
            })
            .fail(function(xhr, status, error) {
                log(`❌ JQUERY ERROR: ${error}`);
            });
        }

        function showBackups() {
            log('📋 Manual backup list from API test:');
            log('   sirpo_backup_full_2025-08-08_10-34-34.zip (full)');
            log('   sirpo_backup_files_2025-08-08_10-34-32.zip (files)');
            log('   sirpo_backup_database_2025-08-08_10-34-30.zip (database)');
            log('   sirpo_backup_full_2025-08-06_16-10-10.zip (full)');
            log('   sirpo_backup_full_2025-08-06_16-05-55.zip (full)');
        }

        function clearResults() {
            document.getElementById('results').textContent = '';
            log('🧹 Results cleared. Ready for testing!');
        }

        // Auto start
        $(document).ready(function() {
            log('🚀 Simple Backup Tester loaded!');
            log('💡 Click buttons above to test functionality');
        });
    </script>
</body>
</html>
