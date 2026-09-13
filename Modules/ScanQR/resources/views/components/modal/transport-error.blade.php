<template data-scan-modal-template="transport">
    <div class="fixed inset-0 z-50">
        <div data-close-scan class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
        <div class="relative flex min-h-screen items-center justify-center p-4 pointer-events-none">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl pointer-events-auto">
                <div class="bg-red-600 px-6 py-4 text-white"><h2 class="text-lg font-bold">Absensi Tidak Disimpan</h2></div>
                <div class="p-6 text-sm text-gray-700">Server tidak dapat memproses hasil scan. Silakan coba kembali.</div>
            </div>
        </div>
    </div>
</template>
