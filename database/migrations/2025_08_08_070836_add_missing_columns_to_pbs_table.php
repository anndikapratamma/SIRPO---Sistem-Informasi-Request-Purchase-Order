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
        if (Schema::hasTable('pbs')) {
            Schema::table('pbs', function (Blueprint $table) {
                // Add missing columns if they don't exist
                if (!Schema::hasColumn('pbs', 'file_path')) {
                    $table->string('file_path')->nullable();
                }
                if (!Schema::hasColumn('pbs', 'file_name')) {
                    $table->string('file_name')->nullable();
                }
                if (!Schema::hasColumn('pbs', 'file_type')) {
                    $table->string('file_type')->nullable();
                }
                if (!Schema::hasColumn('pbs', 'file_size')) {
                    $table->integer('file_size')->nullable();
                }
                if (!Schema::hasColumn('pbs', 'cancelled_at')) {
                    $table->datetime('cancelled_at')->nullable();
                }
                if (!Schema::hasColumn('pbs', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable();
                }
                if (!Schema::hasColumn('pbs', 'cancel_reason')) {
                    $table->text('cancel_reason')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pbs')) {
            Schema::table('pbs', function (Blueprint $table) {
                $table->dropColumn([
                    'file_path',
                    'file_name',
                    'file_type',
                    'file_size',
                    'cancelled_at',
                    'cancelled_by',
                    'cancel_reason'
                ]);
            });
        }
    }
};
