<?php

namespace App\Livewire\Manajemen\Waktu;

use Livewire\Component;
use App\Support\DateHelper;
use App\Models\Murid\AbsenMurid;

class ManajemenWaktu extends Component
{
    public $month;
    public $year;
    public $selectedDate;
    public $selectedEvent;

    public $title;
    public $masuk;
    public $pulang;
    public $description;

    public $calendar = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;

        $this->generateCalendar();
    }

    public function generateCalendar()
    {
        $this->calendar = DateHelper::generateCalendar(
            $this->month,
            $this->year
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
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;

        $absen = AbsenMurid::whereDate('tanggal', $date)->first();

        if ($absen) {
            $this->selectedEvent = $absen;

            $this->title = $absen->status; // bisa kamu ganti
            $this->masuk = $absen->waktu_masuk;
            $this->pulang = $absen->waktu_keluar;
            $this->description = $absen->keterangan;
        } else {
            $this->selectedEvent = null;

            $default = \App\Support\DateHelper::getDefaultSchedule($date);

            $this->title = '';
            $this->masuk = $default['masuk'];
            $this->pulang = $default['pulang'];
            $this->description = '';
        }
    }

    public function render()
    {
        return view('livewire.manajemen.waktu.manajemen-waktu');
    }
}