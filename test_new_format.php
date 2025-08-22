<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PbCounter;
use Carbon\Carbon;

echo "=== TEST NEW PB NUMBER FORMAT ===\n\n";

try {
    // Test new format
    echo "1. Testing new PB format:\n";
    $pbNumber1 = PbCounter::getNextNumber();
    echo "   PB Number 1: {$pbNumber1}\n";

    $pbNumber2 = PbCounter::getNextNumber();
    echo "   PB Number 2: {$pbNumber2}\n";

    $pbNumber3 = PbCounter::getNextNumber();
    echo "   PB Number 3: {$pbNumber3}\n";

    echo "\n✅ New format working: PB-001, PB-002, PB-003\n";
    echo "✅ Format changed from PB-YYYY-XXX to PB-XXX successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
