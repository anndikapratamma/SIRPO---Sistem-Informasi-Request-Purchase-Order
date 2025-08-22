<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

try {
    $app = require_once 'bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    echo "Reset counter script started...\n";

    $pdo = DB::connection()->getPdo();
    $today = date('Y-m-d');

    // Update counter value
    $sql = "UPDATE pb_counters SET counter_value = 4589, updated_at = NOW() WHERE counter_date = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$today]);

    if ($result) {
        echo "Success! Counter reset to 4589\n";
        echo "Next PB will be 4590\n";
    } else {
        echo "Failed to update counter\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Script completed.\n";
