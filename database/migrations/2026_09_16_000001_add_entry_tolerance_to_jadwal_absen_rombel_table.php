<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_absen_rombel', function (Blueprint $table) {
            $table->time('scan_masuk_sampai_dasar')->nullable()->after('scan_masuk_sampai');
            $table->unsignedSmallInteger('toleransi_masuk')->nullable()->after('scan_masuk_sampai_dasar');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_absen_rombel', function (Blueprint $table) {
            $table->dropColumn(['scan_masuk_sampai_dasar', 'toleransi_masuk']);
        });
    }
};
