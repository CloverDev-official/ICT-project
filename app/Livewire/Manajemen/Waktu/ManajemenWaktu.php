<?php

namespace App\Livewire\Manajemen\Waktu;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use App\Support\DateHelper;
use App\Models\JadwalAbsen;

class ManajemenWaktu extends Component
{
    public $month;
    public $year;
    public $selectedDate;
    public $title;
    public $masuk;
    public $pulang;

    public $keterangan;
    public $isEditing = false;
    // single | same_day | weekdays | fridays
    public $isCustomMode = false;
    public $scheduleMode = 'libur';
    public $isSpecialSchedule = false;

    public $calendar = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;

        $this->generateCalendar();
        $this->loadJadwal(); // 🔥 WAJIB
    }

    public function generateCalendar()
    {
        $this->calendar = DateHelper::generateCalendar(
            $this->month,
            $this->year,
        );
    }

    public function nextMonth()
    {
        if ($this->month == 12) {
            $this->month = 1;
            $this->year++;
        } else {
            $this->month++;
        }

        $this->generateCalendar();
        $this->loadJadwal(); // 🔥 penting
    }

    public function previousMonth()
    {
        if ($this->month == 1) {
            $this->month = 12;
            $this->year--;
        } else {
            $this->month--;
        }

        $this->generateCalendar();
        $this->loadJadwal(); // 🔥 penting
    }

    public function selectDate($date)
    {
        $this->isEditing = false;

        $this->title = null;

        $this->scheduleMode = 'normal';

        $this->selectedDate = $date;

        $jadwal = JadwalAbsen::whereDate('tanggal', $date)->first();

        if ($jadwal) {

            $this->title = $jadwal->nama_acara;

            $this->masuk = $jadwal->jam_masuk;
            $this->pulang = $jadwal->jam_pulang;

            $this->keterangan = $jadwal->keterangan;

            $this->isSpecialSchedule = false;

            $this->scheduleMode = match ($jadwal->tipe) {

                'libur' => 'libur',

                'khusus' => 'khusus',

                'normal' => 'normal',

                'custom' => 'custom',

                default => 'normal'
            };

            return;
        }

        $day = date('N', strtotime($date));

        if ($day >= 6) {

            $this->scheduleMode = 'libur';

            $this->masuk = null;
            $this->pulang = null;

            $this->keterangan = 'Hari Libur';

            return;
        }

        if ($day == 5) {
            $this->scheduleMode = 'khusus';

            $this->masuk = '06:30';
            $this->pulang = '11:30';

            $this->keterangan = 'Jadwal Khusus';
            return;
        }

        $this->masuk = '06:30';
        $this->pulang = '16:30';
        $this->keterangan = 'Normal';
        $this->isSpecialSchedule = false;
    }

    public function updatedScheduleMode($value)
    {
        /*
        |--------------------------------------------------------------------------
        | LIBUR
        |--------------------------------------------------------------------------
        */

        if ($value == 'libur') {
            $this->masuk = null;
            $this->pulang = null;

            $this->keterangan = 'Hari Libur';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | NORMAL
        |--------------------------------------------------------------------------
        */

        if ($value == 'normal') {
            $this->masuk = '06:30';
            $this->pulang = '16:30';

            $this->keterangan = 'Jadwal Normal';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | KHUSUS
        |--------------------------------------------------------------------------
        */

        if ($value == 'khusus') {
            $this->masuk = '06:30';
            $this->pulang = '11:30';

            $this->keterangan = 'Jadwal Khusus';

            return;
        }
    }

    public function saveJadwal()
    {
        $this->validate([
            'selectedDate' => 'required',
        ]);

        $day = date('N', strtotime($this->selectedDate));

        /*
        |--------------------------------------------------------------------------
        | CUSTOM
        |--------------------------------------------------------------------------
        */

        if ($this->isCustomMode) {
            if (
                $this->scheduleMode == 'libur' &&
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
                    'jam_masuk' => $this->scheduleMode == 'libur'
                        ? null
                        : $this->masuk,

                    'jam_pulang' => $this->scheduleMode == 'libur'
                        ? null
                        : $this->pulang,    
                    'keterangan' => $this->keterangan,
                    'tipe' => $this->scheduleMode == 'libur'
                    ? 'libur'
                    : 'custom',
                ],
            );
        }
        /*
        |--------------------------------------------------------------------------
        | SPECIAL SCHEDULE
        |--------------------------------------------------------------------------
        */ elseif (
            $this->isSpecialSchedule
        ) {
            JadwalAbsen::updateOrCreate(
                ['tanggal' => $this->selectedDate],
                [
                    'nama_acara' => null,

                    'jam_masuk' =>
                        $this->scheduleMode == 'libur' ? null : $this->masuk,

                    'jam_pulang' =>
                        $this->scheduleMode == 'libur' ? null : $this->pulang,

                    'keterangan' => $this->keterangan,

                    'tipe' => match ($this->scheduleMode) {
                        'libur' => 'libur',

                        'normal' => 'normal',

                        'khusus' => 'khusus',

                        default => 'libur',
                    },
                ],
            );
        }
        /*
        |--------------------------------------------------------------------------
        | KHUSUS
        |--------------------------------------------------------------------------
        */ elseif (
            $day == 5
        ) {
            $start = \Carbon\Carbon::parse($this->selectedDate)->startOfYear();
            $end = \Carbon\Carbon::parse($this->selectedDate)->endOfYear();

            while ($start <= $end) {
                if ($start->dayOfWeekIso == 5) {
                    JadwalAbsen::updateOrCreate(
                        ['tanggal' => $start->format('Y-m-d')],
                        [
                            'jam_masuk' => $this->masuk,
                            'jam_pulang' => $this->pulang,
                            'keterangan' => 'Jumat',
                            'tipe' => 'khusus',
                        ],
                    );
                }

                $start->addDay();
            }
        }
        /*
        |--------------------------------------------------------------------------
        | SENIN - KAMIS
        |--------------------------------------------------------------------------
        */ else {
            $start = \Carbon\Carbon::parse($this->selectedDate)->startOfYear();
            $end = \Carbon\Carbon::parse($this->selectedDate)->endOfYear();

            while ($start <= $end) {
                if ($start->dayOfWeekIso >= 1 && $start->dayOfWeekIso <= 4) {
                    JadwalAbsen::updateOrCreate(
                        ['tanggal' => $start->format('Y-m-d')],
                        [
                            'jam_masuk' => $this->masuk,
                            'jam_pulang' => $this->pulang,
                            'keterangan' => 'Normal',
                            'tipe' => 'normal',
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

        $this->scheduleMode = 'libur';
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->isCustomMode = false;
        $this->isSpecialSchedule = false;

        $this->scheduleMode = 'libur';
    }

    public $jadwal = [];

    public function loadJadwal()
    {
        // 🔥 cegah error kalau tabel belum ada / belum kebaca
        if (!Schema::hasTable('jadwal_absen')) {
            $this->jadwal = [];
            return;
        }

        $start = now()->setDate($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $this->jadwal = JadwalAbsen::whereBetween('tanggal', [$start, $end])
            ->get()
            ->keyBy(fn($j) => $j->tanggal->format('Y-m-d'))
            ->toArray();
    }

    public function editJadwal()
    {
        $jadwal = JadwalAbsen::whereDate(
            'tanggal',
            $this->selectedDate,
        )->first();

        /*
        |--------------------------------------------------------------------------
        | CUSTOM EVENT / LIBUR NASIONAL
        |--------------------------------------------------------------------------
        */

        if ($jadwal) {
            // 🔥 custom event kuning
            if ($jadwal->tipe == 'custom') {
                $this->isCustomMode = true;
                $this->scheduleMode = 'custom';

                $this->title = $jadwal->nama_acara;

                $this->masuk = $jadwal->jam_masuk;
                $this->pulang = $jadwal->jam_pulang;

                $this->keterangan = $jadwal->keterangan;

                return;
            }

            // 🔥 LIBUR
            if ($jadwal->tipe == 'libur') {

                /*
                |--------------------------------------------------------------------------
                | LIBUR NASIONAL (custom merah)
                |--------------------------------------------------------------------------
                */

                if ($jadwal->nama_acara) {

                    $this->isCustomMode = true;

                    $this->title = $jadwal->nama_acara;

                    $this->masuk = null;
                    $this->pulang = null;

                    $this->keterangan = $jadwal->keterangan;

                    $this->scheduleMode = 'libur';

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | LIBUR BIASA
                |--------------------------------------------------------------------------
                */

                $this->isEditing = true;

                $this->isSpecialSchedule = true;

                $this->scheduleMode = 'libur';

                $this->masuk = null;
                $this->pulang = null;

                $this->keterangan = 'Hari Libur';

                return;
            }

            // 🔥 jadwal khusus ungu
            if ($jadwal->tipe == 'khusus') {
                $this->isEditing = true;

                $this->isSpecialSchedule = true;

                $this->scheduleMode = 'khusus';

                $this->masuk = $jadwal->jam_masuk;
                $this->pulang = $jadwal->jam_pulang;

                $this->keterangan = $jadwal->keterangan;

                return;
            }

            // 🔥 normal
            if ($jadwal->tipe == 'normal') {
                $this->isEditing = true;

                $this->isSpecialSchedule = true;

                $this->scheduleMode = 'normal';

                $this->masuk = $jadwal->jam_masuk;
                $this->pulang = $jadwal->jam_pulang;

                $this->keterangan = $jadwal->keterangan;

                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT WEEKEND
        |--------------------------------------------------------------------------
        */

        $day = date('N', strtotime($this->selectedDate));

        if ($day >= 6) {
            $this->isEditing = true;

            $this->isSpecialSchedule = true;

            // default weekend = libur
            $this->scheduleMode = 'libur';

            $this->masuk = null;
            $this->pulang = null;

            $this->keterangan = 'Hari Libur';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT FRIDAY
        |--------------------------------------------------------------------------
        */

        if ($day == 5) {
            $this->isEditing = true;

            $this->isSpecialSchedule = true;

            $this->scheduleMode = 'khusus';

            $this->masuk = '06:30';
            $this->pulang = '11:30';

            $this->keterangan = 'Jadwal Khusus';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT NORMAL
        |--------------------------------------------------------------------------
        */

        $this->isEditing = true;

        $this->isSpecialSchedule = true;

        $this->scheduleMode = 'normal';

        $this->masuk = '06:30';
        $this->pulang = '16:30';

        $this->keterangan = 'Jadwal Normal';
    }

    public function toggleHoliday()
    {
        /*
        |--------------------------------------------------------------------------
        | JADI LIBUR
        |--------------------------------------------------------------------------
        */

        if ($this->scheduleMode != 'libur') {

            $this->scheduleMode = 'libur';

            $this->masuk = null;
            $this->pulang = null;

            $this->keterangan = 'Hari Libur';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | JADI NORMAL
        |--------------------------------------------------------------------------
        */

        $this->scheduleMode = 'normal';

        $this->masuk = '06:30';
        $this->pulang = '16:30';

        $this->keterangan = 'Jadwal Normal';
    }

    public function goToday()
    {
        $today = now();

        /*
        |--------------------------------------------------------------------------
        | PINDAH BULAN & TAHUN
        |--------------------------------------------------------------------------
        */

        $this->month = $today->month;
        $this->year = $today->year;

        /*
        |--------------------------------------------------------------------------
        | RELOAD CALENDAR
        |--------------------------------------------------------------------------
        */

        $this->generateCalendar();

        $this->loadJadwal();

        /*
        |--------------------------------------------------------------------------
        | AUTO SELECT HARI INI
        |--------------------------------------------------------------------------
        */

        $this->selectDate(
            $today->format('Y-m-d')
        );
    }

    public function render()
    {
        return view('livewire.manajemen.waktu.manajemen-waktu');
    }
}
