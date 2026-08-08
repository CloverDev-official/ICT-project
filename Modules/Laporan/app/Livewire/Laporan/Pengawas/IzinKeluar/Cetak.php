<?php

namespace Modules\Laporan\Livewire\Laporan\Pengawas\IzinKeluar;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Murid\IzinMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Tingkat;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Cetak extends Component
{
    public ?int $tingkatId = null;

    public ?int $jurusanId = null;

    public ?int $indeksId = null;

    public string $muridSearch = '';

    public ?int $muridId = null;

    public ?string $alasan = null;

    public ?string $dariJam = null;

    public ?string $sampaiJam = null;

    public ?int $izinMuridId = null;

    public function updatedMuridSearch(): void
    {
        $this->muridId = null;
    }

    public function updatedJurusanId(): void
    {
        $this->resetMuridSelection();
    }

    public function updatedTingkatId(): void
    {
        $this->resetMuridSelection();
    }

    public function updatedIndeksId(): void
    {
        $this->resetMuridSelection();
    }

    private function resetMuridSelection(): void
    {
        $this->muridId = null;
        $this->muridSearch = '';
        $this->resetErrorBag('muridId');
    }

    public function selectMurid(int $muridId): void
    {
        $murid = $this->muridQuery()->find($muridId);

        if (! $murid) {
            $this->addError('muridId', 'Murid yang dipilih tidak ditemukan.');

            return;
        }

        $this->muridId = $murid->id;
        $this->muridSearch = $murid->nama;
    }

    public function resetJam()
    {
        $this->sampaiJam = null;
    }

    public function cetakIzin()
    {
        $validated = ValidateMagic::run([
            'muridId' => ['required', 'exists:murid,id'],
            'alasan' => ['required', 'string'],
            'dariJam' => ['required', 'date_format:H:i'],
            'sampaiJam' => ['nullable', 'date_format:H:i'],
        ], [
            'muridId.required' => 'Pilih murid dari daftar pencarian.',
            'muridId.exists' => 'Murid yang dipilih tidak valid.',
            'alasan.required' => 'Alasan izin wajib diisi.',
            'dariJam.required' => 'Jam mulai wajib diisi.',
        ]);

        if (! $validated) {
            return;
        }

        $murid = $this->muridQuery()->find($this->muridId);

        if (! $murid) {
            $this->addError('muridId', 'Murid yang dipilih tidak ditemukan.');

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

        ToastMagic::success('Data izin berhasil disimpan untuk murid: '.$murid->nama.'.');

        $this->reset(['tingkatId', 'jurusanId', 'indeksId', 'muridSearch', 'muridId', 'alasan', 'dariJam', 'sampaiJam']);

        $url = route('surat-izin-keluar', [
            'id' => $this->izinMuridId,
        ]);

        return $this->redirect($url, navigate: true);
    }

    private function muridSuggestions()
    {
        $search = trim($this->muridSearch);

        if ($search === '' || $this->muridId !== null) {
            return collect();
        }

        return $this->muridQuery()
            ->where(function (Builder $query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nipd', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->limit(10)
            ->get();
    }

    private function muridQuery(): Builder
    {
        return Murid::query()
            ->aktif()
            ->when($this->tingkatId, fn (Builder $query) => $query->whereHas(
                'rombel',
                fn (Builder $query) => $query->where('tingkat_id', $this->tingkatId)
            ))
            ->when($this->jurusanId, fn (Builder $query) => $query->whereHas(
                'rombel',
                fn (Builder $query) => $query->where('jurusan_id', $this->jurusanId)
            ))
            ->when($this->indeksId, fn (Builder $query) => $query->whereHas(
                'rombel',
                fn (Builder $query) => $query->where('indeks_id', $this->indeksId)
            ))
            ->with(['rombel.tingkat', 'rombel.jurusan', 'rombel.indeks']);
    }

    public function render()
    {
        return view('laporan::livewire.laporan.pengawas.izin-keluar.cetak', [
            'tingkatList' => Tingkat::query()->orderBy('nama')->get(['id', 'nama']),
            'jurusanList' => Jurusan::query()->orderBy('nama')->get(['id', 'nama']),
            'indeksList' => Indeks::query()->orderBy('nama')->get(['id', 'nama']),
            'muridSuggestions' => $this->muridSuggestions(),
        ]);
    }
}
