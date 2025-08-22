<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PbCounter;

class ResetPbCounter extends Command
{
    protected $signature = 'pb:reset-counter {value=4589 : Counter value to set}';
    protected $description = 'Reset PB counter to specific value';

    public function handle()
    {
        $value = (int) $this->argument('value');

        try {
            $nextNumber = PbCounter::resetToValue($value);

            $this->info("✅ Counter reset to {$value}");
            $this->info("✅ Next PB number will be: {$nextNumber}");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to reset counter: " . $e->getMessage());
            return 1;
        }
    }
}
