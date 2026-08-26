<?php

namespace Modules\Absensi\Livewire\Absen\Murid;

use App\Enums\AttendanceStatus;
use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Murid\AbsenMurid;
use Illuminate\Validation\Rule;
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
                'status' => ['required', Rule::in(array_map(
                    static fn (AttendanceStatus $status): string => $status->lowercase(),
                    AttendanceStatus::editable(),
                ))],
                'keterangan' => 'nullable|string|max:255',
            ],
            [
                'status.required' => 'Status kehadiran wajib dipilih.',
                'status.in' => 'Status kehadiran tidak valid.',
                'keterangan.max' => 'Keterangan maksimal 255 karakter.',
            ],
        );

        if (! $validate) {
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
        return view('absensi::livewire.absen.murid.edit');
    }
}
