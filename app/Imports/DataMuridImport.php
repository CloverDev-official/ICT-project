<?php

namespace App\Imports;

use Fruitcake\LaravelDebugbar\Facades\Debugbar;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Indeks;
use Illuminate\Support\Facades\Cache;

class DataMuridImport implements ToCollection, WithCalculatedFormulas, WithHeadingRow, WithStartRow, WithChunkReading, WithEvents, ShouldQueue
{
    private array $tingkatCache = [];
    private array $jurusanCache = [];
    private array $indeksCache = [];
    private array $rombelCache = [];
    private int $importedCount = 0;
    private int $skippedCount = 0;
    private ?string $progressKey = null;
    private ?string $resultKey = null;
    private int $totalRows = 0;

    public function __construct(?string $progressKey = null, ?string $resultKey = null, int $totalRows = 0)
    {
        $this->progressKey = $progressKey;
        $this->resultKey = $resultKey;
        $this->totalRows = $totalRows;
    }

    public function startRow(): int
    {
        return 6;
    }
    public function headingRow(): int
    {
        return 5;
    }
    public function chunkSize(): int
    {
        return 1000;
    }

    public function collection(Collection $collection)
    {
        if ($collection->isEmpty()) {
            return;
        }

        $now = now();
        $rows = [];
        $skippedThisChunk = 0;
        $processedThisChunk = $collection->count();

        foreach ($collection as $row) {
            $row = $this->normalizeRow($row);

            $nama = $this->getValueExact($row, ['nama'])
                ?? $this->getValueLike($row, ['nama_peserta', 'nama_siswa', 'nama_murid']);
            $nipd = $this->normalizeNumberString(
                $this->getValueExact($row, ['nipd', 'nis', 'no_induk'])
            );
            $jk = $this->normalizeJenisKelamin(
                $this->getValueExact($row, ['jk', 'jenis_kelamin', 'jenis_kel'])
            );

            Debugbar::info("Processing row: Nama={$nama}, NIPD={$nipd}, JK={$jk}");

            $nisn = $this->normalizeNumberString(
                $this->getValueExact($row, ['nisn'])
            );

            if (!$nama || !$nipd || !$nisn || !$jk) {
                $this->skippedCount++;
                $skippedThisChunk++;
                continue;
            }

            $tempatLahir = $this->getValueExact($row, ['tempat lahir', 'tmpt_lahir']);
            $tanggalLahir = $this->normalizeDate(
                $this->getValueExact($row, ['tanggal lahir', 'tgl_lahir', 'tgl'])
            );

            if (!$tempatLahir || !$tanggalLahir) {
                $this->skippedCount++;
                $skippedThisChunk++;
                continue;
            }

            $rombelId = $this->resolveRombelId($row);

            $rows[] = [
                'ulid' => (string) Str::ulid(),
                'nama' => $nama,
                'nipd' => $nipd,
                'jk' => $jk,
                'nisn' => $nisn,
                'tempat_lahir' => $tempatLahir,
                'tanggal_lahir' => $tanggalLahir,
                'agama' => $this->getValueExact($row, ['agama']),
                'alamat' => $this->getValueExact($row, ['alamat', 'alamat_jalan']),
                'rt' => $this->normalizeNumberString($this->getValueExact($row, ['rt'])),
                'rw' => $this->normalizeNumberString($this->getValueExact($row, ['rw'])),
                'kelurahan' => $this->getValueExact($row, ['kelurahan', 'desa', 'desa_kelurahan']),
                'kecamatan' => $this->getValueExact($row, ['kecamatan']),
                'kode_pos' => $this->normalizeNumberString($this->getValueExact($row, ['kode pos', 'kodepos'])),
                'hp' => $this->normalizeNumberString(
                    $this->getValueExact($row, ['telepon', 'hp', 'no_hp', 'no_telp', 'no_telpon'])
                ),
                'email' => $this->getValueExact($row, ['e-mail','email', 'e_mail']),
                'nama_ayah' => $this->getValueExact($row, ['nama ayah', 'ayah', 'nama_ayah_kandung']),
                'nama_ibu' => $this->getValueExact($row, ['nama ibu', 'ibu', 'nama_ibu_kandung']),
                'nama_wali' => $this->getValueExact($row, ['nama wali', 'wali']),
                'image_path' => null,
                'rombel_id' => $rombelId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!$rows) {
            $this->updateProgressCache($processedThisChunk, 0, $skippedThisChunk);
            return;
        }

        Murid::query()->upsert(
            $rows,
            ['nipd'],
            [
                'nama',
                'jk',
                'nisn',
                'tempat_lahir',
                'tanggal_lahir',
                'agama',
                'alamat',
                'rt',
                'rw',
                'kelurahan',
                'kecamatan',
                'hp',
                'email',
                'nama_ayah',
                'nama_ibu',
                'nama_wali',
                'image_path',
                'rombel_id',
                'updated_at',
            ]
        );

        $this->importedCount += count($rows);
        $this->updateProgressCache($processedThisChunk, count($rows), $skippedThisChunk);
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function () {
                if (!$this->progressKey) {
                    return;
                }

                Cache::put($this->progressKey . ':done', true, now()->addHour());
            },
        ];
    }

    private function updateProgressCache(int $processed, int $imported, int $skipped): void
    {
        if (!$this->progressKey) {
            return;
        }

        Cache::increment($this->progressKey . ':processed', $processed);
        Cache::put($this->progressKey . ':total', $this->totalRows, now()->addHour());

        if ($this->resultKey) {
            Cache::increment($this->resultKey . ':imported', $imported);
            Cache::increment($this->resultKey . ':skipped', $skipped);
        }
    }

    private function normalizeRow($row): array
    {
        $array = $row instanceof Collection ? $row->toArray() : (array) $row;
        return array_change_key_case($array, CASE_LOWER);
    }

    private function normalizeKey($value): string
    {
        $value = preg_replace('/\s+/', '_', trim((string) $value));

        return function_exists('mb_strtolower')
            ? mb_strtolower($value, 'UTF-8')
            : strtolower($value);
    }

    private function getValueExact(array $row, array $keys): ?string
    {
        $normalizedRow = [];

        foreach ($row as $k => $v) {
            $normalizedRow[$this->normalizeKey($k)] = $v;
        }

        foreach ($keys as $key) {
            $normalizedKey = $this->normalizeKey($key);

            if (array_key_exists($normalizedKey, $normalizedRow)) {
                return $this->normalizeString($normalizedRow[$normalizedKey]);
            }
        }

        return null;
    }

    private function getValueLike(array $row, array $needles): ?string
    {
        $normalizedRow = [];

        foreach ($row as $k => $v) {
            $normalizedRow[$this->normalizeKey($k)] = $v;
        }

        foreach ($needles as $needle) {
            $normalizedNeedle = $this->normalizeKey($needle);

            foreach ($normalizedRow as $key => $value) {
                if (str_contains($key, $normalizedNeedle)) {
                    return $this->normalizeString($value);
                }
            }
        }

        return null;
    }

    private function normalizeString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function normalizeNumberString($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            $value = sprintf('%.0f', $value);
        }

        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function normalizeJenisKelamin($value): ?string
    {
        $value = $this->normalizeString($value);
        if (!$value) {
            return null;
        }

        $value = strtoupper(str_replace(['.', '-', '_'], ' ', $value));

        if (in_array($value, ['L', 'LAKI LAKI', 'LAKI-LAKI', 'LAKI', 'MALE', 'M'], true)) {
            return 'L';
        }

        if (in_array($value, ['P', 'PEREMPUAN', 'WANITA', 'FEMALE', 'F'], true)) {
            return 'P';
        }

        return null;
    }

    private function normalizeDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function resolveRombelId(array $row): ?int
    {
        $tingkat = $this->getValueExact($row, ['tingkat', 'kelas_tingkat', 'tingkat_kelas']);
        $jurusan = $this->getValueExact($row, ['jurusan', 'program_keahlian', 'kompetensi_keahlian']);
        $indeks = $this->getValueExact($row, ['indeks', 'kelas_indeks', 'rombel_indeks']);

        if (!$tingkat || !$jurusan || !$indeks) {
            $kelasRaw = $this->getValueExact($row, ['Rombel Saat Ini','kelas', 'rombel', 'kelas_rombel', 'rombongan_belajar']);
            if ($kelasRaw) {
                [$parsedTingkat, $parsedJurusan, $parsedIndeks] = $this->parseKelasString($kelasRaw);
                $tingkat = $tingkat ?: $parsedTingkat;
                $jurusan = $jurusan ?: $parsedJurusan;
                $indeks = $indeks ?: $parsedIndeks;
            }
        }

        $tingkatId = $this->getOrCreateTingkatId($tingkat);
        $jurusanId = $this->getOrCreateJurusanId($jurusan);
        $indeksId = $this->getOrCreateIndeksId($indeks);

        if (!$tingkatId || !$jurusanId || !$indeksId) {
            return null;
        }

        $key = $tingkatId . '|' . $jurusanId . '|' . $indeksId;
        if (isset($this->rombelCache[$key])) {
            return $this->rombelCache[$key];
        }

        $rombel = Rombel::firstOrCreate([
            'tingkat_id' => $tingkatId,
            'jurusan_id' => $jurusanId,
            'indeks_id' => $indeksId,
        ]);

        $this->rombelCache[$key] = $rombel->id;
        return $rombel->id;
    }

    private function parseKelasString(string $kelas): array
    {
        $kelas = trim($kelas);
        $kelas = str_replace(['/', '-', '_', '.'], ' ', $kelas);
        $parts = array_values(array_filter(explode(' ', $kelas), fn ($p) => $p !== ''));

        $tingkat = $parts[0] ?? null;
        $indeks = null;
        $jurusan = null;

        if (count($parts) >= 2) {
            $last = $parts[count($parts) - 1];
            if (preg_match('/^[A-Za-z0-9]+$/', $last)) {
                $indeks = $last;
                $jurusanParts = array_slice($parts, 1, -1);
            } else {
                $jurusanParts = array_slice($parts, 1);
            }
            $jurusan = $jurusanParts ? implode(' ', $jurusanParts) : null;
        }

        return [$tingkat, $jurusan, $indeks];
    }

    private function getOrCreateTingkatId(?string $nama): ?int
    {
        $nama = $this->normalizeString($nama);
        if (!$nama) {
            return null;
        }

        $key = strtolower($nama);
        if (isset($this->tingkatCache[$key])) {
            return $this->tingkatCache[$key];
        }

        $tingkat = Tingkat::firstOrCreate(['nama' => $nama]);
        $this->tingkatCache[$key] = $tingkat->id;

        return $tingkat->id;
    }

    private function getOrCreateJurusanId(?string $nama): ?int
    {
        $nama = $this->normalizeString($nama);
        if (!$nama) {
            return null;
        }

        $key = strtolower($nama);
        if (isset($this->jurusanCache[$key])) {
            return $this->jurusanCache[$key];
        }

        $jurusan = Jurusan::firstOrCreate(['nama' => $nama]);
        $this->jurusanCache[$key] = $jurusan->id;

        return $jurusan->id;
    }

    private function getOrCreateIndeksId(?string $nama): ?int
    {
        $nama = $this->normalizeString($nama);
        if (!$nama) {
            return null;
        }

        $key = strtolower($nama);
        if (isset($this->indeksCache[$key])) {
            return $this->indeksCache[$key];
        }

        $indeks = Indeks::firstOrCreate(['nama' => $nama]);
        $this->indeksCache[$key] = $indeks->id;

        return $indeks->id;
    }
}