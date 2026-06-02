<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('nama', 191);
            $table->string('nuptk', 191)->nullable()->unique();
            $table->enum('jk', ['L', 'P']);

            $table->string('tempat_lahir', 191)->nullable();
            $table->date('tanggal_lahir')->nullable();

            $table->string('nip', 191)->nullable()->unique();

            $table->string('status_kepegawaian', 191)->nullable();
            $table->string('jenis_ptk', 191)->nullable();
            $table->string('agama', 100)->nullable();

            $table->text('alamat_jalan')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('desa_kelurahan', 191)->nullable();
            $table->string('kecamatan', 191)->nullable();
            $table->string('kode_pos', 10)->nullable();

            $table->string('telepon', 30)->nullable();
            $table->string('hp', 30)->nullable();
            $table->string('email', 191)->nullable()->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('guru_rombel', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('guru_id')
                ->constrained('guru')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table
                ->foreignId('rombel_id')
                ->constrained('rombel')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();

            $table->unique(['guru_id', 'rombel_id']);
        });

        Schema::table('rombel', function (Blueprint $table) {
            $table->foreign('wali_guru_id')
                ->references('id')
                ->on('guru')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_rombel');

        if (Schema::hasTable('rombel')) {
            Schema::table('rombel', function (Blueprint $table) {
                $table->dropForeign(['wali_guru_id']);
            });
        }

        Schema::dropIfExists('guru');
    }
};
