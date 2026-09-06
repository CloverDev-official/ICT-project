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
        return $this->hasMany(Rombel::class)->withTrashed();
    }

    public static function options(int $additionalOptions = 0): Collection
    {
        $indeks = self::query()->orderBy('nama')->get(['id', 'nama']);
        $additionalOptions = max(0, $additionalOptions);
        $alphabet = collect(range('A', 'Z'));

        if ($indeks->isEmpty()) {
            return $alphabet
                ->take(min(3 + $additionalOptions, $alphabet->count()))
                ->map(fn (string $nama) => (object) [
                    'id' => $nama,
                    'nama' => $nama,
                ]);
        }

        return collect($indeks->all())->concat(
            self::nextAlphabetOptions($indeks, $additionalOptions)->map(fn (string $nama) => (object) [
                'id' => $nama,
                'nama' => $nama,
            ])
        );
    }

    public static function hasMoreOptions(int $additionalOptions = 0): bool
    {
        $indeks = self::query()->get(['nama']);

        if ($indeks->isEmpty()) {
            return 3 + max(0, $additionalOptions) < 26;
        }

        return self::nextAlphabetOptions($indeks, $additionalOptions + 1)->count() > $additionalOptions;
    }

    private static function nextAlphabetOptions(Collection $indeks, int $count): Collection
    {
        if ($count === 0) {
            return collect();
        }

        $lastAlphabetCode = $indeks
            ->map(fn ($item) => strtoupper(trim((string) $item->nama)))
            ->filter(fn (string $nama) => strlen($nama) === 1 && ctype_alpha($nama))
            ->map(fn (string $nama) => ord($nama))
            ->max() ?? ord('@');

        return collect(range('A', 'Z'))
            ->filter(fn (string $nama) => ord($nama) > $lastAlphabetCode)
            ->take($count)
            ->values();
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
            'nama' => $nama,
        ]);
    }
}
