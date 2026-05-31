<?php

namespace App\Livewire\Manajemen;

use App\Helpers\QRCodeHelper;
use App\Models\Murid\Murid;
use App\Services\Rombel\RombelFilterService;
use Fruitcake\LaravelDebugbar\Facades\Debugbar;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipStream\ZipStream;

class GenerateQR extends Component
{
    use WithFileUploads;

    public $listRombel;
    public $filteredJurusan;
    public $filteredIndeks;

    public ?int $filterIndeks = null;
    public ?int $filterTingkat = null;
    public ?int $filterJurusan = null;
    public ?int $activeFilterIndeks = null;
    public ?int $activeFilterTingkat = null;
    public ?int $activeFilterJurusan = null;
    public int $totalData = 0;
    public int $lastId = 0;
    public bool $isGenerating = false;
    public int $runId = 0;

    public function mount(): void
    {
        $this->listRombel = $this->getRombel();
        $this->refreshFilterOptions();
    }

    private function rombelFilterService(): RombelFilterService
    {
        return app(RombelFilterService::class);
    }

    private function applyRombelFilter($q)
    {
        $filterTingkat = $this->isGenerating ? $this->activeFilterTingkat : $this->filterTingkat;
        $filterJurusan = $this->isGenerating ? $this->activeFilterJurusan : $this->filterJurusan;
        $filterIndeks = $this->isGenerating ? $this->activeFilterIndeks : $this->filterIndeks;
        $this->rombelFilterService()->applyRombelFilters($q, $filterTingkat, $filterJurusan, $filterIndeks, null);
    }

    private function rombelBaseQuery()
    {
        return app(RombelFilterService::class)->getRombelList(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            null,
        );
    }

    private function getRombel()
    {
        return $this->rombelFilterService()->getRombelList(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            null,
        );
    }

    private function getAvailableJurusan()
    {
        return $this->rombelFilterService()->getAvailableJurusan(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            null,
        );
    }

    private function getAvailableIndeks()
    {
        return $this->rombelFilterService()->getAvailableIndeks(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            null,
        );
    }

    private function loadChunk()
    {
        $hasFilter = $this->isGenerating
            ? ($this->activeFilterTingkat || $this->activeFilterJurusan || $this->activeFilterIndeks)
            : ($this->filterTingkat || $this->filterJurusan || $this->filterIndeks);

        $query = Murid::query()
            ->with([
                'rombel:id,tingkat_id,jurusan_id,indeks_id',
            ])
            ->tap(fn ($q) => $this->rombelFilterService()->applyRombelFiltersToRelation(
                $q,
                'rombel',
                $this->isGenerating ? $this->activeFilterTingkat : $this->filterTingkat,
                $this->isGenerating ? $this->activeFilterJurusan : $this->filterJurusan,
                $this->isGenerating ? $this->activeFilterIndeks : $this->filterIndeks,
                null,
            ));

        $this->totalData = $this->totalData?: (clone $query)->count();

        $data = $query
            ->where('id', '>', $this->lastId)
            ->orderBy('id')
            ->take(1000)
            ->get(['id', 'ulid', 'nama', 'nisn', 'image_path' ,'rombel_id']);

            if ($data->isNotEmpty()) {
                $this->lastId = $data->last()->id;
            }

        return $data;
    }

    private function refreshFilterOptions(): void
    {
        $this->filteredJurusan = $this->getAvailableJurusan();
        $this->filteredIndeks = $this->getAvailableIndeks();
    }
    public function updatedFilterTingkat(): void
    {
        $this->refreshFilterOptions();
    }

    public function updatedFilterJurusan(): void
    {
        $this->refreshFilterOptions();
    }

    public function updatedFilterIndeks(): void
    {
        $this->refreshFilterOptions();
    }

    public float $startedAt;

    public function startGenerate()
    {
        if($this->isGenerating) {
            Debugbar::warning('Generate QR is already in progress');
            return;
        }

        $this->isGenerating = true;
        $this->runId++;
        $this->activeFilterTingkat = $this->filterTingkat;
        $this->activeFilterJurusan = $this->filterJurusan;
        $this->activeFilterIndeks = $this->filterIndeks;
        
        $this->startedAt = microtime(true);
        $this->lastId = 0;
        $this->totalData = 0;
        $chunkData = $this->loadChunk();

        $this->dispatch(
            'generate-qr',
            runId: $this->runId,
            dataMurid: $chunkData->toArray(),
            totalData: $this->totalData,
        );
    }

    public function nextChunk()
    {
        $chunkData = $this->loadChunk();

        if (!$chunkData->isNotEmpty()) {
            $duration = microtime(true) - $this->startedAt;
            Debugbar::info("Total generator time: {$duration} seconds");
            $this->lastId = 0;
            $this->totalData = 0;
            $this->isGenerating = false;
            $this->activeFilterTingkat = null;
            $this->activeFilterJurusan = null;
            $this->activeFilterIndeks = null;
            return;
        }

        $this->dispatch(
            'generate-qr',
            runId: $this->runId,
            dataMurid: $chunkData->toArray(),
            totalData: $this->totalData,
        );
    }

    public function render()
    {
        return view('livewire.manajemen.generate-qr');
    }
}
