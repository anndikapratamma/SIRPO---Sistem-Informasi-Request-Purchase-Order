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
        Schema::table('pbs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('status')->default('pending')->after('divisi'); // pending, approved, rejected
            $table->string('no_pb')->nullable()->after('nomor_pb'); // untuk nomor PB yang lebih fleksibel
            $table->text('keperluan')->nullable()->after('keterangan'); // untuk detail keperluan

            // Add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Add index for better performance
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pbs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id']);
            $table->dropColumn(['user_id', 'status', 'no_pb', 'keperluan']);
        });
    }
};
