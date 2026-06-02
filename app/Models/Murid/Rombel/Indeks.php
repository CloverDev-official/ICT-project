<?php

namespace App\Models\Murid\Rombel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Indeks extends Model
{
    protected $table = 'indeks';

    protected $fillable = ['nama'];

    public function rombel()
    {
        return $this->hasMany(Rombel::class);
    }

    public static function options(): Collection
    {
        $indeks = self::query()->orderBy('nama')->get(['id', 'nama']);

        if ($indeks->isNotEmpty()) {
            return $indeks;
        }

        return collect(range('A', 'Z'))->map(function (string $nama) {
            return (object) [
                'id' => $nama,
                'nama' => $nama,
            ];
        });
    }

    public static function resolveSelection(int|string|null $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return self::query()->find((int) $value);
        }

        $nama = strtoupper(trim((string) $value));

        $existing = self::query()
            ->whereRaw('UPPER(nama) = ?', [$nama])
            ->first();

        if ($existing instanceof self) {
            return $existing;
        }

        return self::query()->firstOrCreate([
            'nama' => $nama
        ]);
    }
}