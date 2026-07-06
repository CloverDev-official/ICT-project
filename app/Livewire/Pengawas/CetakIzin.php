<?php

namespace App\Livewire\Pengawas;

use App\Helpers\ToastMagic;
use App\Models\Guru\Guru;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use Illuminate\Support\Str;
use Livewire\Component;

class CetakIzin extends Component
{
    public $name;
    public $nipd;
    public $alasan;

    public $dariJam;
    public $sampaiJam;

    public $muridDitemukan = false;
    
    public function updatedNipd()
    {
        $murid = Murid::where('nipd', $this->nipd)->first();
        if ($murid) {
            $this->name = $murid->nama;
            $this->muridDitemukan = true;
        } else {
            $this->name = '';
            $this->muridDitemukan = false;
            ToastMagic::warning('Tidak ada murid dengan NIPD ' . $this->nipd . '.');
        }
    }

    public function resetJam()
    {
        $this->sampaiJam = null;
    }

    public function cetakIzin()
    {
        if (!$this->muridDitemukan) {
            ToastMagic::error('Murid tidak ditemukan. Silakan masukkan NIPD yang valid.');
            return;
        }

        if (!$this->nipd || !$this->alasan) {
            ToastMagic::error('Murid dan alasan harus diisi.');
            return;
        }

        dd($this->nipd, $this->name,  $this->alasan, $this->dariJam, $this->sampaiJam);

        // Logika untuk mencetak izin (misalnya, membuat PDF atau mengirim ke printer)
        // ...

        // $this->dispatchBrowserEvent('show-toast', [
        //     'type' => 'success',
        //     'message' => 'Izin berhasil dicetak untuk murid: ' . $murid->nama,
        // ]);
    }


    public function render()
    {
        return view('livewire.pengawas.cetak-izin');
    }
}
