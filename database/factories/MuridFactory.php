<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MuridFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'nipd' => fake()->unique()->numberBetween(1000,9999),
            'nisn' => fake()->unique()->numerify('##########'),
            'jk' => fake()->randomElement(['Laki-laki','Perempuan']),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->date(),
            'agama' => fake()->randomElement(['Islam','Kristen','Hindu','Budha']),
            'alamat' => fake()->address(),
            'rt' => fake()->numberBetween(1,10),
            'rw' => fake()->numberBetween(1,10),
            'kelurahan' => fake()->citySuffix(),
            'kecamatan' => fake()->city(),
            'kelas' => fake()->randomElement([
                'X PPLG A',
                'X PPLG B',
                'XI PPLG A',
                'XI PPLG B',
                'XII PPLG A'
            ]),
            'hp' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'ayah' => fake()->name('male'),
            'ibu' => fake()->name('female'),
            'wali' => fake()->name()
        ];
    }
}