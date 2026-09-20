<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\StudentStatus;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\IzinMurid;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\ScanQR\Services\AutoAlpaMuridService;
use Tests\TestCase;

class AutoAlpaMuridServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_marks_only_active_students_without_attendance_and_uses_the_run_timestamp(): void
    {
        Carbon::setTestNow('2026-09-16 07:31:00');
        $rombelId = $this->createRombel();
        $missingStudentId = $this->createStudent($rombelId, 'missing');
        $presentStudentId = $this->createStudent($rombelId, 'present');
        $this->createStudent($rombelId, 'inactive', 'nonaktif');

        DB::table('absen_murid')->insert([
            'murid_id' => $presentStudentId,
            'tanggal' => '2026-09-16',
            'status' => AttendanceStatus::Hadir->value,
            'created_at' => '2026-09-16 07:00:00',
            'updated_at' => '2026-09-16 07:00:00',
        ]);

        $created = app(AutoAlpaMuridService::class)->syncForRombel(
            $rombelId,
            '2026-09-16',
            $this->normalSchedule(),
            now(),
        );

        $this->assertSame(1, $created);
        $this->assertDatabaseHas('absen_murid', [
            'murid_id' => $missingStudentId,
            'tanggal' => '2026-09-16',
            'status' => AttendanceStatus::Alpa->value,
            'created_at' => '2026-09-16 07:31:00',
            'updated_at' => '2026-09-16 07:31:00',
        ]);
        $this->assertDatabaseCount('absen_murid', 2);

        $this->assertSame(0, app(AutoAlpaMuridService::class)->syncForRombel(
            $rombelId,
            '2026-09-16',
            $this->normalSchedule(),
            now(),
        ));
    }

    public function test_it_skips_non_attendance_schedules_and_invalid_scan_end_times(): void
    {
        Carbon::setTestNow('2026-09-16 07:31:00');
        $rombelId = $this->createRombel();
        $studentId = $this->createStudent($rombelId, 'scheduled-skip');
        $service = app(AutoAlpaMuridService::class);

        $holidaySchedule = $this->normalSchedule();
        $holidaySchedule['tipe'] = 'libur';
        $this->assertSame(0, $service->syncForRombel($rombelId, '2026-09-16', $holidaySchedule, now()));

        $invalidTimeSchedule = $this->normalSchedule();
        $invalidTimeSchedule['scan_masuk_sampai'] = '25:00';
        $this->assertSame(0, $service->syncForRombel($rombelId, '2026-09-16', $invalidTimeSchedule, now()));

        $this->assertDatabaseMissing('absen_murid', ['murid_id' => $studentId]);
    }

    public function test_it_marks_only_expired_unscanned_izin_keluar_as_alpa(): void
    {
        Carbon::setTestNow('2026-09-17 00:01:00');
        $rombelId = $this->createRombel();
        $izinKeluarStudentId = $this->createStudent($rombelId, 'izin-keluar');
        $manualIzinStudentId = $this->createStudent($rombelId, 'manual-izin');
        $changedStudentId = $this->createStudent($rombelId, 'changed-izin');

        $izinKeluarAbsen = AbsenMurid::create([
            'murid_id' => $izinKeluarStudentId,
            'tanggal' => '2026-09-16',
            'waktu_masuk' => '07:00:00',
            'status' => AttendanceStatus::Izin->value,
        ]);
        $izinKeluar = IzinMurid::create([
            'murid_id' => $izinKeluarStudentId,
            'tanggal' => '2026-09-16',
            'alasan' => 'Keperluan keluarga',
            'dari_jam' => '10:00:00',
            'status' => AttendanceStatus::Izin->value,
            'status_absensi_sebelumnya' => AttendanceStatus::Hadir->value,
        ]);

        AbsenMurid::create([
            'murid_id' => $manualIzinStudentId,
            'tanggal' => '2026-09-16',
            'status' => AttendanceStatus::Izin->value,
        ]);
        $changedAbsen = AbsenMurid::create([
            'murid_id' => $changedStudentId,
            'tanggal' => '2026-09-16',
            'status' => AttendanceStatus::Sakit->value,
        ]);
        $changedIzin = IzinMurid::create([
            'murid_id' => $changedStudentId,
            'tanggal' => '2026-09-16',
            'alasan' => 'Status diubah manual',
            'dari_jam' => '10:00:00',
            'status' => AttendanceStatus::Izin->value,
        ]);

        $updated = app(AutoAlpaMuridService::class)->syncExpiredIzinKeluar(now());

        $this->assertSame(1, $updated);
        $this->assertSame(AttendanceStatus::Alpa->value, $izinKeluar->fresh()->status);
        $this->assertSame(AttendanceStatus::Alpa->value, $izinKeluarAbsen->fresh()->status);
        $this->assertSame(
            'Izin keluar tidak diselesaikan dengan scan QR pada tanggal izin.',
            $izinKeluarAbsen->fresh()->keterangan,
        );
        $this->assertDatabaseHas('absen_murid', [
            'murid_id' => $manualIzinStudentId,
            'status' => AttendanceStatus::Izin->value,
        ]);
        $this->assertSame(AttendanceStatus::Izin->value, $changedIzin->fresh()->status);
        $this->assertSame(AttendanceStatus::Sakit->value, $changedAbsen->fresh()->status);
    }

    private function createRombel(): int
    {
        return DB::table('rombel')->insertGetId([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createStudent(int $rombelId, string $suffix, string $status = StudentStatus::Aktif->value): int
    {
        return DB::table('murid')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'nama' => "Student {$suffix}",
            'nipd' => "nipd-{$suffix}",
            'jk' => 'L',
            'nisn' => "nisn-{$suffix}",
            'tempat_lahir' => 'Banjarmasin',
            'tanggal_lahir' => '2010-01-01',
            'rombel_id' => $rombelId,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @return array<string, string> */
    private function normalSchedule(): array
    {
        return [
            'tipe' => 'normal',
            'scan_masuk_sampai' => '07:30',
            'label' => 'Jadwal reguler',
        ];
    }
}
