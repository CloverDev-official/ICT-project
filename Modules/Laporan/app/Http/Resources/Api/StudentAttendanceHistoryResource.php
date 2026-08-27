<?php

namespace Modules\Laporan\Http\Resources\Api;

use App\Enums\AttendanceStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceHistoryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $murid = $this->resource->murid;
        $rombel = $murid?->rombel;
        $status = AttendanceStatus::fromCode((string) $this->resource->status);

        return [
            'id' => $this->resource->id,
            'murid' => [
                'id' => $murid?->id,
                'uuid' => $murid?->uuid,
                'nama' => $murid?->nama,
                'nipd' => $murid?->nipd,
                'nisn' => $murid?->nisn,
            ],
            'jurusan' => $rombel?->jurusan ? [
                'id' => $rombel->jurusan->id,
                'nama' => $rombel->jurusan->nama,
            ] : null,
            'tingkat' => $rombel?->tingkat ? [
                'id' => $rombel->tingkat->id,
                'nama' => $rombel->tingkat->nama,
            ] : null,
            'indeks' => $rombel?->indeks ? [
                'id' => $rombel->indeks->id,
                'nama' => $rombel->indeks->nama,
            ] : null,
            'status' => [
                'code' => $status?->lowercase(),
                'label' => $status?->value,
            ],
            'tanggal' => $this->resource->tanggal?->format('Y-m-d'),
        ];
    }
}
