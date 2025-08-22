<?php

echo "=== RESET PB COUNTER TO 4589 ===\n\n";

// Multiple approaches to ensure success
$success = false;

// Approach 1: PDO with error handling
try {
    $pdo = new PDO("mysql:host=localhost;dbname=sirpo;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $today = '2025-08-15';

    // Check if record exists
    $check = $pdo->prepare("SELECT counter_value FROM pb_counters WHERE counter_date = ?");
    $check->execute([$today]);
    $current = $check->fetchColumn();

    if ($current !== false) {
        echo "Current counter value: $current\n";

        // Update existing record
        $update = $pdo->prepare("UPDATE pb_counters SET counter_value = 4589, updated_at = NOW() WHERE counter_date = ?");
        $success = $update->execute([$today]);

        if ($success) {
            echo "✅ Counter updated to 4589\n";
        }
    } else {
        // Insert new record
        $insert = $pdo->prepare("INSERT INTO pb_counters (counter_date, counter_value, created_at, updated_at) VALUES (?, 4589, NOW(), NOW())");
        $success = $insert->execute([$today]);

        if ($success) {
            echo "✅ New counter created with value 4589\n";
        }
    }

    // Verify the change
    $verify = $pdo->prepare("SELECT counter_value FROM pb_counters WHERE counter_date = ?");
    $verify->execute([$today]);
    $newValue = $verify->fetchColumn();

    echo "✅ Verified counter value: $newValue\n";
    echo "✅ Next PB will be: " . ($newValue + 1) . "\n";

} catch (Exception $e) {
    echo "PDO Error: " . $e->getMessage() . "\n";

    // Approach 2: mysqli as fallback
    try {
        $mysqli = new mysqli('localhost', 'root', '', 'sirpo');

        if ($mysqli->connect_error) {
            throw new Exception('Connection failed: ' . $mysqli->connect_error);
        }

        $sql = "UPDATE pb_counters SET counter_value = 4589, updated_at = NOW() WHERE counter_date = '2025-08-15'";

        if ($mysqli->query($sql)) {
            echo "✅ Counter updated via mysqli\n";
            $success = true;

            // Verify
            $result = $mysqli->query("SELECT counter_value FROM pb_counters WHERE counter_date = '2025-08-15'");
            if ($result && $row = $result->fetch_assoc()) {
                echo "✅ Verified counter: " . $row['counter_value'] . "\n";
                echo "✅ Next PB will be: " . ($row['counter_value'] + 1) . "\n";
            }
        } else {
            echo "❌ mysqli Error: " . $mysqli->error . "\n";
        }

        $mysqli->close();

    } catch (Exception $e2) {
        echo "mysqli Error: " . $e2->getMessage() . "\n";
    }
}

if ($success) {
    echo "\n🎉 SUCCESS! Counter has been reset.\n";
    echo "🎉 You can now create PB and it will start from 4590!\n";
} else {
    echo "\n❌ Failed to update counter. Please try manual update.\n";
}

echo "\n--- Script completed ---\n";
