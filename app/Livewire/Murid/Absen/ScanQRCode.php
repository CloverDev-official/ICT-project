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
            $this->rejectScan(sprintf(
                'Absensi QR tidak dibuka karena jadwal kelas %s hari ini adalah %s%s.',
                $murid->rombel->nama_lengkap ?? '-',
                $jadwal['label'],
                $jadwal['keterangan'] ? ' - ' . $jadwal['keterangan'] : ''
            ));
            return;
        }

        $absen = AbsenMurid::query()
            ->where('murid_id', $murid->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absen) {
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

    private function statusMasuk(string $currentTime, ?string $jamMasuk): string
    {
        if (!$jamMasuk) {
            return 'Hadir';
        }

        return $currentTime <= $this->normalizeTime($jamMasuk) ? 'Hadir' : 'Alpa';
    }

    private function bolehAbsenKeluar(string $currentTime, ?string $jamPulang): bool
    {
        if (!$jamPulang) {
            return false;
        }

        return $currentTime >= $this->normalizeTime($jamPulang);
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
