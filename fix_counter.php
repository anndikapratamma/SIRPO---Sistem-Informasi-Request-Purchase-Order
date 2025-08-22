<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PbCounter;
use App\Models\Pbs;

// Get last PB number
$lastPb = Pbs::orderBy('nomor_pb', 'desc')->first();
echo "Last PB number: " . ($lastPb ? $lastPb->nomor_pb : 'None') . "\n";

// Update counter
$counter = PbCounter::first();
if ($counter) {
    $counter->counter_value = $lastPb ? intval($lastPb->nomor_pb) : 4601;
    $counter->save();
    echo "Counter updated to: " . $counter->counter_value . "\n";
} else {
    echo "No counter found\n";
}

// Test next number
$nextNumber = PbCounter::getNextNumber();
echo "Next PB number will be: " . $nextNumber . "\n";
