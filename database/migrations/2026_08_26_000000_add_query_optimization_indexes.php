<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes for existing query predicates without changing any data,
     * ordering, pagination, or application output.
     */
    public function up(): void
    {
        Schema::table('role', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->index(['user_id', 'deleted_at']);
            $table->index(['status_kepegawaian', 'jenis_ptk', 'deleted_at']);
        });

        Schema::table('rombel', function (Blueprint $table) {
            $table->index(['wali_guru_id', 'deleted_at']);
        });

        Schema::table('murid', function (Blueprint $table) {
            $table->index(['rombel_id', 'status', 'deleted_at']);
        });

        Schema::table('absen_murid', function (Blueprint $table) {
            $table->index(['tanggal', 'status', 'deleted_at']);
        });

        Schema::table('absen_guru', function (Blueprint $table) {
            $table->index(['guru_id', 'tanggal', 'deleted_at']);
            $table->index(['tanggal', 'status', 'deleted_at']);
        });

        Schema::table('izin_murid', function (Blueprint $table) {
            $table->index(['status', 'tanggal', 'deleted_at']);
        });
    }

    /**
     * Reverse only the indexes introduced by this migration.
     */
    public function down(): void
    {
        Schema::table('izin_murid', function (Blueprint $table) {
            $table->dropIndex(['status', 'tanggal', 'deleted_at']);
        });

        Schema::table('absen_guru', function (Blueprint $table) {
            $table->dropIndex(['guru_id', 'tanggal', 'deleted_at']);
            $table->dropIndex(['tanggal', 'status', 'deleted_at']);
        });

        Schema::table('absen_murid', function (Blueprint $table) {
            $table->dropIndex(['tanggal', 'status', 'deleted_at']);
        });

        Schema::table('murid', function (Blueprint $table) {
            $table->dropIndex(['rombel_id', 'status', 'deleted_at']);
        });

        Schema::table('rombel', function (Blueprint $table) {
            $table->dropIndex(['wali_guru_id', 'deleted_at']);
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'deleted_at']);
            $table->dropIndex(['status_kepegawaian', 'jenis_ptk', 'deleted_at']);
        });

        Schema::table('role', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};
