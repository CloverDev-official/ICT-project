<?php

namespace Modules\Laporan\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceSummaryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Ringkasan absensi murid berhasil diambil.',
            'data' => $this->resource,
        ];
    }
}
