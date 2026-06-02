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
        Schema::create('tingkat', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 191)->unique();
            $table->timestamps();
        });

        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 191)->unique();
            $table->timestamps();
        });

        Schema::create('indeks', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 191)->unique();
            $table->timestamps();
        });

        Schema::create('rombel', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_masuk', 20)->nullable();
            $table->foreignId('tingkat_id')->constrained('tingkat')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('indeks_id')->constrained('indeks')->cascadeOnDelete()->cascadeOnUpdate();

            // Dibuat tanpa constraint dulu karena tabel guru dibuat setelah rombel.
            // Foreign key-nya ditambahkan di migration create_guru_table setelah tabel guru tersedia.
            $table->foreignId('wali_guru_id')->nullable()->index();

            $table->timestamps();

            $table->unique(['tingkat_id', 'jurusan_id', 'indeks_id']);
            $table->index(['tingkat_id', 'jurusan_id', 'indeks_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombel');
        Schema::dropIfExists('indeks');
        Schema::dropIfExists('jurusan');
        Schema::dropIfExists('tingkat');
    }
};
