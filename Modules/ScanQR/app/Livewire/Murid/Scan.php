<?php

namespace Modules\ScanQR\Livewire\Murid;

use App\Enums\AttendanceStatus;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\IzinMurid;
use App\Models\Murid\Murid;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Modules\ScanQR\Services\AutoAlpaMuridService;
use Modules\ScanQR\Services\JadwalAbsensiService;
use Throwable;

class Scan extends Component
{
    #[Locked]
    public ?array $scanResult = null;

    protected ?Murid $murid = null;

    public bool $tersimpan = false;

    protected ?string $scanTitle = null;

    public ?string $scanStatus = null;

    protected ?string $scanMessage = null;

    protected ?string $izinUuid = null;

    protected bool $isContainsIzin = false;

    protected array $jadwalHariIni = [];

    #[On('verifiedQRCode')]
    #[Renderless]
    public function verifiedQRCode($muridUuid): void
    {
        $this->executeScan(fn () => $this->processQRCode($muridUuid));
    }

    private function executeScan(callable $process): void
    {
        try {
            DB::transaction($process);
        } catch (Throwable $error) {
            report($error);
            $this->resetScanState();
            $this->rejectScan('Proses absensi gagal. Silakan coba kembali.');
        }

        // Publish only the final outcome, after the transaction commits.
        $this->dispatch('scanResult', result: $this->scanResult);
    }

    private function processQRCode($muridUuid): void
    {
        // Bersihkan hasil scan sebelumnya agar respons QR baru tidak memakai state lama.
        $this->resetScanState();

        if (! is_string($muridUuid) || $muridUuid === '') {
            $this->rejectScan('QR Code tidak valid.');

            return;
        }
        $muridUuid = $this->parseMuridUuid($muridUuid);
        $murid = $this->findMurid($muridUuid);

        if(!$this->checkMuridState($murid)) return;
        
        $jadwalService = app(JadwalAbsensiService::class);
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');
        $jadwal = $jadwalService->forRombel($murid->rombel_id, $today);
        
        $this->murid = $murid;
        $this->jadwalHariIni = $jadwal;

        if (!$this->processIzin($murid, $currentTime, $jadwal)) {
            return;
        }

        // Hari libur dan PJJ tetap ditolak oleh aturan jadwal yang sama.
        if (! $jadwalService->bolehScan($jadwal)) {
            $this->rejectScan('Absensi Tidak Disimpan. Sistem mengecek jadwal kelas dari Manajemen Waktu.');
            return;
        }

        //app(AutoAlpaMuridService::class)->syncForRombel($murid->rombel_id, $today, $jadwal, $now);

        $this->processAbsensi($murid, $today, $currentTime, $jadwal);
    }
    private function checkMuridState($murid): bool{
        // QR hanya dapat diproses untuk murid aktif yang ditemukan di database.
        if (!$murid) {
            $this->rejectScan('QR Code tidak valid atau murid tidak ditemukan.');
            return false;
        }

        // Murid yang tidak aktif tidak dapat melakukan absensi.
        if($murid->status !== 'aktif') {
            $this->rejectScan('Murid tidak aktif, absensi tidak dapat disimpan.');
            return false;
        }

        // Absensi membutuhkan rombel untuk menentukan jadwal yang berlaku.
        if (! $murid->rombel_id) {
            $this->murid = $murid;
            $this->rejectScan('Murid belum memiliki kelas/rombel, absensi tidak dapat disimpan.');

            return false;
        }

        return true;
    }
    private function saveLateAttendance(Murid $murid, string $today, string $currentTime): void
    {
        $saved = AbsenMurid::updateOrCreate(
            ['murid_id' => $murid->id, 'tanggal' => $today],
            [
                'waktu_masuk' => $currentTime,
                'status' => AttendanceStatus::Terlambat->value,
            ],
        );

        if (! $saved->exists || (! $saved->wasRecentlyCreated && ! $saved->wasChanged())) {
            $this->rejectScan('Absensi gagal disimpan.');

            return;
        }

        $this->acceptScan('Absensi masuk berhasil disimpan.', 'late');
    }

    #[Renderless]
    public function closeModal(?string $resultId = null): void
    {
        if ($resultId !== null && $this->scanResult && $this->scanResult['id'] !== $resultId) {
            return;
        }

        $this->resetScanState();

        $this->dispatch('scanModalClosed');
    }

    private function parseMuridUuid($muridUuid)
    {
        // Scanner lama dapat mengirim payload serialisasi PHP.
        if (str_starts_with($muridUuid, 's:')) {
            $decoded = @unserialize($muridUuid, ['allowed_classes' => false]);
            if (is_string($decoded)) {
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

    private function processIzin(Murid $murid, string $currentTime, array $jadwal): bool
    {
        $izin = IzinMurid::query()
            ->where('murid_id', $murid->id)
            ->latest('created_at')
            ->first();

        if ($izin && $izin?->status === AttendanceStatus::Izin->value && (!$this->isContainsIzin || $izin?->uuid !== $this->izinUuid)) {
            // Murid yang masih izin harus menyelesaikan QR izin terlebih dahulu.
            $this->rejectScan("Murid masih memiliki izin yang belum selesai. Silakan scan QR izin untuk menyelesaikan proses izin.");
            return false;
        }

        // Jika QR izin cocok dengan izin yang sedang berlangsung, maka izin akan diperbarui menjadi selesai.
        if ($izin && $this->isContainsIzin && $izin?->uuid === $this->izinUuid && $izin?->status === AttendanceStatus::Izin->value) {
            // QR hanya menutup proses izin. Status absensi telah diubah saat
            // pengawas membuat izin, bukan ketika QR dipindai.
            if (!$izin->update([
                'status' => AttendanceStatus::Selesai->value,
            ])) {
                $this->rejectScan('Izin gagal disimpan.');

                return false;
            }

            $message = 'Izin telah diproses.';
            if ($this->withinWindow(
                $currentTime,
                $jadwal['scan_masuk_mulai'] ?? null,
                $jadwal['scan_masuk_sampai'] ?? null,
            ) || $this->dalamJendelaPulang($currentTime, $jadwal)) {
                $message .= ' Silakan gunakan QR code absensi untuk absensi.';
            }

            $this->acceptMessage('Izin Berhasil Diperbarui', $message, 'permission_success');
            return false;
        }

        if ($izin && $this->isContainsIzin && $izin?->status === AttendanceStatus::Selesai->value) {
            // Izin selesai tidak dapat digunakan kembali untuk proses absensi.
            $this->rejectScan('Murid sudah selesai izin. Silakan scan QR absensi.');
            return false;
        }

        return true;
    }

    private function processAbsensi(Murid $murid, string $today, string $currentTime, array $jadwal): void
    {
        $absen = AbsenMurid::query()
            ->where('murid_id', $murid->id)
            // tanggal bertipe DATE; perbandingan langsung dapat memakai indeks murid_id/tanggal.
            ->where('tanggal', $today)
            ->first();

        // Cek apakah murid tidak absen masuk dan sudah melewati jendela scan masuk, tetapi belum waktunya pulang.
        if (! $absen?->waktu_masuk && $absen?->status != AttendanceStatus::Terlambat->value && ! $this->dalamJendelaPulang($currentTime, $jadwal)) {
            $beradaSetelahScanMasuk = $currentTime > $this->normalizeTime($jadwal['scan_masuk_sampai'] ?? null);
            $belumScanKeluar = ! $this->isWindowStarted($currentTime, $jadwal['scan_keluar_mulai'] ?? null);
            if ($beradaSetelahScanMasuk && $belumScanKeluar) {
                $this->saveLateAttendance($murid, $today, $currentTime);

                return;
            }
        }

        if (! $absen && ! $absen?->waktu_keluar) {
            // Record pertama hari ini dapat berupa masuk atau langsung pulang.
            $this->processAbsensiPertama($murid, $today, $currentTime, $jadwal);

            return;
        }

        $this->processAbsensiLanjutan($absen, $currentTime, $jadwal);
    }

    private function processAbsensiPertama(Murid $murid, string $today, string $currentTime, array $jadwal): void
    {
        if (! $this->withinWindow($currentTime, $jadwal['scan_masuk_mulai'] ?? null, $jadwal['scan_masuk_sampai'] ?? null)
            && ! $this->isWindowStarted($currentTime, $jadwal['scan_masuk_mulai'] ?? null)) {
            $this->rejectScan('Jadwal absensi masuk belum dibuka.', 'attendance_not_open');

            return;
        }

        // Scan masuk hanya boleh disimpan di dalam window scan masuk.
        if (! $this->withinWindow($currentTime, $jadwal['scan_masuk_mulai'] ?? null, $jadwal['scan_masuk_sampai'] ?? null)) {
            $this->rejectScan(sprintf(
                'Scan masuk dibuka pukul %s - %s.',
                $jadwal['scan_masuk_mulai'] ?? '--:--',
                $jadwal['scan_masuk_sampai'] ?? '--:--',
            ));

            return;
        }

        $saved = AbsenMurid::create([
            'murid_id' => $murid->id,
            'tanggal' => $today,
            'waktu_masuk' => $currentTime,
            'status' => AttendanceStatus::Masuk->value,
            'keterangan' => $this->keteranganAbsensi($jadwal),
        ]);

        if (! $saved->exists) {
            $this->rejectScan('Absensi gagal disimpan.');

            return;
        }

        $this->acceptScan('Absensi masuk berhasil disimpan.');
    }

    private function processAbsensiLanjutan(?AbsenMurid $absen, string $currentTime, array $jadwal): void
    {
        // Murid tidak dapat absen pulang jika belum memiliki absensi masuk saat jendela pulang terbuka.
        if (! $absen?->waktu_masuk && $this->dalamJendelaPulang($currentTime, $jadwal)) {
            $this->rejectScan('Maaf kamu tidak bisa absen pulang karena belum absen masuk');

            return;
        }

        // Murid dapat absen pulang jika belum memiliki absensi pulang dan berada di dalam jendela pulang.
        if (! $absen?->waktu_keluar && $this->dalamJendelaPulang($currentTime, $jadwal)) {
            // Status pulang mengikuti status masuk, jika masuk terlambat maka pulang juga terlambat.
            $status = $absen?->status === AttendanceStatus::Terlambat->value
                ? AttendanceStatus::Terlambat->value
                : AttendanceStatus::Hadir->value;
            if (! $absen->update([
                'waktu_keluar' => $currentTime,
                'status' => $status,
            ])) {
                $this->rejectScan('Absensi gagal disimpan.');

                return;
            }

            // Status absensi tetap terlambat bila masuk terlambat, tetapi modal
            // pulang adalah notifikasi normal—bukan peringatan keterlambatan lagi.
            $this->acceptScan('Absensi pulang berhasil disimpan.');

            return;
        }

        // Waktu keluar yang sudah tersimpan mencegah absensi pulang kedua.
        if ($absen?->waktu_keluar) {
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
        if (! $start || ! $end) {
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

    private function isWindowStarted(string $currentTime, ?string $start): bool
    {
        return $start && $this->normalizeTime($currentTime) >= $this->normalizeTime($start);
    }

    private function normalizeTime(?string $time): ?string
    {
        if (! $time) {
            return null;
        }

        return strlen($time) === 5 ? $time.':00' : $time;
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

    private function acceptScan(string $message, string $status = 'success'): void
    {
        $this->scanStatus = 'success';
        $this->scanMessage = $message;
        $this->tersimpan = true;

        $this->setScanResult($status);
    }

    private function acceptMessage(string $title, string $message, string $status = 'already_recorded'): void
    {
        $this->scanTitle = $title;
        $this->scanStatus = 'message';
        $this->scanMessage = $message;
        $this->tersimpan = false;

        $this->setScanResult($status);
    }

    private function rejectScan(string $message, string $status = 'failed'): void
    {
        $this->scanStatus = 'error';
        $this->scanMessage = $message;
        $this->tersimpan = false;

        $this->setScanResult($status);
    }

    private function setScanResult(string $status, bool $autoClose = true): void
    {
        $this->scanResult = [
            'id' => (string) Str::uuid(),
            'status' => $status,
            'autoClose' => $autoClose,
            'modal' => $this->modalData(),
        ];
    }

    private function modalData(): array
    {
        $type = $this->tersimpan ? 'success' : $this->scanStatus;
        $modal = [
            'type' => $type,
            'message' => $this->scanMessage,
        ];

        if ($type === 'message') {
            $modal['title'] = $this->scanTitle;
        }

        if ($type === 'success' && $this->murid) {
            $modal['murid'] = [
                'uuid' => $this->murid->uuid,
                'nama' => $this->murid->nama,
                'rombel' => $this->murid->rombel?->nama_lengkap,
                'nisn' => $this->murid->nisn,
                'nipd' => $this->murid->nipd,
                'tempatLahir' => $this->murid->tempat_lahir,
                'tanggalLahir' => $this->murid->tanggal_lahir?->translatedFormat('d M Y'),
                'jenisKelamin' => $this->murid->jk === 'L' ? 'Laki-laki' : 'Perempuan',
                'agama' => $this->murid->agama,
                'hp' => $this->murid->hp,
                'alamat' => $this->murid->alamat,
                'imagePath' => $this->murid->image_path,
            ];
            $modal['jadwal'] = array_filter([
                'label' => $this->jadwalHariIni['label'] ?? null,
                'jam_masuk' => $this->jadwalHariIni['jam_masuk'] ?? null,
                'jam_pulang' => $this->jadwalHariIni['jam_pulang'] ?? null,
            ]);
        }

        if ($type === 'error') {
            if ($this->murid) {
                $modal['murid'] = [
                    'nama' => $this->murid->nama,
                    'rombel' => $this->murid->rombel?->nama_lengkap,
                ];
            }

            $modal['jadwal'] = array_filter([
                'label' => $this->jadwalHariIni['label'] ?? null,
                'nama_acara' => $this->jadwalHariIni['nama_acara'] ?? null,
                'keterangan' => $this->jadwalHariIni['keterangan'] ?? null,
            ]);
        }

        return $modal;
    }

    private function resetScanState(): void
    {
        $this->scanResult = null;
        $this->tersimpan = false;
        $this->scanStatus = null;
        $this->scanMessage = null;
        $this->scanTitle = null;
        $this->murid = null;
        $this->izinUuid = null;
        $this->isContainsIzin = false;
        $this->jadwalHariIni = [];
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        Carbon::setLocale('id');
        $dateNow = Carbon::now()->translatedFormat('d F Y, l');

        return view('scanqr::livewire.murid.scan', [
            'dateNow' => $dateNow,
        ]);
    }
}
