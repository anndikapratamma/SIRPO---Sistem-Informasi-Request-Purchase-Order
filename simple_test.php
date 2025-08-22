<?php
echo "Testing PB system...";

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PbCounter;

$nextPb = PbCounter::getNextNumber();
echo "Next PB: " . $nextPb;
