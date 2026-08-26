<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Hadir = 'Hadir';
    case Masuk = 'Masuk';
    case Izin = 'Izin';
    case Sakit = 'Sakit';
    case Alpa = 'Alpa';
    case Terlambat = 'Terlambat';
    case Selesai = 'Selesai';

    /** @return array<int, self> */
    public static function dashboard(): array
    {
        return [self::Hadir, self::Terlambat, self::Izin, self::Sakit, self::Alpa];
    }

    /** @return array<int, self> */
    public static function editable(): array
    {
        return [self::Hadir, self::Sakit, self::Izin, self::Alpa];
    }

    /** @return array<int, string> */
    public static function rekapLowercase(): array
    {
        return array_map(
            static fn (self $status): string => $status->lowercase(),
            [self::Sakit, self::Izin, self::Alpa, self::Selesai],
        );
    }

    /** @return array<int, array{value: string, label: string, desc: string, icon: string, class: string}> */
    public static function editFormOptions(): array
    {
        return array_map(static fn (self $status): array => $status->editFormOption(), [
            self::Hadir,
            self::Sakit,
            self::Izin,
            self::Alpa,
            self::Terlambat,
        ]);
    }

    public function lowercase(): string
    {
        return mb_strtolower($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Hadir => '#22c55e',
            self::Terlambat => '#f97316',
            self::Izin => '#eab308',
            self::Sakit => '#3b82f6',
            self::Alpa => '#ef4444',
            default => '#94a3b8',
        };
    }

    /** @return array{value: string, label: string, desc: string, icon: string, class: string} */
    private function editFormOption(): array
    {
        return match ($this) {
            self::Hadir => [
                'value' => $this->lowercase(),
                'label' => $this->value,
                'desc' => 'Murid hadir mengikuti kegiatan',
                'icon' => 'solar:check-circle-bold',
                'class' => 'bg-emerald-100 text-emerald-600',
            ],
            self::Sakit => [
                'value' => $this->lowercase(),
                'label' => $this->value,
                'desc' => 'Murid tidak hadir karena sakit',
                'icon' => 'solar:health-bold',
                'class' => 'bg-amber-100 text-amber-600',
            ],
            self::Izin => [
                'value' => $this->lowercase(),
                'label' => $this->value,
                'desc' => 'Murid tidak hadir dengan izin',
                'icon' => 'solar:document-text-bold',
                'class' => 'bg-blue-100 text-blue-600',
            ],
            self::Alpa => [
                'value' => $this->lowercase(),
                'label' => $this->value,
                'desc' => 'Murid tidak hadir tanpa keterangan',
                'icon' => 'solar:close-circle-bold',
                'class' => 'bg-rose-100 text-rose-600',
            ],
            self::Terlambat => [
                'value' => $this->lowercase(),
                'label' => $this->value,
                'desc' => 'Murid hadir tapi terlambat',
                'icon' => 'solar:clock-circle-bold',
                'class' => 'bg-orange-100 text-orange-600',
            ],
            default => throw new \LogicException("{$this->value} tidak tersedia pada form edit absensi."),
        };
    }
}
