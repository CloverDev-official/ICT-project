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
        if (Schema::hasColumn('jadwal_absen_rombel', 'gunakan_window_scan')) {
            return;
        }

        Schema::table('jadwal_absen_rombel', function (Blueprint $table) {
            $table->boolean('gunakan_window_scan')->default(false)->after('jam_pulang');
            $table->time('scan_masuk_mulai')->nullable()->after('gunakan_window_scan');
            $table->time('scan_masuk_sampai')->nullable()->after('scan_masuk_mulai');
            $table->time('scan_keluar_mulai')->nullable()->after('scan_masuk_sampai');
            $table->time('scan_keluar_sampai')->nullable()->after('scan_keluar_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('jadwal_absen_rombel', 'gunakan_window_scan')) {
            return;
        }

        Schema::table('jadwal_absen_rombel', function (Blueprint $table) {
            $table->dropColumn([
                'gunakan_window_scan',
                'scan_masuk_mulai',
                'scan_masuk_sampai',
                'scan_keluar_mulai',
                'scan_keluar_sampai',
            ]);
        });
    }
};
