<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsenMuridSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        $statuses = array_map(
            static fn (AttendanceStatus $status): string => $status->value,
            AttendanceStatus::editable(),
        );
        $totalHari = 7;
        $insertChunk = 100;
        $now = now()->toDateTimeString();

        // hitung total murid
        $totalMurid = DB::table('murid')->count();
        $totalData = $totalMurid * $totalHari;

        $processed = 0;

        echo "Total data: {$totalData}\n";

        DB::table('murid')
            ->orderBy('id')
            ->chunkById(500, function ($murids) use (
                $statuses,
                $totalHari,
                $insertChunk,
                $now,
                $totalData,
                &$processed,
            ) {
                $batch = [];

                foreach ($murids as $mIndex => $murid) {
                    for ($d = 0; $d < $totalHari; $d++) {
                        $tanggal = date('Y-m-d', strtotime("-$d days"));
                        $status = $statuses[($mIndex + $d) % 4];

                        $waktuMasuk = null;
                        $waktuKeluar = null;
                        $keterangan = null;

                        if ($status === AttendanceStatus::Hadir->value) {
                            $waktuMasuk = sprintf(
                                '%02d:%02d:%02d',
                                rand(7, 8),
                                rand(0, 59),
                                rand(0, 59),
                            );
                            $waktuKeluar = sprintf(
                                '%02d:%02d:%02d',
                                rand(14, 16),
                                rand(0, 59),
                                rand(0, 59),
                            );
                        } else {
                            $keterangan = $status;
                        }

                        $batch[] = [
                            'murid_id' => $murid->id,
                            'status' => $status,
                            'waktu_masuk' => $waktuMasuk,
                            'waktu_keluar' => $waktuKeluar,
                            'keterangan' => $keterangan,
                            'tanggal' => $tanggal,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        $processed++;

                        // insert per chunk
                        if (count($batch) >= $insertChunk) {
                            DB::table('absen_murid')->insert($batch);
                            $batch = [];

                            // tampilkan progress
                            $percent = number_format(
                                ($processed / $totalData) * 100,
                                2,
                            );
                            echo "Progress: {$processed}/{$totalData} ({$percent}%)\r";
                        }
                    }
                }

                if (! empty($batch)) {
                    DB::table('absen_murid')->insert($batch);

                    $percent = number_format(
                        ($processed / $totalData) * 100,
                        2,
                    );
                    echo "Progress: {$processed}/{$totalData} ({$percent}%)\r";
                }
            });

        echo "\nSelesai! Total: {$processed}\n";
    }
}
