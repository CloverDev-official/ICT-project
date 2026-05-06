<?php

namespace App\Livewire\Murid\Absen;

use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Setting;
use Fruitcake\LaravelDebugbar\Facades\Debugbar;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Carbon\Carbon;

class ScanQRCode extends Component
{   
    public Murid $murid;
    public $tersimpan = false;

    #[On('verifiedQRCode')]
    public function verifiedQRCode($muridUlid)
    {
        if (str_starts_with($muridUlid, 's:')) {
            $decoded = @unserialize($muridUlid);
            if ($decoded !== false) {
                $muridUlid = $decoded;
            }
        }

        $murid = Murid::where('ulid', $muridUlid)->first();

        if (!$murid) {
            $this->dispatch('scanNotFound'); // ← tambah ini
            return;
        }

        $settings = cache()->remember('settings', 3600, function () {
            return Setting::pluck('value', 'key');
        });

        $waktuMasuk  = $settings['waktu_masuk'] ?? null;
        $waktuKeluar = $settings['waktu_keluar'] ?? null;

        $now = now();

        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        $absen = AbsenMurid::select('id', 'waktu_keluar')
            ->where('murid_id', $murid->id)
            ->whereDate('tanggal', $today)
            ->first();


        if (!$absen) {
            $status = $currentTime < $waktuMasuk ? 'Hadir' : 'Alpa';

            AbsenMurid::create([
                'murid_id' => $murid->id,
                'tanggal' => $today,
                'waktu_masuk' => $currentTime,
                'status' => $status,
            ]);
        }
        else if ($currentTime > $waktuKeluar && !$absen->waktu_keluar) {
            $absen->update([
                'waktu_keluar' => $currentTime,
            ]);
        }

        $this->murid = $murid;
        $this->tersimpan = true;

        $this->dispatch('scanSuccess');
    }

    #[Layout("layouts.auth")]
    public function render()
    {   
        Carbon::setLocale('id');
        $dateNow = Carbon::now()->translatedFormat('d F Y, l');

        return view('livewire.murid.absen.scan-qrcode', [
            'dateNow' => $dateNow,
        ]);
    }
}