<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pbs;
use App\Models\PbCounter;
use Carbon\Carbon;

echo "=== TEST PB CREATION ===\n\n";

try {
    // Test 1: Generate nomor PB
    echo "1. Generating PB number...\n";
    $nomorPb = PbCounter::getNextNumber(Carbon::today());
    echo "   Generated: {$nomorPb}\n";

    // Test 2: Create PB record
    echo "\n2. Creating PB record...\n";
    $data = [
        'nomor_pb' => $nomorPb,
        'tanggal' => Carbon::today(),
        'penginput' => 'Test User',
        'nominal' => 1000000,
        'keterangan' => 'Test PB creation',
        'divisi' => 'E-CHANNEL',
        'input_date' => Carbon::today(),
        'user_id' => 1, // Assuming user ID 1 exists
        'status' => 'active'
    ];

    $pb = Pbs::create($data);
    echo "   PB created successfully!\n";
    echo "   ID: {$pb->id}\n";
    echo "   Nomor: {$pb->nomor_pb}\n";

    echo "\n✅ PB creation test completed successfully!\n";
    echo "✅ Error 'Column not found: input_date' has been resolved!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "❌ File: " . $e->getFile() . "\n";
    echo "❌ Line: " . $e->getLine() . "\n";
}
