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
        Schema::create('absen_murid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('murid')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa']);
            $table->string('keterangan', 191)->nullable();
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['murid_id', 'tanggal']);
        });

        Schema::create('absen_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa']);
            $table->string('keterangan', 191)->nullable();
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absen_murid');
        Schema::dropIfExists('absen_guru');
    }
};
