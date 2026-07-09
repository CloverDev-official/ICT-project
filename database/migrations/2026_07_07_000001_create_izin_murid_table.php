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
        Schema::create('izin_murid', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('murid_id')
                ->constrained('murid')
                ->cascadeOnUpdate();
                // ->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('alasan');
            $table->time('dari_jam');
            $table->time('sampai_jam')->nullable();
            $table->string('status')->default('Izin');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['murid_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_murid');
    }
};
