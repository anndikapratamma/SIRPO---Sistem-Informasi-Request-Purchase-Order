<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PbCounter;

echo "=== RESET COUNTER AGAR PB SELANJUTNYA JADI 4590 ===\n\n";

try {
    // Reset counter ke 4589 agar PB selanjutnya jadi 4590
    $nextPbNumber = PbCounter::resetToValue(4589);

    echo "✅ Counter berhasil direset ke 4589\n";
    echo "✅ PB selanjutnya akan mendapat nomor: {$nextPbNumber}\n\n";

    // Test generate nomor PB untuk memastikan
    echo "--- Test Generate PB Number ---\n";
    $pb1 = PbCounter::getNextNumber();
    echo "PB pertama setelah reset: {$pb1}\n";

    $pb2 = PbCounter::getNextNumber();
    echo "PB kedua: {$pb2}\n";

    $pb3 = PbCounter::getNextNumber();
    echo "PB ketiga: {$pb3}\n";

    echo "\n✅ Sistem sudah siap!\n";
    echo "✅ Nomor PB selanjutnya akan dimulai dari yang Anda inginkan!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
