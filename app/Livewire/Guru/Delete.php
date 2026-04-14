<?php

namespace App\Livewire\Guru;

use App\Models\Guru\Guru;
use Livewire\Component;

class Delete extends Component
{
    public Guru $guru;

    public function mount(int $id): void
    {
        $this->guru = Guru::findOrFail($id);
    }

    public function destroy(): void
    {
        $this->guru->delete();
    }

    public function render()
    {
        return view("livewire.guru.tambah-guru");
    }
}
