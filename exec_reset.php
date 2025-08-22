<?php

echo "Executing counter reset...\n";

// Change to project directory and run the script
$command = 'cd C:\project\SIRPO && php direct_update.php';

echo "Command: $command\n\n";

// Execute and capture output
exec($command, $output, $return_code);

// Display output
foreach ($output as $line) {
    echo $line . "\n";
}

echo "\nReturn code: $return_code\n";

if ($return_code === 0) {
    echo "✅ Script executed successfully!\n";
} else {
    echo "❌ Script execution failed!\n";
}
