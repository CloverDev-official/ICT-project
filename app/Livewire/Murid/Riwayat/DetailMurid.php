<?php

namespace App\Livewire\Murid\Riwayat;

use App\Models\Guru\Guru;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class DetailMurid extends Component
{
    public Murid $murid;

    public ?string $filterTanggalDari = null;
    public ?string $filterTanggalSampai = null;

    public function mount(string $muridulid): void
    {
        $this->murid = Murid::query()->where('ulid', $muridulid)->firstOrFail();
        $this->ensureWaliKelasAccess();
        $this->filterTanggalDari = now()->startOfMonth()->toDateString();
        $this->filterTanggalSampai = now()->toDateString();
    }

    private function ensureWaliKelasAccess(): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        $roleSlug = Str::slug($user->role?->name ?? '');

        if ($roleSlug !== 'wali-kelas') {
            return;
        }

        $guru = Guru::query()
            ->where('user_id', $user->id)
            ->first();

        if (!$guru) {
            abort(403);
        }

        $waliRombelIds = Rombel::query()
            ->where('wali_guru_id', $guru->id)
            ->pluck('id')
            ->all();

        if (
            empty($waliRombelIds) ||
            !in_array($this->murid->rombel_id, $waliRombelIds, true)
        ) {
            abort(403);
        }
    }

    private function getTanggalDari(): string
    {
        return $this->filterTanggalDari ?: now()->startOfMonth()->toDateString();
    }

    private function getTanggalSampai(): string
    {
        return $this->filterTanggalSampai ?: now()->toDateString();
    }

    private function formatTanggalLabel(string $value): string
    {
        return strtolower(Carbon::parse($value)->locale('id')->translatedFormat('j F Y'));
    }

    private function getTanggalRangeLabel(): string
    {
        return $this->formatTanggalLabel($this->getTanggalDari()) . ' - ' .
            $this->formatTanggalLabel($this->getTanggalSampai());
    }

    private function getAbsensi()
    {
        return AbsenMurid::query()
            ->where('murid_id', $this->murid->id)
            ->whereBetween('tanggal', [$this->getTanggalDari(), $this->getTanggalSampai()])
            ->orderBy('tanggal')
            ->get(['id', 'murid_id', 'status', 'tanggal']);
    }

    public function render()
    {
        return view('livewire.murid.riwayat.detail-murid', [
            'murid' => $this->murid,
            'listAbsensi' => $this->getAbsensi(),
            'tanggalRangeLabel' => $this->getTanggalRangeLabel(),
        ]);
    }
}
