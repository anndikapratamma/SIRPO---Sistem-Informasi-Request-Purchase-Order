<?php

namespace App\Http\Controllers;

use App\Models\Pbs;
use App\Models\Template;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use ZipArchive;

class BackupController extends Controller
{
    public function __construct()
    {
        // Temporarily disable middleware for testing
        // $this->middleware('auth');
        // $this->middleware(function ($request, $next) {
        //     if (auth()->user()->role !== 'admin') {
        //         abort(403, 'Unauthorized');
        //     }
        //     return $next($request);
        // });
    }

    /**
     * Show backup management page
     */
    public function index()
    {
        try {
            return view('admin.backup.working');
        } catch (\Exception $e) {
            return view('admin.backup.test');
        }
    }

    /**
     * Get list of backups (API endpoint)
     */
    public function list()
    {
        try {
            $backupDir = storage_path('app/backups');
            $backups = [];
            
            if (is_dir($backupDir)) {
                $files = glob($backupDir . '/*.zip');
                
                foreach ($files as $file) {
                    $filename = basename($file);
                    $size = filesize($file);
                    $created = date('d-m-Y H:i:s', filemtime($file));
                    
                    // Determine backup type from filename
                    $type = 'unknown';
                    if (strpos($filename, '_database_') !== false) {
                        $type = 'database';
                    } elseif (strpos($filename, '_files_') !== false) {
                        $type = 'files';
                    } elseif (strpos($filename, '_full_') !== false) {
                        $type = 'full';
                    }
                    
                    $backups[] = [
                        'filename' => $filename,
                        'size' => $this->formatBytes($size),
                        'type' => $type,
                        'created_at' => $created
                    ];
                }
                
                // Sort by creation time (newest first)
                usort($backups, function($a, $b) {
                    return strcmp($b['created_at'], $a['created_at']);
                });
            }

            return response()->json([
                'success' => true,
                'backups' => $backups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create backup (API endpoint)
     */
    public function create(Request $request)
    {
        try {
            $backupType = $request->get('type', 'full'); // full, database, files
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupDir = storage_path('app/backups');
            
            // Create backup directory if not exists
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filename = "sirpo_backup_{$backupType}_{$timestamp}.zip";
            $filepath = $backupDir . '/' . $filename;

            $zip = new ZipArchive();
            if ($zip->open($filepath, ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Tidak dapat membuat file ZIP');
            }

            switch ($backupType) {
                case 'database':
                    $this->createDatabaseBackup($zip);
                    break;
                case 'files':
                    $this->createFilesBackup($zip);
                    break;
                case 'full':
                    $this->createDatabaseBackup($zip);
                    $this->createFilesBackup($zip);
                    break;
                default:
                    throw new \Exception('Tipe backup tidak valid');
            }

            $zip->close();

            return response()->json([
                'success' => true,
                'message' => "Backup {$backupType} berhasil dibuat!",
                'filename' => $filename,
                'size' => $this->formatBytes(filesize($filepath))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create database backup
     */
    private function createDatabaseBackup($zip)
    {
        $dbHost = config('database.connections.mysql.host');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $sqlFile = storage_path('app/temp_db_backup.sql');
        
        // Create SQL dump
        $command = "mysqldump --host={$dbHost} --user={$dbUser} --password={$dbPass} {$dbName} > {$sqlFile}";
        
        // For Windows, try different approaches
        if (PHP_OS_FAMILY === 'Windows') {
            // Try to find mysqldump in common locations
            $possiblePaths = [
                'C:\xampp\mysql\bin\mysqldump.exe',
                'C:\wamp64\bin\mysql\mysql8.0.21\bin\mysqldump.exe',
                'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe',
                'mysqldump' // If in PATH
            ];
            
            $mysqldumpPath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path) || $path === 'mysqldump') {
                    $mysqldumpPath = $path;
                    break;
                }
            }
            
            if ($mysqldumpPath) {
                $command = "\"{$mysqldumpPath}\" --host={$dbHost} --user={$dbUser} --password={$dbPass} {$dbName} > \"{$sqlFile}\"";
            } else {
                // Fallback: create manual backup using Laravel
                $this->createManualDatabaseBackup($sqlFile);
            }
        }
        
        // Execute command if mysqldump is available
        if (isset($mysqldumpPath)) {
            exec($command, $output, $returnVar);
            if ($returnVar !== 0 || !file_exists($sqlFile)) {
                $this->createManualDatabaseBackup($sqlFile);
            }
        } else {
            $this->createManualDatabaseBackup($sqlFile);
        }

        if (file_exists($sqlFile)) {
            $zip->addFile($sqlFile, 'database_backup.sql');
        }
    }

    /**
     * Create manual database backup using Laravel
     */
    private function createManualDatabaseBackup($sqlFile)
    {
        $sql = "-- SIRPO Database Backup\n";
        $sql .= "-- Generated on: " . now() . "\n\n";
        
        // Backup tables data
        $tables = ['users', 'pbs', 'failed_jobs', 'personal_access_tokens', 'password_reset_tokens'];
        
        foreach ($tables as $table) {
            try {
                if (Schema::hasTable($table)) {
                    $sql .= "-- Table: {$table}\n";
                    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    
                    // Get table structure
                    $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                    if (!empty($createTable)) {
                        $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                    }
                    
                    // Get table data
                    $rows = DB::table($table)->get();
                    foreach ($rows as $row) {
                        $values = array_map(function($value) {
                            return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                        }, (array)$row);
                        
                        $sql .= "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            } catch (\Exception $e) {
                $sql .= "-- Error backing up table {$table}: " . $e->getMessage() . "\n";
            }
        }
        
        file_put_contents($sqlFile, $sql);
    }

    /**
     * Create files backup
     */
    private function createFilesBackup($zip)
    {
        $filesToBackup = [
            app_path() => 'app/',
            resource_path() => 'resources/',
            config_path() => 'config/',
            database_path() => 'database/',
            public_path() => 'public/',
        ];

        foreach ($filesToBackup as $sourcePath => $zipPath) {
            if (is_dir($sourcePath)) {
                $this->addDirectoryToZip($zip, $sourcePath, $zipPath);
            }
        }

        // Add important files
        $importantFiles = [
            base_path('.env.example') => '.env.example',
            base_path('composer.json') => 'composer.json',
            base_path('package.json') => 'package.json',
            base_path('artisan') => 'artisan',
        ];

        foreach ($importantFiles as $filePath => $zipPath) {
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $zipPath);
            }
        }
    }

    /**
     * Add directory to ZIP recursively
     */
    private function addDirectoryToZip($zip, $sourcePath, $zipPath)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = $zipPath . substr($file->getPathname(), strlen($sourcePath) + 1);
                $relativePath = str_replace('\\', '/', $relativePath);
                $zip->addFile($file->getPathname(), $relativePath);
            }
        }
    }

    /**
     * Format file size
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Download backup file
     */
    public function download($filename)
    {
        $path = storage_path("app/backups/{$filename}");

        if (!file_exists($path)) {
            abort(404, 'Backup file not found');
        }

        // Log activity
        ActivityLog::logActivity('backup_downloaded', null, "Backup {$filename} didownload");

        return response()->download($path);
    }

    /**
     * Delete backup file
     */
    public function delete($filename)
    {
        try {
            $path = "backups/{$filename}";

            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);

                // Log activity
                ActivityLog::logActivity('backup_deleted', null, "Backup {$filename} dihapus");

                return response()->json([
                    'success' => true,
                    'message' => 'Backup berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export database to SQL
     */
    private function exportDatabase($path)
    {
        $filename = 'database_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $filepath = "{$path}/{$filename}";

        $tables = [
            'users',
            'pbs',
            'templates',
            'notifications',
            'activity_logs',
            'password_reset_tokens',
            'personal_access_tokens',
            'failed_jobs'
        ];

        $sql = "-- SIRPO Database Backup\n";
        $sql .= "-- Generated at: " . now() . "\n";
        $sql .= "-- Laravel Version: " . app()->version() . "\n\n";

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $sql .= $this->exportTableStructure($table);
                $sql .= $this->exportTableData($table);
                $sql .= "\n";
            }
        }

        file_put_contents($filepath, $sql);
    }

    /**
     * Backup template files
     */
    private function backupTemplateFiles($path)
    {
        $templatesPath = "{$path}/templates";
        if (!is_dir($templatesPath)) {
            mkdir($templatesPath, 0755, true);
        }

        // Copy uploaded template files
        if (Storage::disk('public')->exists('templates')) {
            $this->copyDirectory(
                storage_path('app/public/templates'),
                $templatesPath
            );
        }
    }

    /**
     * Create ZIP archive
     */
    private function createZipArchive($sourcePath, $zipPath)
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("Cannot create zip file: {$zipPath}");
        }

        $this->addDirectoryToZip($zip, $sourcePath, '');
        $zip->close();
    }

    /**
     * Add directory to ZIP recursively
     */
    private function addDirectoryToZip($zip, $path, $relativePath)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            $filePath = $file->getRealPath();
            $relativeFilePath = $relativePath . substr($filePath, strlen($path) + 1);
            $zip->addFile($filePath, $relativeFilePath);
        }
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory($path)
    {
        if (!is_dir($path)) {
            return;
        }

        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            $filePath = "$path/$file";
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        rmdir($path);
    }

    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item, $target);
            }
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Export table structure to SQL
     */
    private function exportTableStructure($table)
    {
        $sql = "-- Table structure for table `{$table}`\n";
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

        $createTable = DB::select("SHOW CREATE TABLE `{$table}`")[0];
        $sql .= $createTable->{'Create Table'} . ";\n\n";

        return $sql;
    }

    /**
     * Export table data to SQL
     */
    private function exportTableData($table)
    {
        $sql = "-- Dumping data for table `{$table}`\n";

        $rows = DB::table($table)->get();

        if ($rows->count() > 0) {
            $columns = array_keys((array) $rows->first());
            $sql .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES\n";

            $values = [];
            foreach ($rows as $row) {
                $rowValues = [];
                foreach ($columns as $column) {
                    $value = $row->$column;
                    if (is_null($value)) {
                        $rowValues[] = 'NULL';
                    } else {
                        $rowValues[] = "'" . addslashes($value) . "'";
                    }
                }
                $values[] = '(' . implode(', ', $rowValues) . ')';
            }

            $sql .= implode(",\n", $values) . ";\n\n";
        }

        return $sql;
    }
}
