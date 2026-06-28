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
    public $listMurid;
    public $alasan;
    public ?int $murid_id = null;
    public ?string $selectedLabel = null;
    public bool $isWaliKelas = false;
    public ?array $waliRombelIds = null;

    public function mount(): void
    {
        $this->applyWaliKelasLock();
        $this->listMurid = $this->getMuridList();
    }

    private function applyWaliKelasLock(): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $roleSlug = Str::slug($user->role?->name ?? '');
        $this->isWaliKelas = in_array($roleSlug, ['wali-kelas', 'wali-murid'], true);

        if (!$this->isWaliKelas) {
            return;
        }

        $guru = Guru::query()->where('user_id', $user->id)->first();
        if (!$guru) {
            $this->waliRombelIds = [];
            return;
        }

        $this->waliRombelIds = Rombel::query()
            ->where('wali_guru_id', $guru->id)
            ->pluck('id')
            ->all();
    }

    private function getMuridList()
    {
        return Murid::query()
            ->aktif()
            ->with(['rombel:id,tingkat_id,jurusan_id,indeks_id'])
            ->when(
                $this->isWaliKelas && $this->waliRombelIds !== null,
                fn ($q) => $q->whereHas('rombel', fn ($q) => $q->whereIn('id', $this->waliRombelIds))
            )
            ->orderBy('nama')
            ->get(['id', 'uuid', 'nama', 'nipd', 'nisn', 'image_path', 'rombel_id']);
    }

    public function cetakIzin()
    {
        if (!$this->murid_id || !$this->alasan) {
            ToastMagic::error('Murid dan alasan harus diisi.');
            return;
        }

        $murid = Murid::find($this->murid_id);
        if (!$murid) {
            ToastMagic::error('Murid tidak ditemukan.');
            return;
        }

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
