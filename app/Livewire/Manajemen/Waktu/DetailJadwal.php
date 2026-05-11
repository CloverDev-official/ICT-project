<?php

namespace App\Livewire\Manajemen\Waktu;

use App\Models\JadwalAbsen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;
use Livewire\Component;

class DetailJadwal extends Component
{
    private const DEFAULT_MASUK = '06:30';
    private const DEFAULT_PULANG_NORMAL = '16:30';
    private const DEFAULT_PULANG_KHUSUS = '11:30';
    private const TYPE_LIBUR = 'libur';
    private const TYPE_NORMAL = 'normal';
    private const TYPE_KHUSUS = 'khusus';
    private const TYPE_CUSTOM = 'custom';

    public $month;
    public $year;

    public $selectedDate;
    public $title;
    public $masuk;
    public $pulang;

    public $keterangan;
    public $isEditing = false;
    public $isCustomMode = false;
    public $scheduleMode = 'libur';
    public $isSpecialSchedule = false;

    public $jadwal = [];

    public function mount(int $month, int $year): void
    {
        $this->month = $month;
        $this->year = $year;
        $this->loadJadwal();
    }

    public function updatedMonth(): void
    {
        $this->loadJadwal();
    }

    public function updatedYear(): void
    {
        $this->loadJadwal();
    }

    private function getDayIso(string $date): int
    {
        return (int) date('N', strtotime($date));
    }

    public function getDefaultSchedule(string $date): array
    {
        $day = $this->getDayIso($date);

        if ($day == 5) {
            return [
                'masuk' => self::DEFAULT_MASUK,
                'pulang' => self::DEFAULT_PULANG_KHUSUS,
                'type' => self::TYPE_KHUSUS,
            ];
        }

        if ($day >= 6) {
            return [
                'masuk' => null,
                'pulang' => null,
                'type' => self::TYPE_LIBUR,
            ];
        }

        return [
            'masuk' => self::DEFAULT_MASUK,
            'pulang' => self::DEFAULT_PULANG_NORMAL,
            'type' => self::TYPE_NORMAL,
        ];
    }

    private function setScheduleState(
        string $mode,
        ?string $masuk,
        ?string $pulang,
        ?string $keterangan,
        ?string $title = null
    ): void {
        $this->scheduleMode = $mode;
        $this->masuk = $masuk;
        $this->pulang = $pulang;
        $this->keterangan = $keterangan;
        $this->title = $title;
    }

    private function applyDefaultByDay(string $date): void
    {
        $day = $this->getDayIso($date);

        if ($day >= 6) {
            $this->setScheduleState(self::TYPE_LIBUR, null, null, 'Hari Libur');
            return;
        }

        if ($day == 5) {
            $this->setScheduleState(
                self::TYPE_KHUSUS,
                self::DEFAULT_MASUK,
                self::DEFAULT_PULANG_KHUSUS,
                'Jadwal Khusus'
            );
            return;
        }

        $this->setScheduleState(
            self::TYPE_NORMAL,
            self::DEFAULT_MASUK,
            self::DEFAULT_PULANG_NORMAL,
            'Normal'
        );
        $this->isSpecialSchedule = false;
    }

    #[On('select-date')]
    public function selectDate(string $date): void
    {
        $this->isEditing = false;
        $this->selectedDate = $date;

        $this->setScheduleState(self::TYPE_NORMAL, null, null, null);

        if (isset($this->jadwal[$date])) {
            $jadwal = $this->jadwal[$date];

            $this->setScheduleState(
                $jadwal['tipe'] ?? self::TYPE_NORMAL,
                $jadwal['jam_masuk'] ?? null,
                $jadwal['jam_pulang'] ?? null,
                $jadwal['keterangan'] ?? null,
                $jadwal['nama_acara'] ?? null
            );

            $this->isSpecialSchedule = false;

            return;
        }

        $jadwal = JadwalAbsen::whereDate('tanggal', $date)->first();

        if ($jadwal) {
            $this->setScheduleState(
                $jadwal->tipe ?? self::TYPE_NORMAL,
                $jadwal->jam_masuk,
                $jadwal->jam_pulang,
                $jadwal->keterangan,
                $jadwal->nama_acara
            );

            $this->isSpecialSchedule = false;

            return;
        }

        $this->applyDefaultByDay($date);
    }

    #[On('open-custom')]
    public function openCustomSchedule(string $date): void
    {
        $this->selectedDate = $date;
        $this->isCustomMode = true;
        $this->isEditing = false;
        $this->isSpecialSchedule = false;

        $this->setScheduleState(
            self::TYPE_CUSTOM,
            self::DEFAULT_MASUK,
            self::DEFAULT_PULANG_NORMAL,
            '',
            ''
        );
    }

    public function updatedScheduleMode($value): void
    {
        if ($value == self::TYPE_LIBUR) {
            $this->setScheduleState(self::TYPE_LIBUR, null, null, 'Hari Libur', $this->title);
            return;
        }

        if ($value == self::TYPE_NORMAL) {
            $this->setScheduleState(
                self::TYPE_NORMAL,
                self::DEFAULT_MASUK,
                self::DEFAULT_PULANG_NORMAL,
                'Jadwal Normal',
                $this->title
            );
            return;
        }

        if ($value == self::TYPE_KHUSUS) {
            $this->setScheduleState(
                self::TYPE_KHUSUS,
                self::DEFAULT_MASUK,
                self::DEFAULT_PULANG_KHUSUS,
                'Jadwal Khusus',
                $this->title
            );
            return;
        }
    }

    public function saveJadwal(): void
    {
        $this->validate([
            'selectedDate' => 'required',
        ]);

        $day = $this->getDayIso($this->selectedDate);

        if ($this->isCustomMode) {
            if (
                $this->scheduleMode == self::TYPE_LIBUR &&
                $this->isCustomMode &&
                !$this->title
            ) {
                $this->addError('title', 'Nama acara wajib untuk hari libur');
                return;
            }

            JadwalAbsen::updateOrCreate(
                ['tanggal' => $this->selectedDate],
                [
                    'nama_acara' => $this->title,
                    'jam_masuk' => $this->scheduleMode == self::TYPE_LIBUR
                        ? null
                        : $this->masuk,

                    'jam_pulang' => $this->scheduleMode == self::TYPE_LIBUR
                        ? null
                        : $this->pulang,
                    'keterangan' => $this->keterangan,
                    'tipe' => $this->scheduleMode == self::TYPE_LIBUR
                    ? self::TYPE_LIBUR
                    : self::TYPE_CUSTOM,
                ],
            );
        } elseif ($this->isSpecialSchedule) {
            JadwalAbsen::updateOrCreate(
                ['tanggal' => $this->selectedDate],
                [
                    'nama_acara' => null,

                    'jam_masuk' =>
                        $this->scheduleMode == self::TYPE_LIBUR ? null : $this->masuk,

                    'jam_pulang' =>
                        $this->scheduleMode == self::TYPE_LIBUR ? null : $this->pulang,

                    'keterangan' => $this->keterangan,

                    'tipe' => match ($this->scheduleMode) {
                        self::TYPE_LIBUR => self::TYPE_LIBUR,

                        self::TYPE_NORMAL => self::TYPE_NORMAL,

                        self::TYPE_KHUSUS => self::TYPE_KHUSUS,

                        default => self::TYPE_LIBUR,
                    },
                ],
            );
        } elseif ($day == 5) {
            $start = Carbon::parse($this->selectedDate)->startOfYear();
            $end = Carbon::parse($this->selectedDate)->endOfYear();

            while ($start <= $end) {
                if ($start->dayOfWeekIso == 5) {
                    JadwalAbsen::updateOrCreate(
                        ['tanggal' => $start->format('Y-m-d')],
                        [
                            'jam_masuk' => $this->masuk,
                            'jam_pulang' => $this->pulang,
                            'keterangan' => 'Jumat',
                            'tipe' => self::TYPE_KHUSUS,
                        ],
                    );
                }

                $start->addDay();
            }
        } else {
            $start = Carbon::parse($this->selectedDate)->startOfYear();
            $end = Carbon::parse($this->selectedDate)->endOfYear();

            while ($start <= $end) {
                if ($start->dayOfWeekIso >= 1 && $start->dayOfWeekIso <= 4) {
                    JadwalAbsen::updateOrCreate(
                        ['tanggal' => $start->format('Y-m-d')],
                        [
                            'jam_masuk' => $this->masuk,
                            'jam_pulang' => $this->pulang,
                            'keterangan' => 'Normal',
                            'tipe' => self::TYPE_NORMAL,
                        ],
                    );
                }

                $start->addDay();
            }
        }

        $this->isEditing = false;
        $this->isCustomMode = false;
        $this->isSpecialSchedule = false;
        $this->selectDate($this->selectedDate);

        $this->loadJadwal();

        $this->reset([
            'isCustomMode',
            'isEditing',
            'isSpecialSchedule',
        ]);

        $this->scheduleMode = self::TYPE_LIBUR;

        $this->dispatch('jadwal-updated');
    }

    public function cancelEdit(): void
    {
        $this->isEditing = false;
        $this->isCustomMode = false;
        $this->isSpecialSchedule = false;

        $this->scheduleMode = self::TYPE_LIBUR;
    }

    public function loadJadwal(): void
    {
        if (!Schema::hasTable('jadwal_absen')) {
            $this->jadwal = [];
            return;
        }

        $start = now()->setDate($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $this->jadwal = JadwalAbsen::whereBetween('tanggal', [$start, $end])
            ->get()
            ->keyBy(fn ($j) => $j->tanggal->format('Y-m-d'))
            ->toArray();
    }

    public function editJadwal(): void
    {
        $jadwal = JadwalAbsen::whereDate(
            'tanggal',
            $this->selectedDate,
        )->first();

        if ($jadwal) {
            if ($jadwal->tipe == self::TYPE_CUSTOM) {
                $this->isCustomMode = true;
                $this->scheduleMode = self::TYPE_CUSTOM;

                $this->title = $jadwal->nama_acara;

                $this->masuk = $jadwal->jam_masuk;
                $this->pulang = $jadwal->jam_pulang;

                $this->keterangan = $jadwal->keterangan;

                return;
            }

            if ($jadwal->tipe == self::TYPE_LIBUR) {
                if ($jadwal->nama_acara) {
                    $this->isCustomMode = true;

                    $this->title = $jadwal->nama_acara;

                    $this->masuk = null;
                    $this->pulang = null;

                    $this->keterangan = $jadwal->keterangan;

                    $this->scheduleMode = self::TYPE_LIBUR;

                    return;
                }

                $this->isEditing = true;
                $this->isSpecialSchedule = true;
                $this->scheduleMode = self::TYPE_LIBUR;

                $this->masuk = null;
                $this->pulang = null;

                $this->keterangan = 'Hari Libur';

                return;
            }

            if ($jadwal->tipe == self::TYPE_KHUSUS) {
                $this->isEditing = true;

                $this->isSpecialSchedule = true;
                $this->scheduleMode = self::TYPE_KHUSUS;

                $this->masuk = $jadwal->jam_masuk;
                $this->pulang = $jadwal->jam_pulang;

                $this->keterangan = $jadwal->keterangan;

                return;
            }

            if ($jadwal->tipe == self::TYPE_NORMAL) {
                $this->isEditing = true;

                $this->isSpecialSchedule = true;
                $this->scheduleMode = self::TYPE_NORMAL;

                $this->masuk = $jadwal->jam_masuk;
                $this->pulang = $jadwal->jam_pulang;

                $this->keterangan = $jadwal->keterangan;

                return;
            }
        }

        $day = $this->getDayIso($this->selectedDate);

        if ($day >= 6) {
            $this->isEditing = true;

            $this->isSpecialSchedule = true;
            $this->scheduleMode = self::TYPE_LIBUR;

            $this->masuk = null;
            $this->pulang = null;

            $this->keterangan = 'Hari Libur';

            return;
        }

        if ($day == 5) {
            $this->isEditing = true;

            $this->isSpecialSchedule = true;
            $this->scheduleMode = self::TYPE_KHUSUS;

            $this->masuk = self::DEFAULT_MASUK;
            $this->pulang = self::DEFAULT_PULANG_KHUSUS;

            $this->keterangan = 'Jadwal Khusus';

            return;
        }

        $this->isEditing = true;

        $this->isSpecialSchedule = true;
        $this->scheduleMode = self::TYPE_NORMAL;

        $this->masuk = self::DEFAULT_MASUK;
        $this->pulang = self::DEFAULT_PULANG_NORMAL;

        $this->keterangan = 'Jadwal Normal';
    }

    public function toggleHoliday(): void
    {
        if ($this->scheduleMode != self::TYPE_LIBUR) {
            $this->setScheduleState(self::TYPE_LIBUR, null, null, 'Hari Libur', $this->title);
            return;
        }

        $this->setScheduleState(
            self::TYPE_NORMAL,
            self::DEFAULT_MASUK,
            self::DEFAULT_PULANG_NORMAL,
            'Jadwal Normal',
            $this->title
        );
    }

    public function render()
    {
        return view('livewire.manajemen.waktu.detail-jadwal');
    }
}
