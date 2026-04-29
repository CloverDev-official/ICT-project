<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsenMuridSeeder extends Seeder
{
    public function run(): void
    {
        // Optimasi SQLite (skip kalau MySQL)
        DB::statement('PRAGMA synchronous = OFF');
        DB::statement('PRAGMA cache_size = -64000');
        DB::statement('PRAGMA temp_store = MEMORY');
        DB::statement('PRAGMA mmap_size = 268435456');

        DB::disableQueryLog();

        $muridIds = DB::table('murid')->pluck('id')->toArray();
        $muridCount = count($muridIds);

        if ($muridCount === 0) {
            throw new \RuntimeException('Tabel murid kosong! Jalankan MuridSeeder dulu.');
        }

        $statuses = ['Hadir', 'Sakit', 'Izin', 'Alpa'];

        $totalHari = 7; // jumlah hari absen
        $chunk     = 2000;
        $batch     = [];
        $now       = now()->toDateTimeString();

        DB::beginTransaction();

        try {
            foreach ($muridIds as $mIndex => $muridId) {

                for ($d = 0; $d < $totalHari; $d++) {

                    $tanggal = date('Y-m-d', strtotime("-$d days"));
                    $status  = $statuses[($mIndex + $d) % 4];

                    $waktuMasuk  = null;
                    $waktuKeluar = null;
                    $keterangan  = null;

                    if ($status === 'Hadir') {
                        $waktuMasuk  = sprintf('%02d:%02d:%02d', rand(7,8), rand(0,59), rand(0,59));
                        $waktuKeluar = sprintf('%02d:%02d:%02d', rand(14,16), rand(0,59), rand(0,59));
                    } else {
                        $keterangan = $status;
                    }

                    $batch[] = [
                        'murid_id'     => $muridId,
                        'status'       => $status,
                        'waktu_masuk'  => $waktuMasuk,
                        'waktu_keluar' => $waktuKeluar,
                        'keterangan'   => $keterangan,
                        'tanggal'      => $tanggal,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];

                    if (count($batch) === $chunk) {
                        DB::table('absen_murid')->insert($batch);
                        $batch = [];
                        echo "Inserted batch...\n";
                    }
                }
            }

            if (!empty($batch)) {
                DB::table('absen_murid')->insert($batch);
            }

            DB::commit();

            echo "Selesai! Total data: " . ($muridCount * $totalHari) . "\n";

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}