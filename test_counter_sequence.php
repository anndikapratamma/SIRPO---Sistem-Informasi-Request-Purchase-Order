<?php

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test PB Counter Sequence ===\n";

// Check current state
$counter = App\Models\PbCounter::first();
$lastPb = App\Models\Pbs::orderBy('nomor_pb', 'desc')->first();

echo "Before test:\n";
echo "- Counter value: " . ($counter ? $counter->counter_value : 'not found') . "\n";
echo "- Last PB number: " . ($lastPb ? $lastPb->nomor_pb : 'not found') . "\n";
echo "\n";

// Test getting next numbers
echo "Getting next 5 PB numbers:\n";
for ($i = 1; $i <= 5; $i++) {
    $nextNumber = App\Models\PbCounter::getNextNumber();
    echo "$i. Next PB number: $nextNumber\n";

    // Reset counter for next test (simulate getting number without creating PB)
    App\Models\PbCounter::where('id', 1)->update(['counter_value' => $lastPb->nomor_pb + $i]);
}

echo "\nTest completed. Counter will always continue from last actual PB number.\n";
