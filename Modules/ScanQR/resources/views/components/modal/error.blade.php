<template data-scan-modal-template="error">
    <div class="fixed inset-0 z-50" id="error-modal">
        <div data-close-scan class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
        <div class="relative flex min-h-screen items-center justify-center p-4 pointer-events-none">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl pointer-events-auto">
                <div class="bg-red-600 px-6 py-4 text-white"><h2 class="text-lg font-bold">Absensi Tidak Disimpan</h2><p class="text-xs opacity-80">Sistem mengecek jadwal kelas dari Manajemen Waktu.</p></div>
                <div class="p-6 text-sm text-gray-700">
                    <p class="text-center text-2xl leading-tight font-extrabold text-gray-900 sm:text-3xl" data-scan-field="message"></p>
                    <div class="mt-4 hidden rounded-2xl bg-gray-50 p-4" data-scan-murid><p><span class="text-gray-500">Nama:</span> <span class="font-semibold" data-scan-field="nama">-</span></p><p><span class="text-gray-500">Kelas:</span> <span class="font-semibold" data-scan-field="rombel">-</span></p></div>
                    <div class="mt-4 hidden rounded-2xl border border-red-100 bg-red-50 p-4 text-red-800" data-scan-jadwal><p class="font-semibold" data-scan-field="jadwal-label">-</p><p class="hidden text-xs" data-scan-field="jadwal-nama-acara"></p><p class="hidden text-xs" data-scan-field="jadwal-keterangan"></p></div>
                </div>
            </div>
        </div>
    </div>
</template>
