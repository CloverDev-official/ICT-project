<?php

namespace App\Livewire\Murid\Absen;

use App\Helpers\ValidateMagic;
use App\Helpers\ToastMagic;
use App\Models\Murid\AbsenMurid;
use Livewire\Component;

class Edit extends Component
{
    public AbsenMurid $absen;
    public ?string $status = null;
    public ?string $keterangan = null;

    public function mount(int $absenId): void
    {
        $this->absen = AbsenMurid::query()
            ->with('murid')
            ->findOrFail($absenId);

        $this->fill([
            'status' => strtolower((string) $this->absen->status),
            'keterangan' => $this->absen->keterangan,
        ]);
    }

    public function update()
    {
        $validate = ValidateMagic::run(
            [
                'status' => 'required|in:hadir,sakit,izin,alpa',
                'keterangan' => 'nullable|string|max:255',
            ],
            [
                'status.required' => 'Status kehadiran wajib dipilih.',
                'status.in' => 'Status kehadiran tidak valid.',
                'keterangan.max' => 'Keterangan maksimal 255 karakter.',
            ],
        );

        if (!$validate) {
            return;
        }

        $this->absen->update([
            'status' => $this->status,
            'keterangan' => $this->keterangan,
        ]);

        ToastMagic::success('Absensi berhasil diperbarui.', null, [], true);

        return $this->redirectRoute('absensi-murid', navigate: true);
    }

    public function render()
    {
        return view("livewire.murid.absen.edit-absen-murid");
    }
}
