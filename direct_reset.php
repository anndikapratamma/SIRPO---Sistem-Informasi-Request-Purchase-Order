<?php

// Simple direct database update
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get database connection
$config = config('database.connections.' . config('database.default'));
$host = $config['host'];
$dbname = $config['database'];
$username = $config['username'];
$password = $config['password'];

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $today = date('Y-m-d');

    // Check if record exists
    $stmt = $pdo->prepare("SELECT * FROM pb_counters WHERE counter_date = ?");
    $stmt->execute([$today]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing record
        $stmt = $pdo->prepare("UPDATE pb_counters SET counter_value = 4589, updated_at = NOW() WHERE counter_date = ?");
        $stmt->execute([$today]);
        echo "✅ Counter updated to 4589\n";
    } else {
        // Insert new record
        $stmt = $pdo->prepare("INSERT INTO pb_counters (counter_date, counter_value, created_at, updated_at) VALUES (?, 4589, NOW(), NOW())");
        $stmt->execute([$today]);
        echo "✅ New counter created with value 4589\n";
    }

    // Verify
    $stmt = $pdo->prepare("SELECT counter_value FROM pb_counters WHERE counter_date = ?");
    $stmt->execute([$today]);
    $current = $stmt->fetchColumn();

    echo "Current counter value: {$current}\n";
    echo "Next PB will be: " . ($current + 1) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
