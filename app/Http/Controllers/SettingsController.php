<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    /**
     * Show settings page
     */
    public function index()
    {
        $settings = [
            'app_name' => env('APP_NAME', 'SIRPO'),
            'app_url' => env('APP_URL', 'http://localhost'),
            'app_timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
            'app_locale' => env('APP_LOCALE', 'id'),
            'mail_mailer' => env('MAIL_MAILER', 'smtp'),
            'mail_host' => env('MAIL_HOST', ''),
            'mail_port' => env('MAIL_PORT', '587'),
            'mail_username' => env('MAIL_USERNAME', ''),
            'mail_encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'backup_schedule' => $this->getBackupSchedule(),
            'notification_enabled' => $this->getNotificationSettings(),
            'max_file_size' => ini_get('upload_max_filesize'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        $diskSpace = [
            'total' => disk_total_space(storage_path()),
            'free' => disk_free_space(storage_path()),
            'used' => disk_total_space(storage_path()) - disk_free_space(storage_path())
        ];

        return view('admin.settings.index', compact('settings', 'diskSpace'));
    }

    /**
     * Update application settings
     */
    public function updateApp(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_timezone' => 'required|string',
            'app_locale' => 'required|string'
        ]);

        $this->updateEnvFile([
            'APP_NAME' => $request->app_name,
            'APP_TIMEZONE' => $request->app_timezone,
            'APP_LOCALE' => $request->app_locale
        ]);

        return redirect()->route('admin.settings.index')
                       ->with('success', 'Pengaturan aplikasi berhasil diperbarui');
    }

    /**
     * Update mail settings
     */
    public function updateMail(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required|string|in:tls,ssl,null',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string'
        ]);

        $mailSettings = [
            'MAIL_HOST' => $request->mail_host,
            'MAIL_PORT' => $request->mail_port,
            'MAIL_USERNAME' => $request->mail_username,
            'MAIL_ENCRYPTION' => $request->mail_encryption,
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME' => '"' . $request->mail_from_name . '"'
        ];

        if ($request->filled('mail_password')) {
            $mailSettings['MAIL_PASSWORD'] = $request->mail_password;
        }

        $this->updateEnvFile($mailSettings);

        return redirect()->route('admin.settings.index')
                       ->with('success', 'Pengaturan email berhasil diperbarui');
    }

    /**
     * Test email configuration
     */
    public function testMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            Mail::raw('Test email dari SIRPO. Konfigurasi email berhasil!', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email SIRPO');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email berhasil dikirim ke ' . $request->test_email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update backup settings
     */
    public function updateBackup(Request $request)
    {
        $request->validate([
            'backup_schedule' => 'required|string|in:daily,weekly,monthly,disabled',
            'backup_retention' => 'required|integer|min:1|max:90'
        ]);

        $scheduleFile = storage_path('app/backup_schedule.json');
        $settings = [
            'schedule' => $request->backup_schedule,
            'retention_days' => $request->backup_retention,
            'updated_at' => now()->toDateTimeString(),
            'updated_by' => auth()->user()->name
        ];

        Storage::put('backup_schedule.json', json_encode($settings, JSON_PRETTY_PRINT));

        // Update Laravel scheduler if needed
        if ($request->backup_schedule !== 'disabled') {
            $this->updateScheduler($request->backup_schedule);
        }

        return redirect()->route('admin.settings.index')
                       ->with('success', 'Pengaturan backup berhasil diperbarui');
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json(['message' => 'Cache berhasil dibersihkan!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membersihkan cache: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get system information
     */
    public function systemInfo()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => [
                'driver' => config('database.default'),
                'version' => DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown'
            ],
            'disk_space' => [
                'total' => $this->formatBytes(disk_total_space(storage_path())),
                'free' => $this->formatBytes(disk_free_space(storage_path())),
                'used' => $this->formatBytes(disk_total_space(storage_path()) - disk_free_space(storage_path()))
            ],
            'php_extensions' => get_loaded_extensions(),
            'php_ini' => [
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size')
            ]
        ];

        return response()->json($systemInfo);
    }

    /**
     * Update .env file
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }

    /**
     * Get backup schedule
     */
    private function getBackupSchedule()
    {
        $scheduleFile = storage_path('app/backup_schedule.json');
        if (file_exists($scheduleFile)) {
            $settings = json_decode(file_get_contents($scheduleFile), true);
            return $settings['schedule'] ?? 'disabled';
        }
        return 'disabled';
    }

    /**
     * Get notification settings
     */
    private function getNotificationSettings()
    {
        // This could be expanded to include more notification preferences
        return true;
    }

    /**
     * Update scheduler
     */
    private function updateScheduler($schedule)
    {
        // This would update the Laravel task scheduler
        // Implementation depends on your server setup
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Clear application logs
     */
    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs');
            $files = glob($logPath . '/*.log');

            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            return response()->json(['message' => 'Log berhasil dibersihkan!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membersihkan log: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Optimize application
     */
    public function optimize()
    {
        try {
            Artisan::call('optimize');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            return response()->json(['message' => 'Aplikasi berhasil dioptimasi!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengoptimasi aplikasi: ' . $e->getMessage()], 500);
        }
    }
}
