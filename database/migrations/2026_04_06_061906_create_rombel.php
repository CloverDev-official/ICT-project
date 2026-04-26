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
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('indeks', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('rombel', function (Blueprint $table) {
            $table->id();
            $table->string('angkatan')->nullable();
            $table->foreignId('tingkat_id')->constrained('tingkat')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('indeks_id')->constrained('indeks')->cascadeOnDelete()->cascadeOnUpdate();
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
