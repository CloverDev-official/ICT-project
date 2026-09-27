# Aplikasi Android Absensi

Pendekatan utama sekarang adalah APK native Android dengan WebView, di
`android/absensi`. Fullscreen diatur oleh jendela Android saat aplikasi dibuka,
kembali aktif, dan memuat halaman. Refresh tidak membutuhkan sentuhan untuk
mengaktifkan fullscreen kembali. Ini tidak bergantung pada Fullscreen API Chrome
atau pemasangan PWA.

Nama aplikasi **Absensi** dan ikonnya menggunakan file asli
`public/assets/img/logo_smkn_2.png`, yang disalin otomatis ke resource Android
saat build. URL scanner mengikuti route proyek: `/mpanel/scan-qrcode`.

## Build APK release universal

Persyaratan: JDK 17, Android SDK Platform 35, Build Tools 35.0.0, dan akses internet
untuk mengunduh dependensi Gradle. Minimum perangkat adalah Android 8.0 (API 26).

1. Buka folder `android/absensi` dari checkout proyek ini di Android Studio.
2. Atur Gradle JDK ke JDK 17, pasang SDK yang diminta, lalu lakukan Gradle Sync.
3. Gunakan konfigurasi release dan kunci lokal seperti langkah terminal berikut.

Alternatif terminal dari folder `android/absensi`, dengan SDK sudah dikonfigurasi
melalui `ANDROID_HOME` atau `local.properties`:

```bash
# Hanya sekali jika belum memiliki .signing/; jangan mengganti kunci yang sudah ada.
java scripts/CreateReleaseSigning.java
./gradlew assembleRelease lintRelease
```

APK release bertanda tangan berada di
`android/absensi/app/build/outputs/apk/release/app-release.apk`. Build release
menonaktifkan debugging. Aplikasi hanya memuat Java/DEX dan resource, tanpa library
JNI `.so` atau pembagian APK berdasarkan ABI. Satu APK yang sama dapat dipasang
pada Android 32-bit dan 64-bit (Android 8.0 ke atas); WebView disediakan sistem
perangkat sesuai arsitekturnya. Kamera tetap memerlukan Android System WebView
yang mendukung fitur web scanner.

Kunci release tersimpan di `android/absensi/.signing/absensi-release.p12` dan
konfigurasinya di `.signing/release.properties`. Keduanya dibuat secara lokal,
tidak masuk Git, dan tidak dicetak ke output build. **Cadangkan kedua file itu
secara aman**: semua update release harus memakai kunci yang sama. Jangan
menjalankan pembuat kunci lagi untuk update; langsung jalankan Gradle.

APK debug sebelumnya memakai kunci berbeda. Untuk pindah pertama kali ke release,
hapus aplikasi debug dari HP lalu pasang release. Alamat server, opsi sertifikat
lokal, sesi login, dan izin kamera perlu diatur ulang; data absensi pada server
tidak terhapus. Update antar-release berikutnya bisa dipasang menimpa aplikasi
selama kunci yang sama digunakan dan versionCode dinaikkan.

## Pengaturan awal sekali per perangkat

1. Pasang APK dan izinkan pemasangan dari sumber tersebut jika Android meminta.
2. Buka **Absensi** dan masukkan alamat HTTPS website sekolah, misalnya
   `https://absensi.sekolah.sch.id`. Alamat contoh ini harus diganti dengan server
   yang sebenarnya. Alamat lengkap `/mpanel/scan-qrcode` juga diterima.
   Untuk HTTPS lokal dengan sertifikat self-signed/tidak valid, centang
   **Sertifikat lokal (server ini saja)** sebelum menyimpan.
3. Login menggunakan akun proyek yang memiliki akses menu Scan QR. Sesi WebView
   terpisah dari Chrome; pilih **Ingat saya** jika sesi perlu bertahan.
4. Jika role akun mengarahkan login ke dashboard/menu lain, tekan menu aplikasi
   tiga titik di kiri bawah lalu **Scan QR**.
5. Izinkan kamera saat Android meminta. Izin mikrofon tidak diperlukan.

Setelah pengaturan awal, pembukaan aplikasi langsung menuju scanner dalam
fullscreen. Sesi login yang berakhir tetap membutuhkan login ulang. Izin kamera
yang dicabut atau izin sekali pakai tetap mengikuti aturan Android.

## Kontrol aplikasi

Menu tiga titik berada di **kiri bawah** dengan lingkaran kecil dan transparansi
65% saat tidak digunakan. Saat ditekan atau mendapat fokus, tombol terlihat jelas.
Area sentuh tetap 48dp. Tombol Back Android juga membuka menu ini, yang menyediakan:

- **Scan QR**: kembali ke scanner.
- **Muat ulang**: refresh halaman tanpa keluar fullscreen.
- **Kembali ke halaman sebelumnya**: membuka riwayat halaman WebView.
- **Alamat server**: mengganti alamat server; perubahan alamat membersihkan
  cookie aplikasi dan membutuhkan login ulang.
- **Keluar aplikasi**: menutup aplikasi.

Layar tetap menyala selama aplikasi aktif. Audio diatur agar tidak membutuhkan
gesture melalui WebSettings; perangkat tetap harus memiliki volume media yang
sesuai. Gunakan Android System WebView terbaru untuk kamera dan decoder QR/WASM.

Website tetap harus tersedia lewat HTTPS. Validasi sertifikat aktif secara default.
Untuk server lokal, menu **⋮ → Alamat server → Sertifikat lokal (server ini saja)**
mengizinkan sertifikat HTTPS yang tidak valid pada host dan port server tersebut.
Opsi disimpan sehingga tidak perlu diaktifkan lagi setelah refresh atau restart.
Alamat lain, termasuk CDN, tetap memerlukan sertifikat valid. Mengedit alamat
menghapus centang; aktifkan kembali hanya jika server pengganti juga server lokal
tepercaya. Menonaktifkan opsi dan menyimpan akan menghapus keputusan SSL yang
di-cache WebView sehingga validasi sertifikat kembali berlaku.

Mode ini tidak memerlukan perbaikan sertifikat lokal, tetapi tidak memverifikasi
identitas server dan rentan terhadap penyamaran server di jaringan. Server harus
tetap menyediakan HTTPS; HTTP biasa dan kegagalan handshake TLS bukan masalah
validasi sertifikat dan tidak diatasi oleh opsi ini.

Aplikasi tidak membuka URL file lokal atau memberikan izin kamera ke origin lain.
CDN yang dipakai proyek tetap membutuhkan koneksi internet.
Login, otorisasi, dan penyimpanan absensi tetap dikelola Laravel yang sudah ada.

Ini mode immersive, bukan penguncian perangkat: Android masih dapat menampilkan
bilah navigasi sementara saat pengguna menggeser dari tepi layar. Fullscreen tab
Chrome biasa tidak berubah. Manifest PWA lama tetap tersedia untuk pengguna web,
tetapi tidak dipakai oleh APK.

## Verifikasi pada HP

- Buka aplikasi dan periksa fullscreen sebelum menyentuh halaman.
- Pastikan tombol kecil kiri bawah bisa disentuh, meredup saat tidak digunakan,
  dan tidak tertutup setelah rotasi layar atau keyboard muncul.
- Login, beri izin kamera, dan pindai QR yang valid; periksa hasil absensi dan audio.
- Muat ulang melalui menu aplikasi dan melalui menu halaman; fullscreen tetap aktif.
- Tutup/buka aplikasi, pindah aplikasi lalu kembali, dan putar layar.
- Uji penolakan izin kamera, lalu izinkan dari pengaturan Android dan muat ulang.
- Uji sesi kedaluwarsa, jaringan terputus, alamat salah, serta pemulihan lewat menu.
- Untuk HTTPS lokal, uji opsi sertifikat lokal aktif/nonaktif, refresh, restart,
  serta pergantian alamat agar pengecualian tidak terbawa ke server lain.
- Pasang APK release yang sama pada perangkat 32-bit dan 64-bit untuk uji perangkat.

Tes validasi URL dan origin tanpa SDK (jalankan dari root repository):

```bash
mkdir -p /tmp/absensi-url-tests
javac -d /tmp/absensi-url-tests android/absensi/app/src/main/java/id/sch/smkn2/absensi/ServerAddress.java android/absensi/tests/ServerAddressTest.java
java -cp /tmp/absensi-url-tests id.sch.smkn2.absensi.ServerAddressTest
```

Referensi implementasi: [immersive mode Android](https://developer.android.com/develop/ui/views/layout/immersive),
[izin WebView](https://developer.android.com/reference/android/webkit/PermissionRequest),
[pengaturan media WebView](https://developer.android.com/reference/android/webkit/WebSettings#setMediaPlaybackRequiresUserGesture(boolean)),
[penandatanganan APK](https://developer.android.com/studio/publish/app-signing),
dan [kompatibilitas 64-bit](https://developer.android.com/google/play/requirements/64-bit).
