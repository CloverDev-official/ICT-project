<?php

namespace Tests\Feature;

use App\Models\JadwalAbsen;
use App\Models\JadwalAbsenRombel;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use App\Services\Rombel\RombelFilterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ScanQR\Livewire\Murid\Scan;
use Modules\ScanQR\Services\JadwalAbsensiService;
use Tests\TestCase;

class QueryOutputRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_scan_uses_only_current_date_and_non_deleted_attendance(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 16)->setTime(7, 0));
        $student = Murid::create([
            'nama' => 'Scan Test', 'nipd' => '100', 'nisn' => '200', 'jk' => 'L',
            'tempat_lahir' => 'Makassar', 'tanggal_lahir' => '2010-01-01',
            'rombel_id' => Rombel::create([])->id,
        ]);
        foreach (['2026-09-15', '2026-09-16', '2026-09-17'] as $date) {
            $attendance = AbsenMurid::create([
                'murid_id' => $student->id, 'tanggal' => $date, 'status' => 'Hadir',
                'waktu_masuk' => '06:30:00', 'waktu_keluar' => '16:00:00',
            ]);
            if ($date === '2026-09-16') {
                $attendance->delete();
            }
        }
        $scan = new Scan;
        $scan->verifiedQRCode($student->uuid);
        $this->assertSame('success', $scan->scanResult['status']);
        $this->assertSame('Absensi masuk berhasil disimpan.', $scan->scanResult['modal']['message']);
        $this->assertDatabaseCount('absen_murid', 4);
        $saved = AbsenMurid::where('tanggal', '2026-09-16')->firstOrFail();
        $this->assertSame('07:00:00', $saved->waktu_masuk);
        $this->assertNull($saved->waktu_keluar);

        $scan->verifiedQRCode($student->uuid);
        $this->assertSame('already_recorded', $scan->scanResult['status']);
        $this->assertSame('Absensi masuk sudah tercatat', $scan->scanResult['modal']['title']);
        $this->travelTo(now()->setTime(17, 0));
        $scan->verifiedQRCode($student->uuid);
        $this->assertSame('success', $scan->scanResult['status']);
        $this->assertSame('Absensi pulang berhasil disimpan.', $scan->scanResult['modal']['message']);
        $this->assertSame('17:00:00', $saved->fresh()->waktu_keluar);
        $scan->verifiedQRCode($student->uuid);
        $this->assertSame('Murid sudah pulang', $scan->scanResult['modal']['title']);
        $this->assertDatabaseCount('absen_murid', 4);
    }

    public function test_schedule_preserves_complete_output_for_defaults_global_and_specific_events(): void
    {
        $service = app(JadwalAbsensiService::class);
        $rombel = Rombel::create([]);
        $other = Rombel::create([]);

        foreach (['2026-09-16', '2026-09-18', '2026-09-19', '2026-09-20'] as $date) {
            $default = $service->forRombel(null, $date);
            $this->assertSame($default, $service->forRombel($rombel->id, $date));
            $this->assertSame([$other->id => $default, $rombel->id => $default],
                $service->forRombels([$other->id, null, $rombel->id, $other->id], $date));

            $event = JadwalAbsen::create([
                'tanggal' => $date, 'tipe' => 'khusus', 'nama_acara' => 'Acara', 'keterangan' => 'Global',
            ]);
            $global = array_replace($default, [
                'tipe' => 'khusus', 'nama_acara' => 'Acara', 'keterangan' => 'Global',
                'source' => 'global', 'label' => 'Hari Spesial',
            ]);
            $this->assertSame($global, $service->forRombel($rombel->id, $date));
            $this->assertSame([$rombel->id => $global, $other->id => $global],
                $service->forRombels([$rombel->id, $other->id], $date));

            $detail = JadwalAbsenRombel::create([
                'jadwal_absen_id' => $event->id, 'rombel_id' => $rombel->id,
                'tipe' => 'khusus', 'gunakan_window_scan' => true,
                'scan_masuk_mulai' => '06:15:00', 'scan_masuk_sampai' => '07:45:00',
                'keterangan' => 'Kelas',
            ]);
            // Keep key order as well as values: scan_window_source precedes nama_acara.
            $specific = array_slice($global, 0, 8, true)
                + ['scan_window_source' => 'rombel']
                + array_slice($global, 8, null, true);
            $specific = array_replace($specific, [
                'scan_masuk_mulai' => '06:15', 'scan_masuk_sampai' => '07:45',
                'keterangan' => 'Kelas', 'source' => 'rombel',
            ]);
            $this->assertSame($specific, $service->forRombel($rombel->id, $date));
            $this->assertSame($default, $service->forRombel($other->id, $date));
            $this->assertSame([$other->id => $default, $rombel->id => $specific],
                $service->forRombels([$other->id, $rombel->id], $date));

            foreach (['pjj', 'libur'] as $type) {
                $detail->update(['tipe' => $type, 'gunakan_window_scan' => false]);
                $closed = array_replace($specific, [
                    'tipe' => $type, 'label' => $type === 'pjj' ? 'PJJ' : 'Libur',
                    'scan_window_source' => 'default', 'scan_masuk_mulai' => null,
                    'scan_masuk_sampai' => null, 'scan_keluar_mulai' => null, 'scan_keluar_sampai' => null,
                ]);
                $this->assertSame($closed, $service->forRombel($rombel->id, $date));
                $this->assertSame([$rombel->id => $closed], $service->forRombels([$rombel->id], $date));
            }
        }
        $this->assertSame([], $service->forRombels([]));
    }

    public function test_filter_options_preserve_duplicates_nulls_soft_deletes_self_filters_and_order(): void
    {
        $service = app(RombelFilterService::class);
        $tingkat = Tingkat::create(['nama' => 'X']);
        $z = Jurusan::create(['nama' => 'Z']);
        $a = Jurusan::create(['nama' => 'A']);
        $deleted = Jurusan::create(['nama' => 'Deleted']);
        $i = Indeks::create(['nama' => '1']);
        $j = Indeks::create(['nama' => '2']);
        $rombel = Rombel::create(['tingkat_id' => $tingkat->id, 'jurusan_id' => $z->id, 'indeks_id' => $i->id]);
        Rombel::create(['tingkat_id' => $tingkat->id, 'jurusan_id' => $z->id, 'indeks_id' => $j->id]);
        Rombel::create(['jurusan_id' => $a->id, 'indeks_id' => $i->id]);
        Rombel::create(['jurusan_id' => $deleted->id]);
        Rombel::create([]);
        $deleted->delete();
        $onlyDeleted = Jurusan::create(['nama' => 'Only deleted class']);
        Rombel::create(['jurusan_id' => $onlyDeleted->id])->delete();

        $this->assertSame([$a->only('id', 'nama'), $z->only('id', 'nama')],
            $service->getAvailableJurusan(null, $z->id, null, null)->toArray());
        $this->assertSame([$z->only('id', 'nama')],
            $service->getAvailableJurusan($tingkat->id, null, $i->id, null)->toArray());
        $this->assertSame([$z->only('id', 'nama')],
            $service->getAvailableJurusan(null, null, null, [$rombel->id, $rombel->id])->toArray());
        $this->assertSame([], $service->getAvailableJurusan(null, null, null, [])->toArray());
        $this->assertSame([$i->only('id', 'nama'), $j->only('id', 'nama')],
            $service->getAvailableIndeks($tingkat->id, $z->id, $i->id, null)->toArray());
        $this->assertSame([$i->only('id', 'nama')],
            $service->getAvailableIndeks(null, null, null, [$rombel->id])->toArray());
        $this->assertSame([], $service->getAvailableIndeks(null, null, null, [])->toArray());
    }
}
