<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Exports\Murid\Rekap\RekapPerKelasSheet;
use App\Exports\Murid\Rekap\RekapSemuaKelasSheet;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapKehadiranExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_percentage_uses_all_dates_in_the_export_period(): void
    {
        $rombel = Rombel::create([
            'tahun_masuk' => '2026',
            'tingkat_id' => Tingkat::create(['nama' => 'X'])->id,
            'jurusan_id' => Jurusan::create(['nama' => 'RPL'])->id,
            'indeks_id' => Indeks::create(['nama' => 'A'])->id,
        ]);
        $murid = Murid::create([
            'nama' => 'Murid Hadir', 'nipd' => '100', 'nisn' => '200', 'jk' => 'L',
            'tempat_lahir' => 'Banjarmasin', 'tanggal_lahir' => '2010-01-01', 'rombel_id' => $rombel->id,
        ]);

        foreach (CarbonPeriod::create('2026-09-01', '2026-09-20') as $date) {
            AbsenMurid::create([
                'murid_id' => $murid->id,
                'tanggal' => $date->toDateString(),
                'waktu_masuk' => '07:00:00',
                'waktu_keluar' => '16:30:00',
                'status' => AttendanceStatus::Hadir->value,
            ]);
        }

        $perKelas = new RekapPerKelasSheet('2026-09-01', '2026-09-20', $rombel->id);
        $semuaKelas = new RekapSemuaKelasSheet('2026-09-01', '2026-09-20');

        $this->assertSame('100.00%', $perKelas->map($perKelas->query()->first())[6]);
        $this->assertSame('100.00%', $semuaKelas->map($semuaKelas->query()->first())[6]);

        AbsenMurid::query()->where('murid_id', $murid->id)->where('tanggal', '2026-09-20')->delete();

        $this->assertSame('95.00%', $perKelas->map($perKelas->query()->first())[6]);
        $this->assertSame('95.00%', $semuaKelas->map($semuaKelas->query()->first())[6]);
    }
}
