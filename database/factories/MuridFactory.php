<?php

namespace Database\Factories;

use App\Models\Murid\Murid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Murid>
 */
class MuridFactory extends Factory
{
    protected $model = Murid::class;

    public function definition(): array
    {
        $jk = fake()->randomElement(['L', 'P']);

        return [
            'public_id' => (string) Str::ulid(),

            'nama' =>
                $jk === 'L' ? fake()->name('male') : fake()->name('female'),
            'nipd' => fake()->unique()->numerify('##########'),
            'jk' => $jk,
            'nisn' => fake()->unique()->numerify('##########'),

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
                ? fake()->unique()->safeEmail()
                : null,

            'nama_ayah' => fake()->optional(0.2)->name('male'),
            'nama_ibu' => fake()->optional(0.2)->name('female'),
            'nama_wali' => fake()->optional(0.6)->name(),

            'rombel_id' => null,
        ];
    }
}
