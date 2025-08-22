<?php

namespace App\Http\Controllers;

use App\Models\Pbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
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

            // Clean up temporary files
            $tempFile = storage_path('app/temp_db_backup.sql');
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

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
        $sqlFile = storage_path('app/temp_db_backup.sql');
        $this->createManualDatabaseBackup($sqlFile);

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
     * Download backup file
     */
    public function download($filename)
    {
        try {
            $filepath = storage_path('app/backups/' . $filename);

            if (!file_exists($filepath)) {
                return response()->json(['error' => 'File tidak ditemukan'], 404);
            }

            return response()->download($filepath);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete backup file
     */
    public function delete($filename)
    {
        try {
            $filepath = storage_path('app/backups/' . $filename);

            if (!file_exists($filepath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            unlink($filepath);

            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus backup: ' . $e->getMessage()
            ], 500);
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
}
