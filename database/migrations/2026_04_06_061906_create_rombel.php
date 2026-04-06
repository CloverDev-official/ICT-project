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
        Schema::create('tingkat_kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('indeks_rombel', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('rombel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tingkat_kelas_id')->constrained('tingkat_kelas')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('indeks_rombel_id')->constrained('indeks_rombel')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['tingkat_kelas_id', 'jurusan_id', 'indeks_rombel_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombel');
        Schema::dropIfExists('indeks_rombel');
        Schema::dropIfExists('jurusan');
        Schema::dropIfExists('tingkat_kelas');
    }
};
