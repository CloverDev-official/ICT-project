<?php

namespace App\Services\Absensi;

use App\Models\JadwalAbsen;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class JadwalAbsensiService
{
    private const DEFAULT_MASUK = '06:30';
    private const DEFAULT_PULANG_NORMAL = '16:30';
    private const DEFAULT_PULANG_JUMAT = '11:30';

    public function forRombel(?int $rombelId, Carbon|string|null $date = null): array
    {
        $date = $date instanceof Carbon
            ? $date->copy()
            : Carbon::parse($date ?: now()->toDateString());

        $settings = $this->settings();
        $default = $this->defaultForDate($date, $settings);

        if (!$rombelId) {
            return $default;
        }

        $jadwal = JadwalAbsen::query()
            ->with(['rombelJadwal' => function ($query) use ($rombelId) {
                $query->where('rombel_id', $rombelId);
            }])
            ->whereDate('tanggal', $date->toDateString())
            ->first();

        if (!$jadwal) {
            return $default;
        }

        $detailRombel = $jadwal->rombelJadwal->first();

        if ($detailRombel) {
            $scanWindow = $this->scanWindowForRombel($detailRombel, $default);

            return $this->normalize([
                'tanggal' => $date->toDateString(),
                'tipe' => $detailRombel->tipe ?: $jadwal->tipe ?: $default['tipe'],
                'jam_masuk' => $detailRombel->jam_masuk ?: $jadwal->jam_masuk ?: $default['jam_masuk'],
                'jam_pulang' => $detailRombel->jam_pulang ?: $jadwal->jam_pulang ?: $default['jam_pulang'],
                'scan_masuk_mulai' => $scanWindow['scan_masuk_mulai'],
                'scan_masuk_sampai' => $scanWindow['scan_masuk_sampai'],
                'scan_keluar_mulai' => $scanWindow['scan_keluar_mulai'],
                'scan_keluar_sampai' => $scanWindow['scan_keluar_sampai'],
                'scan_window_source' => $scanWindow['source'],
                'nama_acara' => $jadwal->nama_acara,
                'keterangan' => $detailRombel->keterangan ?: $jadwal->keterangan ?: $default['keterangan'],
                'source' => 'rombel',
            ]);
        }

        // Kalau event sudah punya detail per rombel, berarti hanya rombel yang dipilih yang terdampak.
        // Rombel lain tetap memakai jadwal default hari tersebut.
        if ($jadwal->rombelJadwal()->exists()) {
            return $default;
        }

        // Fallback untuk data lama yang masih menyimpan jadwal global tanpa detail kelas.
        return $this->normalize([
            'tanggal' => $date->toDateString(),
            'tipe' => $jadwal->tipe ?: $default['tipe'],
            'jam_masuk' => $jadwal->jam_masuk ?: $default['jam_masuk'],
            'jam_pulang' => $jadwal->jam_pulang ?: $default['jam_pulang'],
            'scan_masuk_mulai' => $default['scan_masuk_mulai'] ?? null,
            'scan_masuk_sampai' => $default['scan_masuk_sampai'] ?? null,
            'scan_keluar_mulai' => $default['scan_keluar_mulai'] ?? null,
            'scan_keluar_sampai' => $default['scan_keluar_sampai'] ?? null,
            'nama_acara' => $jadwal->nama_acara,
            'keterangan' => $jadwal->keterangan ?: $default['keterangan'],
            'source' => 'global',
        ]);
    }

    public function bolehScan(array $jadwal): bool
    {
        return !in_array($jadwal['tipe'], ['libur', 'pjj'], true);
    }

    public function labelTipe(?string $tipe): string
    {
        return match ($tipe) {
            'normal' => 'Masuk Normal',
            'pulang_cepat' => 'Pulang Cepat',
            'pjj' => 'PJJ',
            'libur' => 'Libur',
            'khusus' => 'Hari Spesial',
            default => 'Jadwal Khusus',
        };
    }

    private function settings(): array
    {
        return Setting::query()
            ->whereIn('key', [
                'jadwal.default_masuk',
                'jadwal.default_pulang_normal',
                'jadwal.default_pulang_jumat',
                'jadwal.scan_masuk_mulai',
                'jadwal.scan_masuk_sampai',
                'jadwal.scan_keluar_mulai',
                'jadwal.scan_keluar_sampai',
                'waktu_masuk',
                'waktu_keluar',
            ])
            ->pluck('value', 'key')
            ->all();
    }

    private function defaultForDate(Carbon $date, array $settings): array
    {
        $jamMasuk = $settings['jadwal.default_masuk']
            ?? $settings['waktu_masuk']
            ?? self::DEFAULT_MASUK;

        $jamPulangNormal = $settings['jadwal.default_pulang_normal']
            ?? $settings['waktu_keluar']
            ?? self::DEFAULT_PULANG_NORMAL;

        $jamPulangJumat = $settings['jadwal.default_pulang_jumat']
            ?? self::DEFAULT_PULANG_JUMAT;

        $scanWindow = $this->scanWindow(
            $settings,
            $jamMasuk,
            $date->isFriday() ? $jamPulangJumat : $jamPulangNormal,
        );

        if ($date->isSaturday() || $date->isSunday()) {
            return $this->normalize([
                'tanggal' => $date->toDateString(),
                'tipe' => 'libur',
                'jam_masuk' => null,
                'jam_pulang' => null,
                'scan_masuk_mulai' => null,
                'scan_masuk_sampai' => null,
                'scan_keluar_mulai' => null,
                'scan_keluar_sampai' => null,
                'nama_acara' => null,
                'keterangan' => 'Libur default Sabtu/Minggu',
                'source' => 'default',
            ]);
        }

        return $this->normalize([
            'tanggal' => $date->toDateString(),
            'tipe' => 'normal',
            'jam_masuk' => $jamMasuk,
            'jam_pulang' => $date->isFriday() ? $jamPulangJumat : $jamPulangNormal,
            'scan_masuk_mulai' => $scanWindow['scan_masuk_mulai'],
            'scan_masuk_sampai' => $scanWindow['scan_masuk_sampai'],
            'scan_keluar_mulai' => $scanWindow['scan_keluar_mulai'],
            'scan_keluar_sampai' => $scanWindow['scan_keluar_sampai'],
            'nama_acara' => $date->isFriday() ? 'Jadwal Jumat' : null,
            'keterangan' => $date->isFriday() ? 'Jadwal default khusus Jumat' : null,
            'source' => 'default',
        ]);
    }

    private function scanWindow(array $settings, ?string $jamMasuk, ?string $jamPulang): array
    {
        return [
            'scan_masuk_mulai' => $settings['jadwal.scan_masuk_mulai'] ?? $jamMasuk,
            'scan_masuk_sampai' => $settings['jadwal.scan_masuk_sampai'] ?? $jamMasuk,
            'scan_keluar_mulai' => $settings['jadwal.scan_keluar_mulai'] ?? $jamPulang,
            'scan_keluar_sampai' => $settings['jadwal.scan_keluar_sampai'] ?? $jamPulang,
        ];
    }

    private function scanWindowForRombel(object $detailRombel, array $default): array
    {
        if (!$this->supportsRombelScanWindow() || !($detailRombel->gunakan_window_scan ?? false)) {
            return [
                'scan_masuk_mulai' => $default['scan_masuk_mulai'] ?? null,
                'scan_masuk_sampai' => $default['scan_masuk_sampai'] ?? null,
                'scan_keluar_mulai' => $default['scan_keluar_mulai'] ?? null,
                'scan_keluar_sampai' => $default['scan_keluar_sampai'] ?? null,
                'source' => 'default',
            ];
        }

        return [
            'scan_masuk_mulai' => $this->formatTime($detailRombel->scan_masuk_mulai ?? null) ?? ($default['scan_masuk_mulai'] ?? null),
            'scan_masuk_sampai' => $this->formatTime($detailRombel->scan_masuk_sampai ?? null) ?? ($default['scan_masuk_sampai'] ?? null),
            'scan_keluar_mulai' => $this->formatTime($detailRombel->scan_keluar_mulai ?? null) ?? ($default['scan_keluar_mulai'] ?? null),
            'scan_keluar_sampai' => $this->formatTime($detailRombel->scan_keluar_sampai ?? null) ?? ($default['scan_keluar_sampai'] ?? null),
            'source' => 'rombel',
        ];
    }

    private function supportsRombelScanWindow(): bool
    {
        static $supported = null;

        if ($supported !== null) {
            return $supported;
        }

        if (!Schema::hasTable('jadwal_absen_rombel')) {
            return $supported = false;
        }

        foreach ([
            'gunakan_window_scan',
            'scan_masuk_mulai',
            'scan_masuk_sampai',
            'scan_keluar_mulai',
            'scan_keluar_sampai',
        ] as $column) {
            if (!Schema::hasColumn('jadwal_absen_rombel', $column)) {
                return $supported = false;
            }
        }

        return $supported = true;
    }

    private function formatTime($time): ?string
    {
        if (!$time) {
            return null;
        }

        return substr((string) $time, 0, 5);
    }

    private function normalize(array $jadwal): array
    {
        $tipe = $jadwal['tipe'] ?: 'normal';

        if (in_array($tipe, ['libur', 'pjj'], true)) {
            $jadwal['jam_masuk'] = null;
            $jadwal['jam_pulang'] = null;
            $jadwal['scan_masuk_mulai'] = null;
            $jadwal['scan_masuk_sampai'] = null;
            $jadwal['scan_keluar_mulai'] = null;
            $jadwal['scan_keluar_sampai'] = null;
        }

        $jadwal['tipe'] = $tipe;
        $jadwal['label'] = $this->labelTipe($tipe);

        return $jadwal;
    }
}
