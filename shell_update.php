<?php

echo "=== UPDATING PB COUNTER ===\n";

// Command untuk update database
$command = 'mysql -u root sirpo -e "UPDATE pb_counters SET counter_value = 4589 WHERE counter_date = \'2025-08-15\'"';

echo "Executing: $command\n";

$output = shell_exec($command);

if ($output === null) {
    echo "✅ Command executed successfully!\n";
    echo "✅ Counter reset to 4589\n";
    echo "✅ Next PB will be 4590\n";
} else {
    echo "Output: $output\n";
}

// Verifikasi
$verify = 'mysql -u root sirpo -e "SELECT counter_value FROM pb_counters WHERE counter_date = \'2025-08-15\'"';
$result = shell_exec($verify);

echo "\nVerifikasi:\n";
echo $result;
echo "\n✅ SELESAI! Silakan coba buat PB baru.\n";
