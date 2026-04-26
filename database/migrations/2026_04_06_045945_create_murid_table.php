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
        Schema::create('murid', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();

            $table->string('nama');
            $table->string('nipd')->unique();
            $table->enum('jk', ['L', 'P']);
            $table->string('nisn')->unique();

            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');

            $table->string('agama')->nullable();

            $table->text('alamat')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();

            $table->string('hp')->nullable();
            $table->string('email')->nullable();

            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nama_wali')->nullable();

            $table->string('image_path')->nullable();
            $table->foreignId('rombel_id')
                ->nullable()
                ->constrained('rombel')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('murid');
    }
};
