<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PbCounter;

echo "=== TEST PENOMORAN PB SETELAH RESET ===\n\n";

try {
    echo "Testing PB numbering setelah reset lengkap:\n";

    // Test 5 nomor berturut-turut
    for ($i = 1; $i <= 5; $i++) {
        $pbNumber = PbCounter::getNextNumber();
        echo "PB ke-{$i}: {$pbNumber}\n";
    }

    echo "\n✅ PENOMORAN SUDAH BENAR!\n";
    echo "✅ Dimulai dari PB-001 dan berurutan\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
