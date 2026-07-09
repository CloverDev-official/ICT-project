<?php

namespace App\Livewire\Pengawas\Laporan;

use App\Models\Murid\IzinMurid;
use Livewire\Component;
use Livewire\WithPagination;

class LaporanIzin extends Component
{
    use WithPagination;

    public $search = '';
    protected $listeners = ['izin-refresh' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $dataIzin = IzinMurid::query()
            ->with(['murid:id,nama,nipd'])
            ->when($this->search, function ($query) {
                $search = trim($this->search);

                $query->where('alasan', 'like', '%' . $search . '%')
                    ->orWhereHas('murid', function ($muridQuery) use ($search) {
                        $muridQuery->where('nama', 'like', '%' . $search . '%')
                            ->orWhere('nipd', 'like', '%' . $search . '%');
                    });
            })
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10);

        return view('livewire.pengawas.laporan.laporan-izin', [
            'dataIzin' => $dataIzin,
        ]);
    }
}
