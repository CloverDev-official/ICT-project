<?php

namespace Modules\DataMaster\Livewire\DataGuru\Components\Modal;

use App\Helpers\ToastMagic;
use App\Imports\DataGuruImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class Import extends Component
{
    use WithFileUploads;

    public $file;
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public int $processedRows = 0;
    public int $totalRows = 0;
    public int $importPercent = 0;
    public bool $isImporting = false;
    public int $currentRow = 6;
    public int $chunkSize = 1000;

    public function import(): void
    {
        $this->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $this->resetImportProgress();

        $fullPath = $this->getUploadPath();
        $this->totalRows = $this->countRows($fullPath);
        $this->isImporting = true;
    }

    public function pollProgress(): void
    {
        if (!$this->isImporting) {
            return;
        }

        $this->processNextChunk();
    }

    private function finishImport(): void
    {
        $this->isImporting = false;
        if ($this->file) {
            $this->file->delete();
        }
        $this->reset('file');
        $this->dispatch('guru-refresh');
        $this->dispatch('imported');

        ToastMagic::success(
            'Import selesai',
            "Berhasil: {$this->importedCount} | Dilewati: {$this->skippedCount}."
        );
    }

    private function resetImportProgress(): void
    {
        $this->processedRows = 0;
        $this->totalRows = 0;
        $this->importPercent = 0;
        $this->importedCount = 0;
        $this->skippedCount = 0;
        $this->currentRow = 6;
    }

    private function countRows(string $path): int
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        $highestRow = (int) $worksheet->getHighestRow();

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        if ($highestRow < 6) {
            return 0;
        }

        $total = 0;
        $row = 6;

        while ($row <= $highestRow) {
            $rows = $this->readChunkRows(
                $path,
                $row,
                min($this->chunkSize, $highestRow - $row + 1)
            );

            $total += count($rows);
            $row += $this->chunkSize;
        }

        return $total;
    }

    private function processNextChunk(): void
    {
        $path = $this->getUploadPath();
        $rows = $this->readChunkRows($path, $this->currentRow, $this->chunkSize);

        if (!$rows) {
            $this->finishImport();
            return;
        }

        $import = new DataGuruImport();
        $import->collection(collect($rows));

        $this->importedCount += $import->getImportedCount();
        $this->skippedCount += $import->getSkippedCount();

        $this->processedRows = min($this->processedRows + count($rows), $this->totalRows);
        $this->importPercent = $this->totalRows > 0
            ? (int) round(($this->processedRows / $this->totalRows) * 100)
            : 0;

        $this->currentRow += $this->chunkSize;

        if ($this->processedRows >= $this->totalRows) {
            $this->finishImport();
        }
    }

    private function readChunkRows(string $path, int $startRow, int $chunkSize): array
    {
        $headingRow = 5;
        $endRow = $startRow + $chunkSize - 1;

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new class($headingRow, $startRow, $endRow) implements IReadFilter {
            public function __construct(
                private int $headingRow,
                private int $startRow,
                private int $endRow
            ) {}

            public function readCell($column, $row, $worksheetName = ''): bool
            {
                return $row === $this->headingRow
                    || ($row >= $this->startRow && $row <= $this->endRow);
            }
        });

        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();

        $headingRange = "A{$headingRow}:{$highestColumn}{$headingRow}";
        $headings = $sheet->rangeToArray($headingRange, null, true, false)[0] ?? [];

        $rows = [];
        for ($row = $startRow; $row <= $endRow; $row++) {
            $rowRange = "A{$row}:{$highestColumn}{$row}";
            $values = $sheet->rangeToArray($rowRange, null, true, false)[0] ?? [];

            $assoc = [];
            $hasValue = false;

            foreach ($values as $index => $value) {
                $heading = $headings[$index] ?? null;
                if ($heading === null || $heading === '') {
                    continue;
                }

                $assoc[$heading] = $value;

                if ($value !== null && $value !== '') {
                    $hasValue = true;
                }
            }

            if ($hasValue) {
                $rows[] = $assoc;
            }
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $rows;
    }

    private function getUploadPath(): string
    {
        $path = $this->file?->getRealPath();
        if ($path) {
            return $path;
        }

        $path = $this->file?->getPathname();
        if ($path) {
            return $path;
        }

        throw new \RuntimeException('Upload path is not available.');
    }

    public function render()
    {
        return view('datamaster::livewire.data-guru.components.modal.import');
    }
}
