<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ScanQR\Livewire\Murid\Scan;
use Tests\TestCase;

class ScanAudioTest extends TestCase
{
    use RefreshDatabase;

    private function student(): Murid
    {
        $rombel = Rombel::create([
            'tahun_masuk' => '2026',
            'tingkat_id' => Tingkat::create(['nama' => 'X'])->id,
            'jurusan_id' => Jurusan::create(['nama' => 'RPL'])->id,
            'indeks_id' => Indeks::create(['nama' => 'A'])->id,
        ]);

        return Murid::create([
            'nama' => 'Test Scanner', 'nipd' => '100', 'nisn' => '200', 'jk' => 'L',
            'tempat_lahir' => 'Makassar', 'tanggal_lahir' => '2010-01-01', 'rombel_id' => $rombel->id,
        ]);
    }

    private function assertResult(Scan $scan, string $status): void
    {
        $this->assertSame($status, $scan->scanResult['status']);
        $events = \Livewire\store($scan)->get('dispatched', []);
        $last = end($events)->serialize();
        $this->assertSame('scanResult', $last['name']);
        $this->assertSame($scan->scanResult, $last['params']['result']);
    }

    public function test_success_duplicate_and_close(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 8)->setTime(7, 0));
        $student = $this->student();
        $scan = new Scan;
        $scan->verifiedQRCode($student->uuid);
        $this->assertResult($scan, 'success');
        $this->assertDatabaseCount('absen_murid', 1);
        $scan->closeModal();
        $scan->closeModal();
        $this->assertNull($scan->scanResult);
        $scan->verifiedQRCode($student->uuid);
        $this->assertResult($scan, 'already_recorded');
        $this->assertDatabaseCount('absen_murid', 1);
    }

    public function test_late_sound_only_after_confirmation_is_saved(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 8)->setTime(8, 0));
        $student = $this->student();
        $scan = new Scan;
        $scan->verifiedQRCode($student->uuid);
        $this->assertResult($scan, 'late_pending');
        $this->assertDatabaseCount('absen_murid', 0);
        $scan->keterangan = 'Terlambat transportasi';
        $scan->konfirmasiTerlambat();
        $this->assertResult($scan, 'late');
        $this->assertDatabaseHas('absen_murid', ['murid_id' => $student->id, 'status' => AttendanceStatus::Terlambat->value]);
        $scan->verifiedQRCode($student->uuid);
        $this->assertResult($scan, 'already_recorded');
        $this->assertDatabaseCount('absen_murid', 1);
    }

    public function test_unopened_schedule_and_invalid_qr_do_not_save(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 8)->setTime(5, 0));
        $student = $this->student();
        $scan = new Scan;
        $scan->verifiedQRCode($student->uuid);
        $this->assertResult($scan, 'attendance_not_open');
        foreach (['unknown', '', ['invalid']] as $qr) {
            $scan->verifiedQRCode($qr);
            $this->assertResult($scan, 'failed');
        }
        $this->assertDatabaseCount('absen_murid', 0);
    }

    public function test_storage_exception_returns_one_failed_result(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 8)->setTime(7, 0));
        $student = $this->student();
        AbsenMurid::creating(fn () => throw new \RuntimeException('Storage failed'));
        try {
            $scan = new Scan;
            $scan->verifiedQRCode($student->uuid);
            $this->assertResult($scan, 'failed');
            $this->assertCount(1, \Livewire\store($scan)->get('dispatched'));
            $this->assertDatabaseCount('absen_murid', 0);
        } finally {
            AbsenMurid::flushEventListeners();
        }
    }

    public function test_stale_close_does_not_close_new_result(): void
    {
        $scan = new Scan;
        $scan->verifiedQRCode('');
        $previousId = $scan->scanResult['id'];
        $scan->verifiedQRCode('');
        $current = $scan->scanResult;
        $scan->closeModal($previousId);
        $this->assertSame($current, $scan->scanResult);
        $scan->closeModal($current['id']);
        $this->assertNull($scan->scanResult);
    }

    public function test_cancelled_save_is_failed(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 8)->setTime(7, 0));
        $student = $this->student();
        AbsenMurid::creating(fn () => false);
        try {
            $scan = new Scan;
            $scan->verifiedQRCode($student->uuid);
            $this->assertResult($scan, 'failed');
            $this->assertDatabaseCount('absen_murid', 0);
        } finally {
            AbsenMurid::flushEventListeners();
        }
    }
}
