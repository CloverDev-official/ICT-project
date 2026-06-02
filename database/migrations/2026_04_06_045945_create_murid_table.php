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

            $table->string('nama', 191);
            $table->string('nipd', 191)->unique();
            $table->enum('jk', ['L', 'P']);
            $table->string('nisn', 191)->unique();

            $table->string('tempat_lahir', 191);
            $table->date('tanggal_lahir');

            $table->string('agama', 100)->nullable();

            $table->text('alamat')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('kelurahan', 191)->nullable();
            $table->string('kecamatan', 191)->nullable();
            $table->string('kode_pos', 20)->nullable();

            $table->string('hp', 30)->nullable();
            $table->string('email', 191)->nullable();

            $table->string('nama_ayah', 191)->nullable();
            $table->string('nama_ibu', 191)->nullable();
            $table->string('nama_wali', 191)->nullable();

            $table->string('image_path')->nullable();
            $table->foreignId('rombel_id')
                ->nullable()
                ->constrained('rombel')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->softDeletes();
            $table->timestamps();

            // Aman untuk MySQL lama: hindari composite index panjang berisi nama 255 char.
            $table->index(['ulid', 'rombel_id']);
            $table->index('nama');
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
