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
            // Add only missing columns
            if (!Schema::hasColumn('pbs', 'input_date')) {
                $table->date('input_date')->nullable()->after('tanggal');
            }

            if (!Schema::hasColumn('pbs', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('pbs', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancelled_at');
            }

            if (!Schema::hasColumn('pbs', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('cancelled_by');
            }

            if (!Schema::hasColumn('pbs', 'file_path')) {
                $table->string('file_path')->nullable()->after('cancel_reason');
            }

            if (!Schema::hasColumn('pbs', 'file_name')) {
                $table->string('file_name')->nullable()->after('file_path');
            }

            if (!Schema::hasColumn('pbs', 'file_type')) {
                $table->string('file_type')->nullable()->after('file_name');
            }

            if (!Schema::hasColumn('pbs', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pbs', function (Blueprint $table) {
            $table->dropColumn([
                'input_date',
                'cancelled_at',
                'cancelled_by',
                'cancel_reason',
                'file_path',
                'file_name',
                'file_type',
                'file_size'
            ]);
        });
    }
};
