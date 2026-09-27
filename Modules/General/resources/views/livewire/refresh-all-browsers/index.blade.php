<div class="mb-20 space-y-6">

    <!-- HERO -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main via-blue-deep to-[#07162f] p-6 shadow-lg">
        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-cyan-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">
                    <iconify-icon icon="solar:restart-bold" width="34" height="34" class="text-white"></iconify-icon>
                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">Refresh Semua Browser</h1>
                    <p class="mt-1 text-sm text-blue-100">Kirim perintah untuk memuat ulang browser pengguna yang sedang membuka aplikasi.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">Sistem</p>
                <h2 class="mt-1 text-lg font-bold text-white">Super Admin</h2>
            </div>
        </div>
    </div>

    <!-- CONTROL CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Kontrol Refresh Browser</h2>
                <p class="mt-1 text-sm text-gray-500">Gunakan saat seluruh pengguna perlu memuat ulang data atau pembaruan aplikasi.</p>
            </div>

            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">
                <iconify-icon icon="solar:shield-check-bold" width="18" height="18"></iconify-icon>
                Akses Super Admin
            </div>
        </div>

        <div class="p-6">
            <div class="flex flex-col gap-6 rounded-3xl border border-amber-200 bg-amber-50 p-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                        <iconify-icon icon="solar:danger-triangle-bold" width="24" height="24"></iconify-icon>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">Refresh pengguna aktif</h3>
                        <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-600">Browser aktif akan menerima perintah dalam beberapa detik. Perubahan formulir yang belum disimpan dapat hilang.</p>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="requestRefresh"
                    class="group inline-flex shrink-0 items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95"
                >
                    <iconify-icon icon="solar:restart-bold" width="20" height="20" class="transition group-hover:rotate-180"></iconify-icon>
                    Refresh Semua Browser
                </button>
            </div>
        </div>
    </div>

    @if ($showConfirmation)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="refresh-all-browsers-title">
            <button type="button" wire:click="cancelRefresh" class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" aria-label="Tutup konfirmasi"></button>

            <div class="relative w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">
                <div class="bg-gradient-to-r from-blue-main to-blue-deep px-6 py-5 text-white">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15">
                            <iconify-icon icon="solar:restart-bold" width="24" height="24"></iconify-icon>
                        </div>
                        <div>
                            <h2 id="refresh-all-browsers-title" class="text-lg font-bold">Refresh semua pengguna?</h2>
                            <p class="mt-0.5 text-xs text-blue-100">Tindakan ini akan segera dikirim ke browser aktif.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <p class="text-sm leading-6 text-gray-600">Kamu yakin ingin me-refresh semua pengguna? Perubahan formulir yang belum disimpan pada browser pengguna dapat hilang.</p>

                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button" wire:click="cancelRefresh" class="rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">Batal</button>
                        <button type="button" wire:click="confirmRefresh" wire:loading.attr="disabled" wire:target="confirmRefresh" class="rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-70">
                            <span wire:loading.remove wire:target="confirmRefresh">Ya, Refresh Semua</span>
                            <span wire:loading wire:target="confirmRefresh">Mengirim...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
