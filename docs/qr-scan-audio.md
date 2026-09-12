# Audio hasil scan absensi

Laravel 12 / Livewire 4 / ZXing WASM tetap menggunakan jadwal dan modal yang
ada. Backend menerbitkan satu event `scanResult` setelah transaksi selesai,
berisi `{id, status, autoClose}`. `scanResult` adalah properti Livewire terkunci;
status audio tidak ditentukan dari teks atau warna modal.

| Status | Audio di resources/audio | Kondisi |
| --- | --- | --- |
| success | scan-success.mp3 | Masuk/pulang atau pembaruan izin berhasil disimpan |
| late | scan-late.mp3 | Absensi berstatus terlambat berhasil disimpan |
| failed | scan-failed.mp3 | QR invalid/tidak ditemukan, penolakan, save dibatalkan/exception, HTTP/jaringan gagal |
| already_recorded | already-recorded.mp3 | Absensi masuk/pulang hari ini sudah tercatat |
| attendance_not_open | attendance-not-open.mp3 | Scan masuk sebelum jadwal dibuka; tidak disimpan |

Scan terlambat langsung disimpan dengan status `Terlambat`, lalu menggunakan
modal berhasil yang sama dengan scan normal. Batas keterlambatan mengikuti
`scan_masuk_sampai` dari jadwal kelas/default. Audio mengikuti hasil backend:
`late` untuk absensi berstatus terlambat, termasuk saat pulang; `success` untuk
absensi normal. Scan ulang sebelum jendela pulang tetap `already_recorded`.

Alasan tidak diminta saat scan. Tombol cetak surat membuka modal alasan wajib di halaman daftar izin telat
(maximal 191 karakter sesuai kolom `absen_murid.keterangan`). Simpan & Cetak
memvalidasi dan menyimpan alasan, lalu membuka halaman surat yang membaca ulang
record dan membuka dialog cetak. Cetak ulang memuat alasan tersimpan untuk diedit.
Halaman surat menolak record yang bukan berstatus `Terlambat` dan tidak merender
area cetak sebelum alasan berhasil disimpan.

## Lifecycle modal

`scan-result.js` menyediakan `openScanResultModal()` dan
`closeScanResultModal()`. Satu controller menyimpan modal aktif, ID hasil,
timer otomatis 2 detik, dan lock pemrosesan. Observer menghubungkan hasil
terstruktur dalam DOM Blade dengan controller setelah modal benar-benar
terpasang. Event/DOM morph dengan ID sama tidak memutar ulang suara.

Penutupan melalui tombol/backdrop/Escape/otomatis langsung menghentikan audio
sebelum menunggu request Livewire. Modal disembunyikan dan kamera langsung dapat
menangkap QR berikutnya tanpa membuat ulang video/canvas. Request scan berikutnya
menunggu close selesai agar perubahan state server tetap berurutan.
ID close lama tidak menutup hasil backend yang lebih baru.
Pergantian modal menghentikan suara lama dahulu. Penghapusan modal, penggantian
node DOM, navigasi SPA dan cleanup komponen juga menghentikan audio. Listener,
observer, timer, interceptor request dan AudioContext dibersihkan saat destroy.

Web Audio memakai satu AudioContext dengan buffer per status. `stopCurrentScanAudio()`
menghentikan dan melepas source serta membatalkan playback yang masih menunggu
preload. Source baru dimulai pada offset 0, setara pause/reset `currentTime=0`
pada HTML Audio. Tidak ada object URL atau instance Audio per scan.

Pengunci QR direset setiap modal ditutup. QR yang sama dan tetap diam bisa
langsung dipindai kembali, sehingga modal dapat berulang selama QR masih terlihat.
Selama request scan atau modal terbuka, kamera tidak memproses QR berikutnya.

## Cache-first

`scan-audio.js`: `scanAudioMap`, `getCachedAudio(status)`,
`preloadScanAudios()`, `playScanAudio(status)`, `stopCurrentScanAudio()`.
Halaman langsung mencoba mengaktifkan AudioContext dan memulai preload
lima audio secara paralel tanpa menunggu kamera. Interaksi pointer/keyboard
mencoba aktivasi kembali jika browser membatasi autoplay. Setiap preload memiliki promise
tunggal per status. Cache `attendance-scan-audio-v2` diperiksa sebelum fetch.
Cache hit tidak menjalankan request, download, atau revalidation jaringan.

Cache miss: fetch, validasi status HTTP, MIME `audio/mpeg`/`audio/ogg`, ukuran
1 byte–5 MB, dekode browser, lalu cache.put. File gagal tidak disimpan. Cache
rusak tidak diunduh ulang secara diam-diam. Kegagalan audio tidak menghentikan
absensi. Cache Storage membutuhkan HTTPS/localhost; jika tidak tersedia (HTTP/IP)
atau akses cache gagal, audio di-fetch langsung dan buffer disimpan di memori
selama controller halaman aktif. Preload tetap satu kali per status sehingga
scan berulang tidak mengunduh ulang. Reload HTTP/IP mengunduh audio lagi.
Kegagalan penulisan cache tidak menghalangi playback. Pada browser biasa yang
memblokir autoplay, klik/tap atau interaksi keyboard tetap diperlukan.
Naikkan versi cache ketika mengganti file; Vite juga
menggunakan URL file dengan hash. Lima MP3 dibangun sebagai file terpisah.
`prebuild`/`predev` memeriksa seluruh file, batas ukuran, dan header MPEG Layer III.
Tidak ada upload audio admin baru.

## Perangkat kiosk tanpa sentuhan

Preload otomatis tidak melewati kebijakan autoplay browser. Untuk Chrome desktop
di perangkat scanner, jalankan browser dengan parameter berikut (ganti URL dengan
alamat scanner sebenarnya):

```text
chrome --kiosk --autoplay-policy=no-user-gesture-required "https://alamat-web/halaman-scanner"
```

Nama/path executable menyesuaikan sistem operasi. Tutup seluruh proses Chrome
sebelum mencoba agar parameter diterapkan pada proses baru. Simpan parameter yang
sama pada startup perangkat agar tetap berlaku setelah restart. Mode `--kiosk`
sendiri tidak memberikan izin autoplay. Alternatif untuk browser terkelola adalah
kebijakan Chrome `AutoplayAllowlist` untuk alamat web scanner.

Rujukan: https://developer.chrome.com/blog/autoplay/ dan
https://chromeenterprise.google/policies/autoplay-allowlist/.

Uji tanpa menyentuh halaman: buka scanner melalui konfigurasi tersebut, scan QR,
dan pastikan speaker berbunyi. Ulangi setelah reload dan restart perangkat.
Pengaturan ini mengatasi izin autoplay; suara tetap bergantung pada volume,
speaker, dan keberhasilan pemuatan audio. Konfigurasi perangkat harus diterapkan
pada perangkat scanner, bukan hanya server Laravel.

## Pengujian

- `php artisan test`: status hasil dengan database testing, scan terlambat langsung, validasi/simpan/edit alasan surat,
  data duplikat, exception/cancelled save, close berulang/stale.
- `npm run test:scanner`: cache, validasi respons/ukuran/dekode, playback,
  pembatalan saat preload, lock QR, lifecycle modal.
- `npm run build`: validasi kelima MP3, build Vite, dan pemeriksaan export scanner
  pada bundle produksi. `preserveEntrySignatures: 'strict'` mempertahankan API
  yang diimpor langsung dari Blade, di luar module graph Vite.
- `QR_PLAYWRIGHT_MODULE=/path/to/playwright/index.mjs node tests/js/scan-audio.browser.mjs`
  (memerlukan Playwright + Chromium). Chromium DevTools Protocol Network sudah
  diuji: cache kosong **5 request**, reload dengan cache terisi **0 tambahan**,
  cache dihapus **5 tambahan**, versi cache diganti **5 tambahan**. HTTP 503
  dan file >5 MB juga ditolak tanpa masuk cache. Kelima MP3 berhasil didekode. Tombol/OK/backdrop,
  Escape, otomatis, pergantian modal, duplikasi hasil dan disposal lulus.
  Script Blade asli juga diuji dengan respons Livewire/kamera simulasi untuk
  penutupan modal, HTTP failure fallback dan cleanup SPA.

Library Chromium sementara berada di /tmp pada lingkungan implementasi;
`PLAYWRIGHT_BROWSERS_PATH` dan `LD_LIBRARY_PATH` mungkin diperlukan bila memakai
instalasi sementara tersebut. Tidak ada dependency sistem project yang diubah.

Untuk pemeriksaan manual: buka scanner, DevTools Network (filter `.mp3`, Preserve
log), hapus cache di Application, lakukan interaksi pertama lalu reload dan
interaksi lagi. Bandingkan jumlah request seperti di atas. Uji kamera fisik dan
speaker di perangkat scanner; pengujian otomatis memakai simulasi QR/kamera dan
belum membuktikan keluaran speaker perangkat pengguna.
