<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Dropping pb_counters table...\n";
    Schema::dropIfExists('pb_counters');

    echo "Creating pb_counters table...\n";
    Schema::create('pb_counters', function ($table) {
        $table->id();
        $table->date('counter_date');
        $table->integer('counter_value')->default(0);
        $table->timestamps();

        $table->index('counter_date');
        $table->unique('counter_date');
    });

    echo "Table pb_counters created successfully!\n";

    // Test insert
    DB::table('pb_counters')->insert([
        'counter_date' => '2025-01-15',
        'counter_value' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "Test record inserted successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
