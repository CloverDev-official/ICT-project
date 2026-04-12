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
        Schema::create('role_guru_romble', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_rombel_id')->constrained('guru_rombel')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('role_id')->constrained('role')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_guru_romble');
    }
};
