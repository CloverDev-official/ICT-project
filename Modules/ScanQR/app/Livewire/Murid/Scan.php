<?php

namespace Modules\ScanQR\Livewire\Murid;

use App\Models\Murid\AbsenMurid;
use App\Models\Murid\IzinMurid;
use App\Models\Murid\Murid;
use Modules\ScanQR\Services\AutoAlpaMuridService;
use Modules\ScanQR\Services\JadwalAbsensiService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class Scan extends Component
{
    public ?Murid $murid = null;
    public bool $tersimpan = false;
    public ?string $scanTitle = null;
    public ?string $scanStatus = null;
    public ?string $scanMessage = null;
    public ?string $keterangan = '';
    public ?string $izinUuid = null;
    public bool $isContainsIzin = false;
    public array $jadwalHariIni = [];

    #[On('verifiedQRCode')]
    public function verifiedQRCode($muridUuid): void
    {
        // Bersihkan hasil scan sebelumnya agar respons QR baru tidak memakai state lama.
        $this->resetScanState();

        $muridUuid = $this->parseMuridUuid($muridUuid);
        $murid = $this->findMurid($muridUuid);

        // QR hanya dapat diproses untuk murid aktif yang ditemukan di database.
        if (!$murid) {
            $this->rejectScan('QR Code tidak valid atau murid tidak ditemukan.');
            return;
        }

        // QR izin memiliki alur sendiri dan tidak boleh diteruskan ke absensi biasa.
        if ($this->processIzin($murid)) {
            return;
        }

        // Absensi membutuhkan rombel untuk menentukan jadwal yang berlaku.
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

        // Hari libur dan PJJ tetap ditolak oleh aturan jadwal yang sama.
        if (!$jadwalService->bolehScan($jadwal)) {
            $this->rejectScan('Absensi Tidak Disimpan. Sistem mengecek jadwal kelas dari Manajemen Waktu.');
            return;
        }

        // app(AutoAlpaMuridService::class)->syncForRombel($murid->rombel_id, $today, $jadwal, $now);

        $this->processAbsensi($murid, $today, $currentTime, $jadwal);
    }

    public function konfirmasiTerlambat()
    {
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        $absen = AbsenMurid::updateOrCreate(
        [
            'murid_id' => $this->murid->id,
            'tanggal' => $today,
        ],
        [
            'waktu_masuk' => $currentTime,
            'status' => 'Terlambat',
            'keterangan' => $this->keterangan,
        ]
    );

        $this->acceptScan('Alasan terlambat tersimpan.');
        $this->dispatch('lateConfirm');
        return;
    }

    private function parseMuridUuid($muridUuid)
    {
        // Scanner lama dapat mengirim payload serialisasi PHP.
        if (str_starts_with($muridUuid, 's:')) {
            $decoded = @unserialize($muridUuid);
            if ($decoded !== false) {
                $muridUuid = $decoded;
            }
        }

        // QR izin memuat UUID murid dan UUID izin yang dipisahkan tanda ">".
        if (str_contains($muridUuid, '>')) {
            $this->isContainsIzin = true;
            [$muridUuid, $izinUuid] = explode('>', $muridUuid, 2);
            $this->izinUuid = $izinUuid;
        }

        return $muridUuid;
    }

    private function findMurid($muridUuid): ?Murid
    {
        return Murid::query()
            ->aktif()
            ->with('rombel')
            ->where('uuid', $muridUuid)
            ->first();
    }

    private function processIzin(Murid $murid): bool
    {
        $izin = IzinMurid::query()
            ->where('murid_id', $murid->id)
            ->firstOr(function () {
                return null;
            });

        // Jika QR izin cocok dengan izin yang sedang berlangsung, maka izin akan diperbarui menjadi selesai.
        if ($izin && $this->isContainsIzin && $izin?->uuid === $this->izinUuid && $izin?->status === 'Izin') {
            // QR izin yang cocok menandai izin selesai tanpa membuat absensi baru.
            $izin->update([
                'status' => 'Selesai',
            ]);

            $this->acceptMessage('Izin Berhasil Diperbarui', 'Izin berhasil diproses.');
            return true;
        }

        if ($izin && $izin?->status === 'Izin') {
            // Murid yang masih izin harus menyelesaikan QR izin terlebih dahulu.
            $this->rejectScan('Murid sedang dalam izin, absensi tidak dapat disimpan. Silakan scan QR izin terlebih dahulu.');
            return true;
        }

        if ($izin && $izin?->status === 'Selesai') {
            // Izin selesai tidak dapat digunakan kembali untuk proses absensi.
            $this->rejectScan('Murid sudah selesai izin, absensi tidak dapat disimpan. Silakan scan QR absensi.');
            return true;
        }

        return false;
    }

    private function processAbsensi(Murid $murid, string $today, string $currentTime, array $jadwal): void
    {
        $absen = AbsenMurid::query()
            ->where('murid_id', $murid->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Cek apakah murid tidak absen masuk dan sudah melewati jendela scan masuk, tetapi belum waktunya pulang.
        if (!$absen?->waktu_masuk && $absen?->status != 'Terlambat' && !$this->dalamJendelaPulang($currentTime, $jadwal)) {
            $beradaSetelahScanMasuk = $currentTime > $this->normalizeTime($jadwal['scan_masuk_sampai'] ?? null);
            $belumScanKeluar = !$this->isWindowStarted($currentTime, $jadwal['scan_keluar_mulai'] ?? null);
            if ($beradaSetelahScanMasuk && $belumScanKeluar) {
                $this->lateScan('Murid sudah terlambat absen masuk.');
                return;
            }
        }

        if (!$absen && !$absen?->waktu_keluar) {
            // Record pertama hari ini dapat berupa masuk atau langsung pulang.
            $this->processAbsensiPertama($murid, $today, $currentTime, $jadwal);
            return;
        }

        $this->processAbsensiLanjutan($absen, $currentTime, $jadwal);
    }

    private function processAbsensiPertama(Murid $murid, string $today, string $currentTime, array $jadwal): void
    {
        // Scan masuk hanya boleh disimpan di dalam window scan masuk.
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
            'status' => 'Masuk',
            'keterangan' => $this->keteranganAbsensi($jadwal),
        ]);

        $this->acceptScan('Absensi masuk berhasil disimpan.');
    }

    private function processAbsensiLanjutan(AbsenMurid $absen, string $currentTime, array $jadwal): void
    {
        // Murid tidak dapat absen pulang jika belum memiliki absensi masuk saat jendela pulang terbuka.
        if (!$absen->waktu_masuk && $this->dalamJendelaPulang($currentTime, $jadwal)) {
            $this->rejectScan('Maaf kamu tidak bisa absen pulang karena belum absen masuk');
            return;
        }

        // Murid dapat absen pulang jika belum memiliki absensi pulang dan berada di dalam jendela pulang.
        if (!$absen->waktu_keluar && $this->dalamJendelaPulang($currentTime, $jadwal)) {
            // Status pulang mengikuti status masuk, jika masuk terlambat maka pulang juga terlambat.
            $status = $absen?->status === 'Terlambat' ? 'Terlambat' : 'Hadir';
            $absen->update([
                'waktu_keluar' => $currentTime,
                'status' => $status,
            ]);

            $this->acceptScan('Absensi pulang berhasil disimpan.');
            return;
        }

        // Waktu keluar yang sudah tersimpan mencegah absensi pulang kedua.
        if ($absen->waktu_keluar) {
            $this->acceptMessage('Murid sudah pulang', 'Murid ini sudah absen masuk dan pulang hari ini.');
            return;
        }

        $this->acceptMessage('Absensi masuk sudah tercatat', sprintf(
            'Absensi pulang dibuka pukul %s - %s.',
            $jadwal['scan_keluar_mulai'] ?? '--:--',
            $jadwal['scan_keluar_sampai'] ?? '--:--',
        ));
    }

    private function bisaLangsungPulang(string $currentTime, array $jadwal): bool
    {
        return $this->dalamJendelaPulang($currentTime, $jadwal);
    }

    private function dalamJendelaPulang(string $currentTime, array $jadwal): bool
    {
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

        dd($current, $startTime, $endTime, ($current >= $startTime && $current <= $endTime));

        // Normal
        if ($startTime <= $endTime) {
            return $current >= $startTime && $current <= $endTime;
        }

        // Lewat tengah malam
        return $current >= $startTime || $current <= $endTime;
    }

    private function isWindowStarted(string $currentTime, ?string $start): bool
    {
        return $start && $this->normalizeTime($currentTime) >= $this->normalizeTime($start);
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

    private function acceptMessage(string $title, string $message): void
    {
        $this->scanTitle = $title;
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
        $this->keterangan = '';
        $this->izinUuid = null;
        $this->isContainsIzin = false;
        $this->jadwalHariIni = [];
    }

    #[Layout("layouts.auth")]
    public function render()
    {
        Carbon::setLocale('id');
        $dateNow = Carbon::now()->translatedFormat('d F Y, l');

        return view('scanqr::livewire.murid.scan', [
            'dateNow' => $dateNow,
        ]);
    }
}
