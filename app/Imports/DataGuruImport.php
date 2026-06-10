<?php

namespace App\Imports;

use App\Models\Guru\Guru;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DataGuruImport implements ToCollection, WithCalculatedFormulas, WithHeadingRow, WithStartRow, WithChunkReading
{
    private int $importedCount = 0;
    private int $skippedCount = 0;

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
        $rowsWithNuptk = [];
        $rowsWithNip = [];
        $rowsWithEmail = [];
        $rowsWithoutUnique = [];

        foreach ($collection as $row) {
            $row = $this->normalizeRow($row);

            $nama = $this->getValueExact($row, ['nama', 'nama_guru', 'nama guru'])
                ?? $this->getValueLike($row, ['nama']);
            $jk = $this->normalizeJenisKelamin(
                $this->getValueExact($row, ['jk', 'jenis_kelamin', 'jenis kelamin', 'jenis_kel'])
            );

            if (!$nama || !$jk) {
                $this->skippedCount++;
                continue;
            }

            $nuptk = $this->normalizeNumberString(
                $this->getValueExact($row, ['nuptk'])
            );
            $nip = $this->normalizeNumberString(
                $this->getValueExact($row, ['nip'])
            );
            $email = $this->getValueExact($row, ['e-mail', 'email', 'e_mail']);

            $payload = [
                'public_id' => (string) Str::uuid(),
                'nama' => $nama,
                'nuptk' => $nuptk,
                'jk' => $jk,
                'tempat_lahir' => $this->getValueExact($row, ['tempat lahir', 'tmpt_lahir']),
                'tanggal_lahir' => $this->normalizeDate(
                    $this->getValueExact($row, ['tanggal lahir', 'tgl_lahir', 'tgl'])
                ),
                'nip' => $nip,
                'status_kepegawaian' => $this->getValueExact(
                    $row,
                    ['status kepegawaian', 'status_kepegawaian', 'status']
                ),
                'jenis_ptk' => $this->getValueExact($row, ['jenis ptk', 'jenis_ptk', 'jenis']),
                'agama' => $this->getValueExact($row, ['agama']),
                'alamat_jalan' => $this->getValueExact(
                    $row,
                    ['alamat', 'alamat_jalan', 'alamat jalan']
                ),
                'rt' => $this->normalizeNumberString($this->getValueExact($row, ['rt'])),
                'rw' => $this->normalizeNumberString($this->getValueExact($row, ['rw'])),
                'desa_kelurahan' => $this->getValueExact(
                    $row,
                    ['kelurahan', 'desa', 'desa_kelurahan']
                ),
                'kecamatan' => $this->getValueExact($row, ['kecamatan']),
                'kode_pos' => $this->normalizeNumberString(
                    $this->getValueExact($row, ['kode pos', 'kodepos'])
                ),
                'telepon' => $this->normalizeNumberString(
                    $this->getValueExact($row, ['telepon', 'telp', 'no_telp', 'no_telpon'])
                ),
                'hp' => $this->normalizeNumberString(
                    $this->getValueExact($row, ['hp', 'no_hp'])
                ),
                'email' => $email,
                'user_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($nuptk) {
                $rowsWithNuptk[] = $payload;
            } elseif ($nip) {
                $rowsWithNip[] = $payload;
            } elseif ($email) {
                $rowsWithEmail[] = $payload;
            } else {
                $rowsWithoutUnique[] = $payload;
            }
        }

        $this->upsertGuru($rowsWithNuptk, ['nuptk']);
        $this->upsertGuru($rowsWithNip, ['nip']);
        $this->upsertGuru($rowsWithEmail, ['email']);

        if ($rowsWithoutUnique) {
            Guru::query()->insert($rowsWithoutUnique);
            $this->importedCount += count($rowsWithoutUnique);
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    private function upsertGuru(array $rows, array $uniqueBy): void
    {
        if (!$rows) {
            return;
        }

        Guru::query()->upsert(
            $rows,
            $uniqueBy,
            [
                'nama',
                'jk',
                'tempat_lahir',
                'tanggal_lahir',
                'nip',
                'status_kepegawaian',
                'jenis_ptk',
                'agama',
                'alamat_jalan',
                'rt',
                'rw',
                'desa_kelurahan',
                'kecamatan',
                'kode_pos',
                'telepon',
                'hp',
                'email',
                'nuptk',
                'updated_at',
            ]
        );

        $this->importedCount += count($rows);
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

        $value = trim((string) $value);

        // Konversi nama bulan Indonesia ke Inggris
        $value = str_ireplace([
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ], [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
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
}
