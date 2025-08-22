<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PbCounter;

$nextNumber = PbCounter::getNextNumber();
echo "Next PB Number: " . $nextNumber . "\n";
