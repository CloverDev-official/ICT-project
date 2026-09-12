# PWA

Website dapat dipasang melalui menu browser dan dibuka dalam mode standalone
tanpa URL bar. Nama aplikasi mengikuti pengaturan nama website. Ikon menggunakan
logo sekolah bawaan di `public/assets/img/logo_smkn_2.png`.

## Deployment dan pemasangan

- Jalankan website melalui HTTPS dengan sertifikat valid. HTTP localhost hanya
  untuk pengembangan; HTTP melalui IP LAN di HP tidak memenuhi syarat instalasi PWA.
- Android Chrome: buka website, lalu menu browser > Install aplikasi / Tambahkan
  ke layar utama. Nama menu dapat berbeda menurut versi browser.
- iPhone Safari: Bagikan > Tambahkan ke Layar Utama; aktifkan Buka sebagai App
  jika pilihan tersebut tersedia.
- Buka kembali dari ikon layar utama untuk menggunakan mode tanpa URL bar.
- Jam, baterai, dan navigasi sistem tetap mengikuti perangkat.

PWA ini memerlukan internet. Tidak ada service worker atau cache offline untuk
halaman login, data pribadi, dan transaksi absensi. Service worker tidak wajib
untuk instalasi pada browser modern yang mendukung manifest.

Untuk mengganti ikon aplikasi, perbarui logo sumber lalu jalankan
`php scripts/generate-pwa-icons.php` (memerlukan ekstensi GD). Commit ketiga PNG
yang dihasilkan. Perangkat mungkin perlu memasang ulang aplikasi untuk memuat ikon baru.

## Verifikasi

Periksa `/manifest.webmanifest` dan DevTools > Application > Manifest: ikon
192/512, start URL, scope, dan display standalone harus valid. Uji pemasangan
pada Android dan iPhone melalui HTTPS, kemudian login dan logout dari ikon aplikasi.
