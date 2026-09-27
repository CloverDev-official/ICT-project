<?php

namespace App\Imports;

use App\Enums\StudentStatus;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DataMuridImport implements ShouldQueue, ToCollection, WithCalculatedFormulas, WithChunkReading, WithEvents, WithHeadingRow, WithStartRow
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

    private ?int $tahunMasuk = null;

    public function __construct(?string $progressKey = null, ?string $resultKey = null, int $totalRows = 0, ?int $tahunMasuk = null)
    {
        $this->progressKey = $progressKey;
        $this->resultKey = $resultKey;
        $this->totalRows = $totalRows;
        $this->tahunMasuk = $tahunMasuk;
    }

    public function startRow(): int
    {
        return 6;
    }

    public function headingRow(): int
    {
        // Baris 4 berisi header utama. Kolom pertama dari setiap grup orang tua
        // adalah "Data Ayah", "Data Ibu", dan "Data Wali"; sedangkan baris 5
        // hanya mengulang sub-header "Nama" untuk ketiganya.
        return 4;
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

            $nisn = $this->normalizeNumberString(
                $this->getValueExact($row, ['nisn'])
            );

            if (! $nama || ! $nipd || ! $nisn || ! $jk) {
                $this->skippedCount++;
                $skippedThisChunk++;

                continue;
            }

            $tempatLahir = $this->getValueExact($row, ['tempat lahir', 'tmpt_lahir']);
            $tanggalLahir = $this->normalizeDate(
                $this->getValueExact($row, ['tanggal lahir', 'tgl_lahir', 'tgl'])
            );

            if (! $tempatLahir || ! $tanggalLahir) {
                $this->skippedCount++;
                $skippedThisChunk++;

                continue;
            }

            $rombelId = $this->resolveRombelId($row);

            $payload = [
                'uuid' => (string) Str::uuid(),
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
                'email' => $this->getValueExact($row, ['e-mail', 'email', 'e_mail']),
                'nama_ayah' => $this->getValueExact($row, ['nama ayah', 'data ayah', 'ayah', 'nama_ayah_kandung']),
                'nama_ibu' => $this->getValueExact($row, ['nama ibu', 'data ibu', 'ibu', 'nama_ibu_kandung']),
                'nama_wali' => $this->getValueExact($row, ['nama wali', 'data wali', 'wali']),
                'image_path' => null,
                'rombel_id' => $rombelId,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('murid', 'status')) {
                $payload['status'] = StudentStatus::Aktif->value;
            }

            $rows[] = $payload;
        }

        if (! $rows) {
            $this->updateProgressCache($processedThisChunk, 0, $skippedThisChunk);

            return;
        }

        $updateColumns = [
            // `uuid` dan `image_path` sengaja tidak diperbarui saat NIPD sudah
            // ada. UUID dipakai oleh QR/route, sedangkan foto dikelola dari
            // manajemen foto murid dan tidak tersedia dalam berkas import.
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
            'rombel_id',
            'updated_at',
        ];

        if (Schema::hasColumn('murid', 'status')) {
            $updateColumns[] = 'status';
        }

        Murid::query()->upsert(
            $rows,
            ['nipd'],
            $updateColumns
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
                if (! $this->progressKey) {
                    return;
                }

                Cache::put($this->progressKey.':done', true, now()->addHour());
            },
        ];
    }

    private function updateProgressCache(int $processed, int $imported, int $skipped): void
    {
        if (! $this->progressKey) {
            return;
        }

        Cache::increment($this->progressKey.':processed', $processed);
        Cache::put($this->progressKey.':total', $this->totalRows, now()->addHour());

        if ($this->resultKey) {
            Cache::increment($this->resultKey.':imported', $imported);
            Cache::increment($this->resultKey.':skipped', $skipped);
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
        if (! $value) {
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

        $value = trim((string) $value);

        // Konversi nama bulan Indonesia ke Inggris
        $value = str_ireplace([
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ], [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
        ], $value);

        $formats = [
            // d/m/Y
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',

            // Y/m/d
            'Y/m/d',
            'Y-m-d',
            'Y.m.d',

            // d/m/y
            'd/m/y',
            'd-m-y',
            'd.m.y',

            // Tanpa separator
            'Ymd',
            'dmY',

            // Dengan waktu
            'd/m/Y H:i:s',
            'd-m-Y H:i:s',
            'Y-m-d H:i:s',
            'Y/m/d H:i:s',

            'd/m/Y H:i',
            'd-m-Y H:i',
            'Y-m-d H:i',
            'Y/m/d H:i',

            // Nama bulan
            'd M Y',
            'd F Y',
            'M d, Y',
            'F d, Y',
            'Y M d',
            'Y F d',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);

                // Validasi agar format benar-benar cocok
                if ($date->format($format) === $value) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function resolveRombelId(array $row): ?int
    {
        $tingkat = $this->getValueExact($row, [
            'tingkat',
            'kelas_tingkat',
            'tingkat_kelas',
        ]);

        $jurusan = $this->getValueExact($row, [
            'jurusan',
            'program_keahlian',
            'kompetensi_keahlian',
        ]);

        $indeks = $this->getValueExact($row, [
            'indeks',
            'kelas_indeks',
            'rombel_indeks',
        ]);

        // Jika tingkat atau jurusan belum ada, coba parse dari nama kelas
        if (! $tingkat || ! $jurusan) {
            $kelasRaw = $this->getValueExact($row, [
                'Rombel Saat Ini',
                'kelas',
                'rombel',
                'kelas_rombel',
                'rombongan_belajar',
            ]);

            if ($kelasRaw) {
                [$parsedTingkat, $parsedJurusan, $parsedIndeks] = $this->parseKelasString($kelasRaw);

                $tingkat = $tingkat ?: $parsedTingkat;
                $jurusan = $jurusan ?: $parsedJurusan;

                // Indeks hanya diisi jika berhasil diparse
                if (! $indeks) {
                    $indeks = $parsedIndeks;
                }
            }
        }

        $tingkatId = $this->getOrCreateTingkatId($tingkat);
        $jurusanId = $this->getOrCreateJurusanId($jurusan);
        $indeksId = $indeks ? $this->getOrCreateIndeksId($indeks) : null;

        $tahunMasuk = $this->resolveTahunMasukForTingkat($tingkat);

        // Tingkat dan jurusan wajib ada
        if (! $tingkatId || ! $jurusanId) {
            return null;
        }

        $key = implode('|', [
            $tingkatId,
            $jurusanId,
            $indeksId ?? 'null',
        ]);

        if (isset($this->rombelCache[$key])) {
            if (
                $tahunMasuk &&
                Rombel::query()
                    ->whereKey($this->rombelCache[$key])
                    ->value('tahun_masuk') !== $tahunMasuk
            ) {
                Rombel::query()
                    ->whereKey($this->rombelCache[$key])
                    ->update([
                        'tahun_masuk' => $tahunMasuk,
                    ]);
            }

            return $this->rombelCache[$key];
        }

        $rombel = Rombel::updateOrCreate(
            [
                'tingkat_id' => $tingkatId,
                'jurusan_id' => $jurusanId,
                'indeks_id' => $indeksId, // Bisa null
            ],
            [
                'tahun_masuk' => $tahunMasuk,
            ]
        );

        $this->rombelCache[$key] = $rombel->id;

        return $rombel->id;
    }

    private function resolveTahunMasukForTingkat(?string $tingkat): ?int
    {
        if (! $this->tahunMasuk || ! $tingkat) {
            return $this->tahunMasuk;
        }

        $level = strtoupper(trim($tingkat));
        $offset = match ($level) {
            'X' => 0,
            'XI' => 1,
            'XII' => 2,
            'XIII' => 3,
            default => 0,
        };

        return $this->tahunMasuk - $offset;
    }

    private function parseKelasString(string $kelas): array
    {
        $kelas = trim(preg_replace('/\s+/', ' ', $kelas));

        $parts = explode(' ', $kelas);

        $tingkat = $parts[0] ?? null;
        $indeks = null;

        // Ambil bagian terakhir sebagai indeks jika hanya 1 huruf
        if (count($parts) >= 3 && preg_match('/^[A-Z]$/i', end($parts))) {
            $indeks = array_pop($parts);
        }

        // Hapus tingkat
        array_shift($parts);

        // Sisanya adalah jurusan
        $jurusan = implode(' ', $parts);

        return [
            $tingkat ?: null,
            $jurusan ?: null,
            $indeks,
        ];
    }

    private function getOrCreateTingkatId(?string $nama): ?int
    {
        $nama = $this->normalizeString($nama);
        if (! $nama) {
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
        if (! $nama) {
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
        if (! $nama) {
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
