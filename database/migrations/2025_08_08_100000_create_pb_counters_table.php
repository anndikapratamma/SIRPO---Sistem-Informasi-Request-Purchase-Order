<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('pb_counters')) {
            Schema::create('pb_counters', function (Blueprint $table) {
                $table->id();
                $table->date('counter_date');
                $table->integer('counter_value')->default(0);
                $table->timestamps();
                $table->index('counter_date');
                $table->unique('counter_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pb_counters');
    }
};
