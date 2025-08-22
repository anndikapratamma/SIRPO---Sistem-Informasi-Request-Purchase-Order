<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePbsTable extends Migration
{
    public function up(): void
    {
        Schema::create('pbs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pb')->unique(); // nomor auto generate
            $table->date('tanggal');
            $table->string('penginput');
            $table->decimal('nominal', 20, 2);
            $table->string('keterangan')->nullable();
            $table->string('divisi'); // contoh: OP, AKT
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pbs');
    }
}
