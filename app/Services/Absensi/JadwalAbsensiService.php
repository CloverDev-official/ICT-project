<?php

namespace App\Services\Absensi;

use App\Models\JadwalAbsen;
use App\Models\Setting;
use Carbon\Carbon;

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
            return $this->normalize([
                'tanggal' => $date->toDateString(),
                'tipe' => $detailRombel->tipe ?: $jadwal->tipe ?: $default['tipe'],
                'jam_masuk' => $detailRombel->jam_masuk ?: $jadwal->jam_masuk ?: $default['jam_masuk'],
                'jam_pulang' => $detailRombel->jam_pulang ?: $jadwal->jam_pulang ?: $default['jam_pulang'],
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

        if ($date->isSaturday() || $date->isSunday()) {
            return $this->normalize([
                'tanggal' => $date->toDateString(),
                'tipe' => 'libur',
                'jam_masuk' => null,
                'jam_pulang' => null,
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
            'nama_acara' => $date->isFriday() ? 'Jadwal Jumat' : null,
            'keterangan' => $date->isFriday() ? 'Jadwal default khusus Jumat' : null,
            'source' => 'default',
        ]);
    }

    private function normalize(array $jadwal): array
    {
        $tipe = $jadwal['tipe'] ?: 'normal';

        if (in_array($tipe, ['libur', 'pjj'], true)) {
            $jadwal['jam_masuk'] = null;
            $jadwal['jam_pulang'] = null;
        }

        $jadwal['tipe'] = $tipe;
        $jadwal['label'] = $this->labelTipe($tipe);

        return $jadwal;
    }
}
