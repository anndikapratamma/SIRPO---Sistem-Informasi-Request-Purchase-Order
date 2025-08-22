<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if pb_counters table exists, if not create it
        if (!Schema::hasTable('pb_counters')) {
            Schema::create('pb_counters', function (Blueprint $table) {
                $table->id();
                $table->date('counter_date');
                $table->integer('counter_value')->default(0);
                $table->timestamps();
                $table->index('counter_date');
                $table->unique('counter_date');
            });
        } else {
            // Add counter_value column if it doesn't exist
            if (!Schema::hasColumn('pb_counters', 'counter_value')) {
                Schema::table('pb_counters', function (Blueprint $table) {
                    $table->integer('counter_value')->default(0)->after('counter_date');
                });
            }
        }

        // Insert initial counter for today starting from 4590
        $today = date('Y-m-d');
        $existingCounter = DB::table('pb_counters')->where('counter_date', $today)->first();

        if (!$existingCounter) {
            DB::table('pb_counters')->insert([
                'counter_date' => $today,
                'counter_value' => 4590,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            // Update existing counter to start from 4590 if it's lower
            if ($existingCounter->counter_value < 4590) {
                DB::table('pb_counters')
                    ->where('counter_date', $today)
                    ->update(['counter_value' => 4590, 'updated_at' => now()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pb_counters', 'counter_value')) {
            Schema::table('pb_counters', function (Blueprint $table) {
                $table->dropColumn('counter_value');
            });
        }
    }
};
