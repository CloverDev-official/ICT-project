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
        Schema::create('jadwal_absen', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal')->unique();

            $table->time('jam_masuk');
            $table->time('jam_pulang');

            $table->string('tipe')->nullable(); // normal / khusus / libur / custom
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_absen');
    }
};
