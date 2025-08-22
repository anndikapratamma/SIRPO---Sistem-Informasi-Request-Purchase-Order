<?php

namespace App\Console\Commands;

use App\Http\Controllers\BackupController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestBackup extends Command
{
    protected $signature = 'test:backup';
    protected $description = 'Test backup functionality';

    public function handle()
    {
        $this->info('Testing backup functionality...');

        try {
            // Simulate authentication for user with ID 1
            auth()->loginUsingId(1);

            $controller = new BackupController();
            $request = new Request(['backup_type' => 'database_only']);

            $this->info('Creating database backup...');
            $response = $controller->createBackup($request);

            $this->info('Backup test completed successfully!');

        } catch (\Exception $e) {
            $this->error('Backup test failed: ' . $e->getMessage());
        }

        return 0;
    }
}
