<?php

namespace App\Exports\Murid;

use App\Exports\Murid\Rekap\RekapBulananSheet;
use App\Exports\Murid\Rekap\RekapHarianSheet;
use App\Exports\Murid\Rekap\RekapMingguanSheet;
use App\Exports\Murid\Rekap\RekapSemuaKelasSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapKehadiranExport implements WithMultipleSheets
{
    public function __construct(
        private readonly ?string $bulan,
        private readonly ?int $rombelId,
    ) {
    }

    public function sheets(): array
    {
        if (!$this->rombelId) {
            return [new RekapSemuaKelasSheet($this->bulan)];
        }

        return [
            new RekapHarianSheet($this->bulan, $this->rombelId),
            new RekapMingguanSheet($this->bulan, $this->rombelId),
            new RekapBulananSheet($this->bulan, $this->rombelId),
        ];
    }
}
