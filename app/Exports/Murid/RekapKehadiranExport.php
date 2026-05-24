<?php

namespace App\Exports\Murid;

use App\Exports\Murid\Rekap\RekapPerKelasSheet;
use App\Exports\Murid\Rekap\RekapSemuaKelasSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapKehadiranExport implements WithMultipleSheets
{
    public function __construct(
        private readonly ?string $tanggalDari,
        private readonly ?string $tanggalSampai,
        private readonly ?int $rombelId,
    ) {
    }

    public function sheets(): array
    {
        if (!$this->rombelId) {
            return [new RekapSemuaKelasSheet($this->tanggalDari, $this->tanggalSampai)];
        }

        return [new RekapPerKelasSheet($this->tanggalDari, $this->tanggalSampai, $this->rombelId)];
    }
}
