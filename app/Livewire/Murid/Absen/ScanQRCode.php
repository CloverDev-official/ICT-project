<?php

namespace App\Livewire\Murid\Absen;

use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Services\Absensi\JadwalAbsensiService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class ScanQRCode extends Component
{
    public ?Murid $murid = null;
    public bool $tersimpan = false;
    public ?string $scanStatus = null;
    public ?string $scanMessage = null;
    public array $jadwalHariIni = [];

    #[On('verifiedQRCode')]
    public function verifiedQRCode($muridUlid): void
    {
        $this->resetScanState();

        if (str_starts_with($muridUlid, 's:')) {
            $decoded = @unserialize($muridUlid);
            if ($decoded !== false) {
                $muridUlid = $decoded;
            }
        }

        $murid = Murid::query()
            ->with('rombel')
            ->where('ulid', $muridUlid)
            ->first();

        if (!$murid) {
            $this->rejectScan('QR murid tidak ditemukan di database.');
            return;
        }

        if (!$murid->rombel_id) {
            $this->murid = $murid;
            $this->rejectScan('Murid belum memiliki kelas/rombel, absensi tidak dapat disimpan.');
            return;
        }

        $jadwalService = app(JadwalAbsensiService::class);
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');
        $jadwal = $jadwalService->forRombel($murid->rombel_id, $today);

        $this->murid = $murid;
        $this->jadwalHariIni = $jadwal;

        if (!$jadwalService->bolehScan($jadwal)) {
            $this->rejectScan('Absensi Tidak Disimpan. Sistem mengecek jadwal kelas dari Manajemen Waktu.');
            return;
        }

        $absen = AbsenMurid::query()
            ->where('murid_id', $murid->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absen) {
            if ($this->bisaLangsungPulang($currentTime, $jadwal)) {
                AbsenMurid::create([
                    'murid_id' => $murid->id,
                    'tanggal' => $today,
                    'waktu_keluar' => $currentTime,
                    'status' => 'Hadir',
                    'keterangan' => $this->keteranganAbsensi($jadwal),
                ]);

                $this->acceptScan('Absensi pulang berhasil disimpan.');
                return;
            }

            if (!$this->withinWindow($currentTime, $jadwal['scan_masuk_mulai'] ?? null, $jadwal['scan_masuk_sampai'] ?? null)) {
                $this->rejectScan(sprintf(
                    'Scan masuk dibuka pukul %s - %s.',
                    $jadwal['scan_masuk_mulai'] ?? '--:--',
                    $jadwal['scan_masuk_sampai'] ?? '--:--',
                ));
                return;
            }

            AbsenMurid::create([
                'murid_id' => $murid->id,
                'tanggal' => $today,
                'waktu_masuk' => $currentTime,
                'status' => $this->statusMasuk($currentTime, $jadwal['jam_masuk']),
                'keterangan' => $this->keteranganAbsensi($jadwal),
            ]);

            $this->acceptScan('Absensi masuk berhasil disimpan.');
            return;
        }

        if (!$absen->waktu_keluar && $this->bolehAbsenKeluar($currentTime, $jadwal['jam_pulang'])) {
            if (!$this->withinWindow($currentTime, $jadwal['scan_keluar_mulai'] ?? null, $jadwal['scan_keluar_sampai'] ?? null)) {
                $this->rejectScan(sprintf(
                    'Scan pulang dibuka pukul %s - %s.',
                    $jadwal['scan_keluar_mulai'] ?? '--:--',
                    $jadwal['scan_keluar_sampai'] ?? '--:--',
                ));
                return;
            }

            $absen->update([
                'waktu_keluar' => $currentTime,
            ]);

            $this->acceptScan('Absensi pulang berhasil disimpan.');
            return;
        }

        if ($absen->waktu_keluar) {
            $this->acceptScan('Murid ini sudah absen masuk dan pulang hari ini.');
            return;
        }

        $this->acceptScan('Absensi masuk sudah tercatat. Absensi pulang baru bisa dilakukan sesuai jam pulang.');
    }

    private function bisaLangsungPulang(string $currentTime, array $jadwal): bool
    {
        if (!$this->bolehAbsenKeluar($currentTime, $jadwal['jam_pulang'] ?? null)) {
            return false;
        }

        return $this->withinWindow(
            $currentTime,
            $jadwal['scan_keluar_mulai'] ?? null,
            $jadwal['scan_keluar_sampai'] ?? null,
        );
    }

    private function withinWindow(string $currentTime, ?string $start, ?string $end): bool
    {
        if (!$start || !$end) {
            return true;
        }

        $current = $this->normalizeTime($currentTime);
        $startTime = $this->normalizeTime($start);
        $endTime = $this->normalizeTime($end);

        // Normal
        if ($startTime <= $endTime) {
            return $current >= $startTime && $current <= $endTime;
        }

        // Lewat tengah malam
        return $current >= $startTime || $current <= $endTime;
    }

    private function statusMasuk(string $currentTime, ?string $jamMasuk): string
    {
        // if (!$jamMasuk) {
        //     return 'Hadir';
        // }

        // return $currentTime <= $this->normalizeTime($jamMasuk) ? 'Hadir' : 'Alpa';
        return 'Hadir';
    }

    private function bolehAbsenKeluar(string $currentTime, ?string $jamPulang): bool
    {
        if (!$jamPulang) {
            return false;
        }

        return $currentTime >= $this->normalizeTime($jamPulang);
    }

    private function formatTime($time): ?string
    {
        if (!$time) {
            return null;
        }

        return substr((string) $time, 0, 5);
    }

    private function normalizeTime(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        return strlen($time) === 5 ? $time . ':00' : $time;
    }

    private function keteranganAbsensi(array $jadwal): ?string
    {
        $parts = array_filter([
            $jadwal['label'] ?? null,
            $jadwal['nama_acara'] ?? null,
            $jadwal['keterangan'] ?? null,
        ]);

        return $parts ? implode(' - ', $parts) : null;
    }

    private function acceptScan(string $message): void
    {
        $this->scanStatus = 'success';
        $this->scanMessage = $message;
        $this->tersimpan = true;

        $this->dispatch('scanSuccess');
    }

    private function rejectScan(string $message): void
    {
        $this->scanStatus = 'error';
        $this->scanMessage = $message;
        $this->tersimpan = false;

        $this->dispatch('scanRejected');
    }

    private function resetScanState(): void
    {
        $this->tersimpan = false;
        $this->scanStatus = null;
        $this->scanMessage = null;
        $this->jadwalHariIni = [];
    }

    #[Layout("layouts.auth")]
    public function render()
    {
        Carbon::setLocale('id');
        $dateNow = Carbon::now()->translatedFormat('d F Y, l');

        return view('livewire.murid.absen.scan-qrcode', [
            'dateNow' => $dateNow,
        ]);
    }
}
