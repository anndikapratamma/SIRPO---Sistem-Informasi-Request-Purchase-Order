<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PbCounter;

echo "=== Testing PB Counter dengan nilai 4590 ===\n";

try {
    $nomorPb1 = PbCounter::getNextNumber();
    echo "PB pertama: " . $nomorPb1 . "\n";

    $nomorPb2 = PbCounter::getNextNumber();
    echo "PB kedua: " . $nomorPb2 . "\n";

    $nomorPb3 = PbCounter::getNextNumber();
    echo "PB ketiga: " . $nomorPb3 . "\n";

    echo "\n✅ Counter berhasil dimulai dari 4590!\n";
    echo "✅ Siap untuk input PB dengan nomor dimulai dari 4590!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
