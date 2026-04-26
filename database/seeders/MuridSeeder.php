<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MuridSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('PRAGMA synchronous = OFF');
        DB::statement('PRAGMA cache_size = -64000');
        DB::statement('PRAGMA temp_store = MEMORY');
        DB::statement('PRAGMA mmap_size = 268435456');

        $rombelIds   = DB::table('rombel')->pluck('id')->toArray();
        $rombelCount = count($rombelIds);

        if ($rombelCount === 0) {
            throw new \RuntimeException('Tabel rombel kosong! Jalankan RombelSeeder dulu.');
        }

        $images     = $this->loadImages();
        $imageCount = count($images) ?: 1;

        $POOL       = 2000;
        $maleNames  = array_map(fn() => fake()->name('male'),    range(1, $POOL));
        $femaleNames= array_map(fn() => fake()->name('female'),  range(1, $POOL));
        $cities     = array_map(fn() => fake()->city(),          range(1, $POOL));
        $streets    = array_map(fn() => fake()->streetAddress(), range(1, 500));
        $emails     = array_map(fn() => fake()->safeEmail(),     range(1, $POOL));
        $agamaPool  = ['Islam','Kristen','Katolik','Hindu','Budha','Konghucu',null,null,null,null,null,null];
        $agamaCount = count($agamaPool);

        $total = 10_000;
        $chunk = 1_000;
        $now   = now()->toDateTimeString();
        $batch = [];

        DB::disableQueryLog();

        DB::beginTransaction();

        try {
            for ($i = 0; $i < $total; $i++) {
                $idx = $i % $POOL;
                $jk  = ($i & 1) === 0 ? 'L' : 'P';

                $batch[] = [
                    'ulid'         => (string) Str::ulid(),
                    'nama'         => $jk === 'L' ? $maleNames[$idx] : $femaleNames[$idx],
                    'jk'           => $jk,
                    'nipd'         => str_pad($i + 1, 10, '0', STR_PAD_LEFT),
                    'nisn'         => str_pad($i + 500_001, 10, '0', STR_PAD_LEFT),
                    'tempat_lahir' => $cities[$idx],
                    'tanggal_lahir'=> date('Y-m-d', mktime(0,0,0,1,1, rand(2006, 2010))),
                    'agama'        => ($i % 7 === 0) ? $agamaPool[$idx % $agamaCount] : null,
                    'alamat'       => ($i % 5 === 0) ? $streets[$i % 500] : null,
                    'rt'           => ($i % 5 === 0) ? str_pad(rand(1,20), 2,'0',STR_PAD_LEFT) : null,
                    'rw'           => ($i % 5 === 0) ? str_pad(rand(1,10), 2,'0',STR_PAD_LEFT) : null,
                    'kelurahan'    => ($i % 5 === 0) ? $cities[($idx + 1) % $POOL] : null,
                    'kecamatan'    => ($i % 5 === 0) ? $cities[($idx + 3) % $POOL] : null,
                    'hp'           => ($i % 4 === 0) ? '08'.rand(100_000_000, 999_999_999) : null,
                    'email'        => ($i % 2 === 0) ? $emails[$idx] : null,
                    'nama_ayah'    => ($i % 5 === 0) ? $maleNames[($idx + 7) % $POOL] : null,
                    'nama_ibu'     => ($i % 5 === 0) ? $femaleNames[($idx + 13) % $POOL] : null,
                    'nama_wali'    => ($i % 2 === 0) ? $maleNames[($idx + 5) % $POOL] : null,
                    'image_path'   => $imageCount > 1 ? $images[$i % $imageCount] : ($images[0] ?? null),
                    'rombel_id'    => $rombelIds[$i % $rombelCount],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];

                if (count($batch) === $chunk) {
                    DB::table('murid')->insert($batch);
                    $batch = [];
                    echo "Inserted: " . ($i + 1) . "\n";
                }
            }

            if (!empty($batch)) {
                DB::table('murid')->insert($batch);
            }

            DB::commit();
            echo "Selesai! $total records inserted.\n";

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function loadImages(): array
    {
        $path = storage_path('app/public/animeFaces/images');

        if (!is_dir($path)) {
            return ['storage/animeFaces/images/default.jpg'];
        }

        $files  = array_diff(scandir($path), ['.', '..']);
        $files  = array_filter($files, fn($f) => preg_match('/\.(jpg|jpeg|png|webp)$/i', $f));
        $images = array_values(array_map(fn($f) => 'storage/animeFaces/images/' . $f, $files));

        shuffle($images);
        return $images;
    }
}