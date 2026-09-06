<?php

namespace App\Models\Murid\Rombel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Tingkat extends Model
{
    protected $table = 'tingkat';

    protected $fillable = ['nama'];

    private const DEFAULT_OPTIONS = ['X', 'XI', 'XII', 'XIII'];

    /**
     * Tampilkan pilihan tingkat bawaan saat master tingkat belum diisi.
     */
    public static function options(int $additionalOptions = 0): Collection
    {
        $tingkat = self::query()->orderBy('nama')->get(['id', 'nama']);
        $additionalOptions = max(0, $additionalOptions);

        if ($tingkat->isEmpty()) {
            return collect(self::DEFAULT_OPTIONS)
                ->take(min(3 + $additionalOptions, count(self::DEFAULT_OPTIONS)))
                ->map(fn (string $nama) => (object) [
                    'id' => $nama,
                    'nama' => $nama,
                ]);
        }

        return collect($tingkat->all())->concat(
            self::nextLevelOptions($tingkat, $additionalOptions)->map(fn (string $nama) => (object) [
                'id' => $nama,
                'nama' => $nama,
            ])
        );
    }

    public static function hasMoreOptions(int $additionalOptions = 0): bool
    {
        $tingkat = self::query()->get(['nama']);

        if ($tingkat->isEmpty()) {
            return 3 + max(0, $additionalOptions) < count(self::DEFAULT_OPTIONS);
        }

        return self::nextLevelOptions($tingkat, $additionalOptions + 1)->count() > $additionalOptions;
    }

    private static function nextLevelOptions(Collection $tingkat, int $count): Collection
    {
        if ($count === 0) {
            return collect();
        }

        $lastPosition = $tingkat
            ->map(fn ($item) => array_search(strtoupper(trim((string) $item->nama)), self::DEFAULT_OPTIONS, true))
            ->filter(fn (int|false $position) => $position !== false)
            ->max() ?? -1;

        return collect(array_slice(self::DEFAULT_OPTIONS, $lastPosition + 1, $count));
    }

    /**
     * Ubah pilihan bawaan menjadi data master saat pilihan tersebut digunakan.
     */
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

        if (! in_array($nama, self::DEFAULT_OPTIONS, true)) {
            return null;
        }

        return self::query()->firstOrCreate(['nama' => $nama]);
    }

    /**
     * Relasi ke Rombel
     */
    public function rombel()
    {
        return $this->hasMany(Rombel::class)->withTrashed();
    }
}
