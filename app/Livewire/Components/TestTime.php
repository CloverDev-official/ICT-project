<?php

namespace App\Livewire\Components;

use App\Models\Setting;
use App\Support\TestDatetime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class TestTime extends Component
{
    public string $date;
    public string $time;

    public bool $open = false;

    public function mount(): void
    {
        $testDatetime = request()->cookie('test_datetime') ?? session('test_datetime');

        $datetime = $testDatetime
            ? Carbon::parse($testDatetime)
            : now();

        $this->date = $datetime->format('Y-m-d');
        $this->time = $datetime->format('H:i');
    }

    public function apply(): void
    {
        $this->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
        ]);

        $testDatetime = "{$this->date} {$this->time}:00";

        session(['test_datetime' => $testDatetime]);

        Cookie::queue(Cookie::make('test_datetime', $testDatetime, 60 * 24 * 365));

        if (app()->environment('local')) {
            Setting::upsertValue(TestDatetime::SETTING_KEY, $testDatetime);
        }

        $this->redirect(url()->previous());
    }

    public function resetTestTime(): void
    {
        session()->forget('test_datetime');

        Cookie::queue(Cookie::forget('test_datetime'));

        if (app()->environment('local')) {
            Setting::query()->where('key', TestDatetime::SETTING_KEY)->delete();
        }

        $this->redirect(url()->previous());
    }

    public function render()
    {
        return view('livewire.components.test-time');
    }
}
