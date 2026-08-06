<?php

namespace App\Livewire\Pengawas\Laporan;

use App\Helpers\ToastMagic;
use App\Models\Murid\IzinMurid;
use App\Models\Murid\Murid;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Str;

class EditIzin extends Component
{
    public IzinMurid $izin;
    public ?Murid $murid = null;

    public string $nama = '';
    public string $nipd = '';

    public ?string $status = '';
    
    public ?string $tanggal = null;
    public ?string $alasan = null;
    public ?string $dariJam = null;
    public ?string $sampaiJam = null;

    public function mount($id): void
    {
        $this->izin = IzinMurid::query()->with('murid')->findOrFail($id);
        $this->murid = $this->izin->murid;

        $this->fill([
            'nama' => $this->murid?->nama ?? '',
            'nipd' => $this->murid?->nipd ?? '',
            'tanggal' => Carbon::parse($this->izin->getRawOriginal('tanggal'))->format('Y-m-d'),
            'alasan' => $this->izin->alasan,
            'dariJam' => $this->izin->dari_jam ? Carbon::parse($this->izin->dari_jam)->format('H:i') : null,
            'sampaiJam' => $this->izin->sampai_jam ? Carbon::parse($this->izin->sampai_jam)->format('H:i') : null,
            'status' => Str::lower($this->izin->status),
        ]);
    }

    public function update(): void
    {
        $validated = $this->validate([
            'tanggal' => ['required', 'date'],
            'alasan' => ['required', 'string'],
            'dariJam' => ['required', 'date_format:H:i'],
            'sampaiJam' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'string'],
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'alasan.required' => 'Alasan izin wajib diisi.',
            'dariJam.required' => 'Jam mulai wajib diisi.',
            'status.required'=> 'Status wajib diisi',
        ]);

        $this->izin->update([
            'tanggal' => $validated['tanggal'],
            'alasan' => $validated['alasan'],
            'dari_jam' => $validated['dariJam'],
            'sampai_jam' => $validated['sampaiJam'] ?? null,
            'status'=> $validated['status'] ?? 'Izin',
        ]);

        ToastMagic::success('Data izin berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.pengawas.laporan.edit-izin');
    }
}
