<?php

namespace App\Livewire\Pengawas;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Murid\IzinMurid;
use App\Models\Murid\Murid;
use Livewire\Component;

class CetakIzin extends Component
{
    public $name;
    public $nipd;
    public $alasan;

    public $dariJam;
    public $sampaiJam;

    public $izinMuridId;

    public $muridDitemukan = false;
    
    public function updatedNipd()
    {
        if (!$this->nipd) {
            $this->name = '';
            $this->muridDitemukan = false;
            return;
        }

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
        $validated = ValidateMagic::run([
            'nipd' => ['required', 'exists:murid,nipd'],
            'alasan' => ['required', 'string'],
            'dariJam' => ['required', 'date_format:H:i'],
            'sampaiJam' => ['nullable', 'date_format:H:i'],
        ], [
            'nipd.required' => 'NIPD wajib diisi.',
            'nipd.exists' => 'Murid tidak ditemukan. Silakan masukkan NIPD yang valid.',
            'alasan.required' => 'Alasan izin wajib diisi.',
            'dariJam.required' => 'Jam mulai wajib diisi.',
        ]);

        if(!$validated) {
            return;
        }

        $murid = Murid::where('nipd', $this->nipd)->first();

        if (!$murid) {
            ToastMagic::error('Murid tidak ditemukan. Silakan masukkan NIPD yang valid.');
            return;
        }

        $izinMurid = IzinMurid::create([
            'murid_id' => $murid->id,
            'alasan' => $this->alasan,
            'tanggal' => now()->toDateString(),
            'dari_jam' => $this->dariJam,
            'sampai_jam' => $this->sampaiJam ?? null,
        ]);

        $this->izinMuridId = $izinMurid->id;

        ToastMagic::success('Data izin berhasil disimpan untuk murid: ' . $murid->nama . '.');

        $this->reset(['name', 'nipd', 'alasan', 'dariJam', 'sampaiJam']);

        $url = route('surat-izin', [
            'id' => $this->izinMuridId,
        ]);

        return $this->redirect($url, navigate: true);
    }


    public function render()
    {
        return view('livewire.pengawas.cetak-izin');
    }
}
