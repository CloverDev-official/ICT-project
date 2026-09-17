# Scan QR fullscreen di Chrome Android

Halaman `/scan-qrcode` menyediakan manifest aplikasi dengan `display: fullscreen`.
Chrome Android memakai mode tersebut ketika aplikasi dibuka dari ikon aplikasi
terpasang. Refresh halaman tetap berada dalam jendela aplikasi fullscreen.

## Pemasangan sekali per perangkat

1. Gunakan alamat HTTPS dengan sertifikat valid, lalu login di Chrome Android.
2. Buka halaman Scan QR.
3. Dari menu Chrome, pilih **Tambahkan ke layar utama → Instal** (atau **Instal aplikasi**, sesuai versi Chrome).
4. Buka **Scan QR** dari ikon aplikasi yang terpasang, bukan dari tab Chrome.
5. Izinkan kamera jika perangkat belum pernah memberikan izin.

Setelah pemasangan dan pemberian izin awal, tidak diperlukan sentuhan tambahan
untuk mengaktifkan fullscreen. Untuk refresh, gunakan menu titik tiga di dalam
halaman Scan QR lalu **Muat ulang**; fullscreen dikelola oleh jendela aplikasi.

Membuka URL di tab Chrome biasa tetap tunduk pada aturan interaksi pengguna untuk
Fullscreen API. Membuat pintasan biasa yang kembali membuka tab Chrome tidak sama
dengan memasang aplikasi. HTTP melalui alamat IP lokal bukan HTTPS yang valid.

Manifest memakai scope aplikasi agar alur login tetap berada di jendela aplikasi.
Pemasangan tidak memberikan akses tambahan: pengguna tetap harus login dan memiliki
izin Scan QR. Absensi tetap membutuhkan koneksi ke server.

## Verifikasi pada perangkat

- Pastikan ikon Scan QR membuka aplikasi tanpa bilah alamat Chrome.
- Dari ikon tersebut, periksa fullscreen sebelum menyentuh halaman.
- Gunakan **Muat ulang**, lalu periksa bahwa halaman kembali dalam fullscreen.
- Tutup dan buka aplikasi kembali, lalu coba pemindaian QR dan suara hasilnya.
- Uji login ulang ketika sesi berakhir dan pastikan dapat kembali ke Scan QR.

Referensi: [fullscreen aplikasi terpasang](https://web.dev/articles/fullscreen)
dan [kriteria pemasangan Chrome](https://web.dev/articles/install-criteria).
