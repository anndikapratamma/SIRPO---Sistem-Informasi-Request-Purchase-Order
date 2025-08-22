<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\PbCounter;

echo "=== RESET COUNTER KE 4589 (PB SELANJUTNYA JADI 4590) ===\n\n";

try {
    $today = '2025-08-15';

    // Force update/insert counter
    $affected = DB::table('pb_counters')
        ->updateOrInsert(
            ['counter_date' => $today],
            [
                'counter_value' => 4589,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

    echo "✅ Counter berhasil direset ke 4589\n";
    echo "✅ PB selanjutnya akan mendapat nomor 4590\n";

    // Verifikasi
    $currentCounter = DB::table('pb_counters')
        ->where('counter_date', $today)
        ->value('counter_value');

    echo "\nVerifikasi:\n";
    echo "- Counter saat ini: {$currentCounter}\n";
    echo "- PB selanjutnya: " . ($currentCounter + 1) . "\n";

    // Test generate PB number
    echo "\n--- Test Generate PB ---\n";

    $nextPb1 = PbCounter::getNextNumber();
    echo "PB yang akan digenerate: {$nextPb1}\n";

    // Reset lagi ke 4589 untuk memastikan PB selanjutnya 4590
    DB::table('pb_counters')
        ->where('counter_date', $today)
        ->update(['counter_value' => 4589]);

    echo "\n✅ SELESAI! Counter sudah direset.\n";
    echo "✅ PB selanjutnya yang Anda buat akan bernomor 4590\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
