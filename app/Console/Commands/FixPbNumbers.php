<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pbs;
use App\Models\PbCounter;

class FixPbNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pb:fix-numbers {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix PB numbers that have timestamp format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Find PBs with wrong format (containing timestamp)
        $wrongPbs = Pbs::where('nomor_pb', 'regexp', '[0-9]{10}')->get();

        if ($wrongPbs->isEmpty()) {
            $this->info('No PBs found with wrong number format.');
            return;
        }

        $this->info("Found {$wrongPbs->count()} PBs with wrong format:");

        foreach ($wrongPbs as $pb) {
            $this->line("- {$pb->nomor_pb} (ID: {$pb->id})");
        }

        if ($dryRun) {
            $this->warn('This is a dry run. No changes were made.');
            return;
        }

        if (!$this->confirm('Do you want to delete these PBs with wrong format?')) {
            $this->info('Operation cancelled.');
            return;
        }

        // Delete wrong PBs
        $deletedCount = Pbs::where('nomor_pb', 'regexp', '[0-9]{10}')->delete();

        // Reset counters
        PbCounter::truncate();

        $this->info("Deleted {$deletedCount} PBs with wrong format.");
        $this->info('Reset all PB counters.');
        $this->info('Next PB numbers will start from PB-001 for each date.');
    }
}
