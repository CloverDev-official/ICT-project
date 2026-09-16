<template data-scan-modal-template="success">
    <div class="fixed inset-0 z-50" id="success-modal">
        <div data-close-scan class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
        <div class="relative flex min-h-screen items-center justify-center p-4 pointer-events-none">
            <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl pointer-events-auto">
                <div class="flex items-center justify-between bg-linear-to-r from-blue-deep to-blue-deep-solid px-6 py-3 text-white">
                    <div class="flex items-center justify-start gap-2">
                        <img src="{{ asset('assets/img/logo_smkn_2.png') }}" class="h-10 w-10" alt="">
                        <div><h2 class="font-bold leading-tight">Absensi Murid</h2><p class="text-xs opacity-80">SMKN 2 Banjarmasin</p></div>
                    </div>
                    <div class="text-right text-xs opacity-80"><p>ID: <span data-scan-field="uuid">-</span></p></div>
                </div>
                <div class="px-6 pt-5">
                    <div data-scan-success-notice class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        <p class="font-semibold" data-scan-field="message">Absensi berhasil disimpan.</p>
                        <p data-scan-late-message class="mt-3 hidden text-center text-2xl leading-tight font-extrabold text-amber-700 sm:text-3xl">
                            Anda terlambat, silahkan lapor ke pengawas
                        </p>
                        <p class="mt-1 hidden text-xs" data-scan-schedule>Jadwal: <span data-scan-field="jadwal-label">-</span><span data-scan-schedule-time></span></p>
                    </div>
                </div>
                <div class="flex gap-6 p-6">
                    <div class="relative flex h-40 w-32 shrink-0 items-center justify-center overflow-hidden rounded-xl border bg-blue-100 text-4xl font-bold text-blue-main shadow" role="img" data-scan-avatar>
                        <span data-scan-avatar-initial></span><img class="absolute inset-0 hidden h-full w-full object-cover" alt="" data-scan-avatar-image>
                    </div>
                    <div class="grid flex-1 grid-cols-2 gap-x-6 gap-y-2 text-sm">
                        <div><p class="text-xs text-gray-500">Nama</p><p class="font-semibold text-gray-900" data-scan-field="nama">-</p></div>
                        <div><p class="text-xs text-gray-500">Kelas</p><p class="font-semibold text-gray-900" data-scan-field="rombel">-</p></div>
                        <div><p class="text-xs text-gray-500">NISN</p><p class="font-semibold" data-scan-field="nisn">-</p></div>
                        <div><p class="text-xs text-gray-500">NIPD</p><p class="font-semibold" data-scan-field="nipd">-</p></div>
                        <div><p class="text-xs text-gray-500">Tempat, Tanggal Lahir</p><p class="font-semibold" data-scan-field="lahir">-</p></div>
                        <div><p class="text-xs text-gray-500">Jenis Kelamin</p><p class="font-semibold" data-scan-field="jenisKelamin">-</p></div>
                        <div><p class="text-xs text-gray-500">Agama</p><p class="font-semibold" data-scan-field="agama">-</p></div>
                        <div><p class="text-xs text-gray-500">No HP</p><p class="font-semibold" data-scan-field="hp">-</p></div>
                        <div class="col-span-2"><p class="text-xs text-gray-500">Alamat</p><p class="font-semibold leading-snug" data-scan-field="alamat">-</p></div>
                    </div>
                </div>
                <div class="flex justify-between bg-gray-100 px-6 py-2 text-xs text-gray-600"><p>Dicetak oleh sistem</p><p data-scan-date></p></div>
            </div>
        </div>
    </div>
</template>
