<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $today = date('Y-m-d');

        // Reset counter untuk hari ini ke 4589 agar PB selanjutnya jadi 4590
        DB::table('pb_counters')
            ->updateOrInsert(
                ['counter_date' => $today],
                [
                    'counter_value' => 4589,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
    }

    public function down(): void
    {
        // No rollback needed
    }
};
