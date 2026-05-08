<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalAbsen;
use Carbon\Carbon;

class JadwalAbsenSeeder extends Seeder
{
    public function run(): void
    {
        $start = Carbon::now()->startOfYear(); // awal tahun
        $end = Carbon::now()->endOfYear();     // akhir tahun

        while ($start <= $end) {

            $day = $start->dayOfWeekIso; // 1=Senin, 7=Minggu

            // SABTU & MINGGU
            if ($day >= 6) {
                JadwalAbsen::updateOrCreate(
                    ['tanggal' => $start->format('Y-m-d')],
                    [
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'tipe' => 'libur',
                        'keterangan' => 'Hari Libur'
                    ]
                );
            }

            // JUMAT
            elseif ($day == 5) {
                JadwalAbsen::updateOrCreate(
                    ['tanggal' => $start->format('Y-m-d')],
                    [
                        'jam_masuk' => '06:30',
                        'jam_pulang' => '11:30',
                        'tipe' => 'khusus',
                        'keterangan' => 'Jadwal Jumat'
                    ]
                );
            }

            // SENIN - KAMIS
            else {
                JadwalAbsen::updateOrCreate(
                    ['tanggal' => $start->format('Y-m-d')],
                    [
                        'jam_masuk' => '06:30',
                        'jam_pulang' => '16:30',
                        'tipe' => 'normal',
                        'keterangan' => 'Jadwal Normal'
                    ]
                );
            }

            $start->addDay();
        }
    }
}