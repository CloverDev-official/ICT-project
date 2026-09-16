<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\IzinMurid;
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

    public function test_completed_permission_uses_its_own_audio_and_restores_attendance_status(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 8)->setTime(7, 0));
        $student = $this->student();
        $absen = AbsenMurid::create([
            'murid_id' => $student->id,
            'tanggal' => '2026-09-08',
            'waktu_masuk' => '06:55:00',
            'status' => AttendanceStatus::Terlambat->value,
        ]);
        $izin = IzinMurid::create([
            'murid_id' => $student->id, 'tanggal' => '2026-09-08',
            'alasan' => 'Keperluan keluarga', 'dari_jam' => '06:30:00',
            'status' => AttendanceStatus::Izin->value,
            'status_absensi_sebelumnya' => AttendanceStatus::Terlambat->value,
        ]);
        $scan = new Scan;
        $scan->verifiedQRCode($student->uuid.'>'.$izin->uuid);

        $this->assertResult($scan, 'permission_success');
        $this->assertSame(AttendanceStatus::Selesai->value, $izin->fresh()->status);
        $this->assertSame([
            'type' => 'message',
            'message' => 'Izin telah diproses dan status absensi dikembalikan. Silakan gunakan QR code absensi untuk absensi.',
            'title' => 'Izin Berhasil Diperbarui',
        ], $scan->scanResult['modal']);
        $this->assertTrue($scan->scanResult['autoClose']);
        $this->assertFalse($scan->tersimpan);
        $this->assertCount(1, \Livewire\store($scan)->get('dispatched'));
        $this->assertSame(AttendanceStatus::Terlambat->value, $absen->fresh()->status);
        $this->assertDatabaseCount('absen_murid', 1);

        $scan->verifiedQRCode($student->uuid.'>'.$izin->uuid);
        $this->assertResult($scan, 'failed');
        $this->assertDatabaseCount('absen_murid', 1);
        $scan->verifiedQRCode($student->uuid);
        $this->assertResult($scan, 'already_recorded');
        $this->assertDatabaseCount('absen_murid', 1);
    }

    public function test_completed_permission_after_its_date_does_not_restore_attendance_status(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 9)->setTime(7, 0));
        $student = $this->student();
        $absen = AbsenMurid::create([
            'murid_id' => $student->id,
            'tanggal' => '2026-09-08',
            'waktu_masuk' => '06:55:00',
            'status' => AttendanceStatus::Izin->value,
        ]);
        $izin = IzinMurid::create([
            'murid_id' => $student->id,
            'tanggal' => '2026-09-08',
            'alasan' => 'Keperluan keluarga',
            'dari_jam' => '06:30:00',
            'status' => AttendanceStatus::Izin->value,
            'status_absensi_sebelumnya' => AttendanceStatus::Terlambat->value,
        ]);

        $scan = new Scan;
        $scan->verifiedQRCode($student->uuid.'>'.$izin->uuid);

        $this->assertResult($scan, 'permission_success');
        $this->assertSame(AttendanceStatus::Selesai->value, $izin->fresh()->status);
        $this->assertSame(AttendanceStatus::Izin->value, $absen->fresh()->status);
        $this->assertSame(
            'Izin telah diproses. Status absensi tidak diubah karena QR dipindai setelah tanggal izin. Silakan gunakan QR code absensi untuk absensi.',
            $scan->scanResult['modal']['message'],
        );
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

    public function test_late_scan_saves_immediately_and_uses_the_success_modal(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 8)->setTime(8, 0));
        $student = $this->student();
        $scan = new Scan;
        $scan->verifiedQRCode($student->uuid);
        $this->assertResult($scan, 'late');
        $this->assertTrue($scan->tersimpan);
        $this->assertSame('success', $scan->scanStatus);
        $this->assertTrue($scan->scanResult['autoClose']);
        $this->assertCount(1, \Livewire\store($scan)->get('dispatched'));
        $this->assertDatabaseHas('absen_murid', ['murid_id' => $student->id, 'status' => AttendanceStatus::Terlambat->value]);
        $scan->verifiedQRCode($student->uuid);
        $this->assertResult($scan, 'already_recorded');
        $this->assertDatabaseCount('absen_murid', 1);
    }

    public function test_alpa_attendance_becomes_late_between_entry_and_exit_windows(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 8)->setTime(8, 0));
        $student = $this->student();
        $absen = AbsenMurid::create([
            'murid_id' => $student->id,
            'tanggal' => '2026-09-08',
            'status' => AttendanceStatus::Alpa->value,
            'keterangan' => 'Otomatis alpa setelah batas scan masuk - Pulang Cepat - Wow',
        ]);

        $scan = new Scan;
        $scan->verifiedQRCode($student->uuid);

        $this->assertResult($scan, 'late');
        $this->assertSame(AttendanceStatus::Terlambat->value, $absen->fresh()->status);
        $this->assertSame('08:00:00', $absen->fresh()->waktu_masuk);
        $this->assertNull($absen->fresh()->keterangan);
        $this->assertDatabaseCount('absen_murid', 1);
    }

    public function test_alpa_attendance_is_not_changed_when_exit_window_has_started(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 8)->setTime(16, 30));
        $student = $this->student();
        $absen = AbsenMurid::create([
            'murid_id' => $student->id,
            'tanggal' => '2026-09-08',
            'status' => AttendanceStatus::Alpa->value,
        ]);

        $scan = new Scan;
        $scan->verifiedQRCode($student->uuid);

        $this->assertResult($scan, 'failed');
        $this->assertSame(AttendanceStatus::Alpa->value, $absen->fresh()->status);
        $this->assertNull($absen->fresh()->waktu_masuk);
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

    public function test_normal_and_late_scans_render_the_same_success_modal(): void
    {
        $student = $this->student();
        foreach ([7 => 'success', 8 => 'late'] as $hour => $status) {
            $this->travelTo(now()->setDate(2026, 9, 8)->setTime($hour, 0));
            \Livewire\Livewire::test(Scan::class)
                ->call('verifiedQRCode', $student->uuid)
                ->assertSet('scanResult.status', $status)
                ->assertSeeHtml('id="success-modal"')
                ->assertDontSeeHtml('id="error-modal"')
                ->assertDontSee('Alasan terlambat');
            AbsenMurid::where('murid_id', $student->id)->forceDelete();
        }
    }

    public function test_late_letter_requires_reason_and_loads_updated_reason_on_reprint(): void
    {
        $role = \App\Models\Role::create(['name' => 'Pengawas', 'permissions' => []]);
        $this->actingAs(\App\Models\User::create([
            'name' => 'Pengawas Test', 'email' => 'pengawas@example.test',
            'password' => 'password', 'role_id' => $role->id, 'is_active' => true,
        ]));
        $student = $this->student();
        $absen = AbsenMurid::create([
            'murid_id' => $student->id,
            'tanggal' => '2026-09-08',
            'waktu_masuk' => '08:00:00',
            'status' => AttendanceStatus::Terlambat->value,
        ]);
        $component = \Livewire\Livewire::test(\Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat\Cetak::class)
            ->assertSet('showAlasan', false)
            ->call('bukaAlasan', $absen->id)
            ->assertSet('showAlasan', true)
            ->assertSee('Alasan Keterlambatan')
            ->assertNoRedirect()
            ->set('alasan', '   ')
            ->call('simpanDanCetak')
            ->assertHasErrors(['alasan' => 'required'])
            ->assertSee('Alasan keterlambatan wajib diisi.')
            ->assertSet('showAlasan', true)
            ->assertNoRedirect();
        $this->assertNull($absen->fresh()->keterangan);
        $component->set('alasan', str_repeat('a', 192))
            ->call('simpanDanCetak')
            ->assertHasErrors(['alasan' => 'max'])
            ->assertNoRedirect();
        $component->set('alasan', '  Kendaraan bermasalah  ')
            ->call('simpanDanCetak')
            ->assertHasNoErrors()
            ->assertRedirect(route('surat-izin-telat', $absen->id));
        $this->assertSame('Kendaraan bermasalah', $absen->fresh()->keterangan);
        \Livewire\Livewire::test(\Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat\Surat::class, ['id' => $absen->id])
            ->assertSeeHtml('id="printArea"')
            ->assertSee('Kendaraan bermasalah')
            ->assertSee('08:00:00')
            ->assertDontSeeHtml('wire:submit="simpanDanCetak"');
        \Livewire\Livewire::test(\Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat\Cetak::class)
            ->call('bukaAlasan', $absen->id)
            ->assertSet('alasan', 'Kendaraan bermasalah')
            ->set('alasan', 'Hujan deras')
            ->call('simpanDanCetak')
            ->assertHasNoErrors()
            ->assertRedirect(route('surat-izin-telat', $absen->id));
        $this->assertSame('Hujan deras', $absen->fresh()->keterangan);
        \Livewire\Livewire::test(\Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat\Surat::class, ['id' => $absen->id])
            ->assertSee('Hujan deras');
        $this->assertSame(AttendanceStatus::Terlambat->value, $absen->fresh()->status);
        $this->assertDatabaseCount('absen_murid', 1);
    }

    public function test_cancel_reason_discards_changes_and_clears_validation(): void
    {
        $student = $this->student();
        $absen = AbsenMurid::create([
            'murid_id' => $student->id, 'tanggal' => '2026-09-08',
            'status' => AttendanceStatus::Terlambat->value, 'keterangan' => 'Macet',
        ]);
        \Livewire\Livewire::test(\Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat\Cetak::class)
            ->call('bukaAlasan', $absen->id)
            ->set('alasan', '')
            ->call('simpanDanCetak')
            ->assertHasErrors('alasan')
            ->call('tutupAlasan')
            ->assertSet('showAlasan', false)
            ->assertSet('absenCetak', null)
            ->assertHasNoErrors()
            ->call('bukaAlasan', $absen->id)
            ->assertSet('alasan', 'Macet');
        $this->assertSame('Macet', $absen->fresh()->keterangan);
    }

    public function test_letter_without_reason_redirects_to_list_without_receipt(): void
    {
        $student = $this->student();
        $absen = AbsenMurid::create([
            'murid_id' => $student->id, 'tanggal' => '2026-09-08',
            'status' => AttendanceStatus::Terlambat->value,
        ]);
        \Livewire\Livewire::test(\Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat\Surat::class, ['id' => $absen->id])
            ->assertRedirect(route('cetak-izin-telat'))
            ->assertDontSeeHtml('id="printArea"');
    }

    public function test_normal_attendance_cannot_open_late_letter(): void
    {
        $student = $this->student();
        $absen = AbsenMurid::create([
            'murid_id' => $student->id,
            'tanggal' => '2026-09-08',
            'waktu_masuk' => '07:00:00',
            'status' => AttendanceStatus::Masuk->value,
        ]);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        (new \Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat\Surat)->mount($absen->id);
    }
}
