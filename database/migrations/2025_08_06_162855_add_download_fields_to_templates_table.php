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
        Schema::table('templates', function (Blueprint $table) {
            $table->string('original_name')->nullable()->after('original_filename');
            $table->bigInteger('file_size')->nullable()->after('original_name');
            $table->integer('download_count')->default(0)->after('file_size');
            $table->timestamp('last_downloaded')->nullable()->after('download_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['original_name', 'file_size', 'download_count', 'last_downloaded']);
        });
    }
};
