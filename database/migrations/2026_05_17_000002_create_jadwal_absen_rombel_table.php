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
        Schema::create('jadwal_absen_rombel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_absen_id')
                ->constrained('jadwal_absen')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('rombel_id')
                ->constrained('rombel')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('tipe', 50)->default('normal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_absen_id', 'rombel_id']);
            $table->index(['rombel_id', 'tipe']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_absen_rombel');
    }
};
