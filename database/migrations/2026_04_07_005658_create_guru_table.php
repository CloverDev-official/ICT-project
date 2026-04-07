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
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('nama');
            $table->string('nuptk')->nullable()->unique();
            $table->enum('jk', ['L', 'P']);

            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            $table->string('nip')->nullable()->unique();

            $table->string('status_kepegawaian')->nullable();
            $table->string('jenis_ptk')->nullable();
            $table->string('agama')->nullable();

            $table->text('alamat_jalan')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kode_pos', 10)->nullable();

            $table->string('telepon')->nullable();
            $table->string('hp')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};
