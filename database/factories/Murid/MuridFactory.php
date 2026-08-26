<?php

namespace Database\Factories\Murid;

use App\Enums\StudentStatus;
use App\Models\Murid\Murid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MuridFactory extends Factory
{
    protected $model = Murid::class;

    protected static $images = null;

    protected static $total = 0;

    protected static $index = 0;

    protected static $counter = 1;

    protected function loadImages()
    {
        $path = storage_path('app/public/animeFaces/images');

        if (! is_dir($path)) {
            throw new \Exception('Folder tidak ditemukan: '.$path);
        }
        $files = array_diff(scandir($path), ['.', '..']);

        $files = array_filter($files, function ($f) {
            return preg_match('/\.(jpg|jpeg|png|webp)$/i', $f);
        });

        self::$images = array_values(array_map(
            fn ($f) => 'storage/animeFaces/images/'.$f,
            $files
        ));

        shuffle(self::$images);

        self::$total = count(self::$images);
    }

    protected function getFastImage()
    {
        if (self::$images === null) {
            $this->loadImages();
        }

        $img = self::$images[self::$index];

        self::$index++;

        if (self::$index >= self::$total) {
            self::$index = 0;

            shuffle(self::$images);
        }

        return $img;
    }

    public function definition(): array
    {
        $jk = fake()->randomElement(['L', 'P']);

        self::$counter++;

        return [
            'uuid' => (string) Str::uuid(),

            'nama' => $jk === 'L' ? fake()->name('male') : fake()->name('female'),

            'jk' => $jk,
            'nipd' => str_pad(self::$counter, 10, '0', STR_PAD_LEFT),
            'nisn' => str_pad(self::$counter + 500000, 10, '0', STR_PAD_LEFT),

            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()
                ->dateTimeBetween('-18 years', '-14 years')
                ->format('Y-m-d'),

            'agama' => fake()
                ->optional(0.15)
                ->randomElement([
                    'Islam',
                    'Kristen',
                    'Katolik',
                    'Hindu',
                    'Budha',
                    'Konghucu',
                ]),

            'alamat' => fake()->optional(0.2)->streetAddress(),
            'rt' => fake()->optional(0.2)->numerify('##'),
            'rw' => fake()->optional(0.2)->numerify('##'),
            'kelurahan' => fake()->optional(0.2)->city(),
            'kecamatan' => fake()->optional(0.2)->city(),

            'hp' => fake()->optional(0.25)->numerify('08##########'),
            'email' => fake()->boolean(60)
                ? fake()->safeEmail()
                : null,

            'nama_ayah' => fake()->optional(0.2)->name('male'),
            'nama_ibu' => fake()->optional(0.2)->name('female'),
            'nama_wali' => fake()->optional(0.6)->name(),

            'image_path' => $this->getFastImage(),

            'rombel_id' => null,
            'status' => StudentStatus::Aktif->value,
        ];
    }
}
