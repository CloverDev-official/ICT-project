<?php

namespace App\Livewire\Murid\Absen;

use App\Models\Murid\AbsenMurid;
use App\Models\Murid\IzinMurid;
use App\Models\Murid\Murid;
use App\Services\Absensi\AutoAlpaMuridService;
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
    public ?string $keterangan = '';
    public ?string $izinUuid = null;
    public bool $isContainsIzin = false;
    public array $jadwalHariIni = [];

    #[On('verifiedQRCode')]
    public function verifiedQRCode($muridUuid): void
    {
        $this->resetScanState();

        if (str_starts_with($muridUuid, 's:')) {
            $decoded = @unserialize($muridUuid);
            if ($decoded !== false) {
                $muridUuid = $decoded;
            }
        }

        if (str_contains($muridUuid, '>')) {
            $this->isContainsIzin = true;
            [$muridUuid, $izinUuid] = explode('>', $muridUuid, 2);
            $this->izinUuid = $izinUuid;
        }

        $murid = Murid::query()
            ->aktif()
            ->with('rombel')
            ->where('uuid', $muridUuid)
            ->first();

        if (!$murid) {
            $this->rejectScan('QR Code tidak valid atau murid tidak ditemukan.');
            return;
        }

        $izin = IzinMurid::query()
            ->where('murid_id', $murid->id)
            ->firstOr(function () {
                return null;
            });

        if($izin && $this->isContainsIzin && $izin?->uuid === $this->izinUuid && $izin?->status === 'Izin') {
            $izin->update([
                'status' => 'Selesai',
            ]);

            $this->acceptMessage('Izin berhasil diproses.');
            return;
        }

        if ($izin && $izin?->status === 'Izin') {
            $this->rejectScan('Murid sedang dalam izin, absensi tidak dapat disimpan. Silakan scan QR izin terlebih dahulu.');
            return;
        }

        if($izin && $izin?->status === 'Selesai') {
            $this->rejectScan('Murid sudah selesai izin, absensi tidak dapat disimpan. Silakan scan QR absensi.');
            return;
        }
        
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

        // app(AutoAlpaMuridService::class)->syncForRombel($murid->rombel_id, $today, $jadwal, $now);

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

            // AbsenMurid::create([
            //     'murid_id' => $murid->id,
            //     'tanggal' => $today,
            //     'waktu_masuk' => $currentTime,
            //     'status' => $this->statusMasuk($currentTime, $jadwal['jam_masuk']),
            //     'keterangan' => $this->keteranganAbsensi($jadwal),
            // ]);

            AbsenMurid::create([
                'murid_id' => $murid->id,
                'tanggal' => $today,
                'waktu_masuk' => $currentTime,
                'status' => 'Masuk',
                'keterangan' => $this->keteranganAbsensi($jadwal),
            ]);

            $this->acceptScan('Absensi masuk berhasil disimpan.');
            return;
        }

        if (!$absen->waktu_masuk && !$this->bolehAbsenKeluar($currentTime, $jadwal['jam_pulang'])) {
            if (!$this->withinWindow($currentTime, $jadwal['scan_masuk_mulai'] ?? null, $jadwal['scan_masuk_sampai'] ?? null)) {
                $this->lateScan('Murid sudah terlambat absen masuk.');
                return;
            }
        }

        if (!$absen->waktu_masuk && $this->bolehAbsenKeluar($currentTime, $jadwal['jam_pulang'])) {
            $this->rejectScan('Maaf kamu tidak bisa absen pulang karena belum absen masuk');
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
                'status' => 'Hadir',
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

    public function konfirmasiTerlambat(){
            $now = now();
            $today = $now->toDateString();
            $currentTime = $now->format('H:i:s');

            $absen = AbsenMurid::query()
                ->where('murid_id', $this->murid->id)
                ->whereDate('tanggal', $today)
                ->first();

            $absen->update([
                'waktu_masuk' => $currentTime,
                'status' => 'Terlambat',
                'keterangan' => $this->keterangan,
            ]);

            $this->acceptScan('Alasan terlambat tersimpan.');
            $this->dispatch('lateConfirm');
            return;
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

    private function acceptMessage(string $message): void
    {
        $this->scanStatus = 'message';
        $this->scanMessage = $message;
        $this->tersimpan = false;

        $this->dispatch('scanMessage');
    }

    private function lateScan(string $message): void
    {
        $this->scanStatus = 'terlambat';
        $this->scanMessage = $message;
        $this->tersimpan = false;

        $this->dispatch('lateMessage');
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
