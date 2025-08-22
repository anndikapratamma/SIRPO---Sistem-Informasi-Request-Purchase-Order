<?php
echo "Testing counter system...\n";

// Manual SQL update
$mysqli = new mysqli('localhost', 'root', '', 'sirpo');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$today = date('Y-m-d');

// Update counter to 4589
$sql = "UPDATE pb_counters SET counter_value = 4589 WHERE counter_date = '$today'";
if ($mysqli->query($sql)) {
    echo "✅ Counter reset to 4589\n";
} else {
    echo "❌ Error: " . $mysqli->error . "\n";
}

// Check current value
$result = $mysqli->query("SELECT counter_value FROM pb_counters WHERE counter_date = '$today'");
if ($result && $row = $result->fetch_assoc()) {
    echo "Current counter: " . $row['counter_value'] . "\n";
    echo "Next PB will be: " . ($row['counter_value'] + 1) . "\n";
}

$mysqli->close();
echo "Done!\n";
