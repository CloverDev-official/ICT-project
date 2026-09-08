# Audio hasil scan absensi

Laravel 12 / Livewire 4 / ZXing WASM tetap menggunakan jadwal dan modal yang
ada. Backend menerbitkan satu event `scanResult` setelah transaksi selesai,
berisi `{id, status, autoClose}`. `scanResult` adalah properti Livewire terkunci;
status audio tidak ditentukan dari teks atau warna modal.

| Status | Audio di resources/audio | Kondisi |
| --- | --- | --- |
| success | scan-success.mp3 | Masuk/pulang atau pembaruan izin berhasil disimpan |
| late | scan-late.mp3 | Konfirmasi alasan terlambat berhasil disimpan |
| failed | scan-failed.mp3 | QR invalid/tidak ditemukan, penolakan, save dibatalkan/exception, HTTP/jaringan gagal |
| already_recorded | already-recorded.mp3 | Absensi masuk/pulang hari ini sudah tercatat |
| attendance_not_open | attendance-not-open.mp3 | Scan masuk sebelum jadwal dibuka; tidak disimpan |

`late_pending` mempertahankan formulir alasan terlambat tanpa audio karena data
belum disimpan. Batas keterlambatan mengikuti `scan_masuk_sampai` dari jadwal
kelas/default. Audio pulang tetap `success` meskipun status masuk sebelumnya
terlambat; scan pulang tidak dinilai sebagai kedatangan terlambat baru. QR biasa
sebelum jendela pulang, dengan masuk sudah tercatat, tetap `already_recorded`
sesuai alur yang ada (QR tidak menyatakan jenis masuk/pulang).

## Lifecycle modal

`scan-result.js` menyediakan `openScanResultModal()` dan
`closeScanResultModal()`. Satu controller menyimpan modal aktif, ID hasil,
timer otomatis 2 detik, dan lock pemrosesan. Observer menghubungkan hasil
terstruktur dalam DOM Blade dengan controller setelah modal benar-benar
terpasang. Event/DOM morph dengan ID sama tidak memutar ulang suara.

Penutupan melalui tombol/backdrop/Escape/otomatis langsung menghentikan audio
sebelum menunggu request Livewire. Modal disembunyikan dan kamera tetap terkunci
sampai close selesai. ID close lama tidak menutup hasil backend yang lebih baru.
Pergantian modal menghentikan suara lama dahulu. Penghapusan modal, penggantian
node DOM, navigasi SPA dan cleanup komponen juga menghentikan audio. Listener,
observer, timer, interceptor request dan AudioContext dibersihkan saat destroy.

Web Audio memakai satu AudioContext dengan buffer per status. `stopCurrentScanAudio()`
menghentikan dan melepas source serta membatalkan playback yang masih menunggu
preload. Source baru dimulai pada offset 0, setara pause/reset `currentTime=0`
pada HTML Audio. Tidak ada object URL atau instance Audio per scan.

QR sama yang masih terlihat tetap diblokir setelah modal tutup. QR berbeda bisa
langsung dipindai; QR lama bisa dipindai kembali setelah hilang selama 1 detik.
Selama request scan atau modal terbuka, kamera tidak memproses QR berikutnya.

## Cache-first

`scan-audio.js`: `scanAudioMap`, `getCachedAudio(status)`,
`preloadScanAudios()`, `playScanAudio(status)`, `stopCurrentScanAudio()`.
Interaksi pointer/keyboard pertama mengaktifkan AudioContext dan memulai preload
lima audio secara paralel tanpa menunggu kamera. Setiap preload memiliki promise
tunggal per status. Cache `attendance-scan-audio-v2` diperiksa sebelum fetch.
Cache hit tidak menjalankan request, download, atau revalidation jaringan.

Cache miss: fetch, validasi status HTTP, MIME `audio/mpeg`/`audio/ogg`, ukuran
1 byte–5 MB, dekode browser, lalu cache.put. File gagal tidak disimpan. Cache
rusak tidak diunduh ulang secara diam-diam. Kegagalan audio tidak menghentikan
absensi. Cache Storage membutuhkan HTTPS/localhost; jika tidak tersedia audio
nonaktif dengan aman. Naikkan versi cache ketika mengganti file; Vite juga
menggunakan URL file dengan hash. Lima MP3 dibangun sebagai file terpisah.
`prebuild`/`predev` memeriksa seluruh file, batas ukuran, dan header MPEG Layer III.
Tidak ada upload audio admin baru.

## Pengujian

- `php artisan test`: status hasil dengan database testing, konfirmasi terlambat,
  data duplikat, exception/cancelled save, close berulang/stale.
- `npm run test:scanner`: cache, validasi respons/ukuran/dekode, playback,
  pembatalan saat preload, lock QR, lifecycle modal.
- `npm run build`: validasi kelima MP3 dan build Vite.
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
