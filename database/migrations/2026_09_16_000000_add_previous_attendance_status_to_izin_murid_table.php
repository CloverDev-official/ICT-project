<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('izin_murid', function (Blueprint $table) {
            $table->string('status_absensi_sebelumnya', 191)
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('izin_murid', function (Blueprint $table) {
            $table->dropColumn('status_absensi_sebelumnya');
        });
    }
};
