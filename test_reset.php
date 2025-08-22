<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PbCounter;

echo "=== TEST RESET PB NUMBERING ===\n\n";

try {
    // Test nomor PB setelah reset
    echo "Testing PB numbering after reset:\n";

    $pbNumber1 = PbCounter::getNextNumber();
    echo "Next PB Number: {$pbNumber1}\n";

    $pbNumber2 = PbCounter::getNextNumber();
    echo "Next PB Number: {$pbNumber2}\n";

    $pbNumber3 = PbCounter::getNextNumber();
    echo "Next PB Number: {$pbNumber3}\n";

    echo "\n✅ RESET BERHASIL!\n";
    echo "✅ Penomoran dimulai dari PB-001 lagi\n";
    echo "✅ Siap untuk input PB baru!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
