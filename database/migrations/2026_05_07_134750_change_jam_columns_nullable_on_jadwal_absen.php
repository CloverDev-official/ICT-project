<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_absen', function (Blueprint $table) {

            $table->time('jam_masuk')
                ->nullable()
                ->change();

            $table->time('jam_pulang')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_absen', function (Blueprint $table) {

            $table->time('jam_masuk')
                ->nullable(false)
                ->change();

            $table->time('jam_pulang')
                ->nullable(false)
                ->change();
        });
    }
};