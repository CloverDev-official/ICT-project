<?php

namespace App\Livewire\Murid\Absen;

use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

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
            logger("ULID tidak ditemukan: " . $muridUlid);
            return;
        }

        $settings = cache()->remember('settings', 3600, function () {
            return Setting::pluck('value', 'key');
        });

        $waktuMasuk  = $settings['waktu_masuk'] ?? null;
        $waktuKeluar = $settings['waktu_keluar'] ?? null;

        $today = now()->toDateString();
        $now = now()->toTimeString();

        $absen = AbsenMurid::select('id', 'waktu_keluar')
            ->where('murid_id', $murid->id)
            ->whereDate('tanggal', $today)
            ->first();


        if (!$absen) {
            $status = $now < $waktuMasuk ? 'Hadir' : 'Terlambat';

            AbsenMurid::create([
                'murid_id' => $murid->id,
                'tanggal' => $today,
                'waktu_masuk' => $now,
                'status' => $status,
            ]);
        }
        else if ($now > $waktuKeluar && !$absen->waktu_keluar) {
            AbsenMurid::where('id', $absen->id)
            ->update([
                'waktu_keluar' => $now,
            ]);
        }

        $this->murid = $murid;
        $this->tersimpan = true;

        $this->dispatch('scanSuccess');
    }

    #[Layout("layouts.auth")]
    public function render()
    {
        return view('livewire.murid.scan-qrcode');
    }
}