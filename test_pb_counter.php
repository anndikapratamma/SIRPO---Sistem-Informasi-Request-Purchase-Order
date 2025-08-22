<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PbCounter;

try {
    echo "Testing PbCounter model...\n";

    // Test getNextNumber method (tanpa parameter akan menggunakan tanggal hari ini)
    $nextNumber = PbCounter::getNextNumber();
    echo "Next PB Number: " . $nextNumber . "\n";

    // Test dengan tanggal spesifik
    $nextNumber2 = PbCounter::getNextNumber('2025-01-15');
    echo "Next PB Number for 2025-01-15: " . $nextNumber2 . "\n";

    echo "Test completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
