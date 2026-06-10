<div class="space-y-6">

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-sm">

        <!-- ornament -->
        <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-white/5"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15 backdrop-blur text-white">

                    <iconify-icon
                        icon="solar:calendar-bold"
                        width="34"
                        height="34">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white capitalize">
                        Kenaikan Tahun Ajaran
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Kelola perpindahan kelas siswa ke tahun ajaran baru.
                    </p>
                </div>

            </div>

            <!-- badge -->
            <div
                class="w-full lg:w-auto rounded-2xl border border-white/20 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-widest text-blue-100">
                    Tahun Ajaran Aktif
                </p>

                <h2 class="mt-1 text-xl font-bold text-white">
                    {{ $currentAcademicYearLabel }}
                </h2>

            </div>

        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        <!-- TOP -->
        <div class="border-b border-slate-200 p-6">

            <div class="grid grid-cols-1">

                <!-- tahun aktif -->
                <div>

                    <label class="text-sm font-medium text-slate-500">
                        Tahun Ajaran Saat Ini
                    </label>

                    <div
                        class="mt-3 flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-5">

                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">

                            <iconify-icon
                                icon="solar:calendar-mark-bold"
                                width="28"
                                height="28">
                            </iconify-icon>

                        </div>

                        <div>
                            <h2 class="text-2xl font-bold text-slate-800">
                                {{ $currentAcademicYearLabel }}
                            </h2>

                            <p class="text-sm text-slate-500">
                                Tahun ajaran yang sedang berjalan
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- PREVIEW -->
        <div class="p-6">

            <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">

                <div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Preview Kenaikan
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Ringkasan perpindahan siswa untuk tahun ajaran {{ $nextAcademicYearLabel }}.
                    </p>
                </div>

                <div
                    class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-main">

                    <iconify-icon
                        icon="solar:info-circle-bold"
                        width="18"
                        height="18">
                    </iconify-icon>

                    Data hanya preview

                </div>

            </div>

            <!-- cards -->
            <div class="grid gap-5 lg:grid-cols-4">

                <!-- X -->
                <div
                    class="group relative overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 transition hover:-translate-y-1 hover:shadow-lg">

                    <div
                        class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-emerald-100 transition group-hover:scale-110">
                    </div>

                    <div class="relative">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm font-medium text-emerald-700">
                                    Kelas X
                                </p>

                                <h2 class="mt-3 text-4xl font-bold text-emerald-900">
                                    {{ $levelStats['X'] ?? 0 }}
                                </h2>
                            </div>

                            <div
                                class="rounded-2xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm">

                                → XI

                            </div>

                        </div>

                        <p class="mt-6 text-sm leading-relaxed text-emerald-700">
                            Murid kelas X akan dipindahkan ke kelas XI pada tahun ajaran berikutnya.
                        </p>

                    </div>

                </div>

                <!-- XI -->
                <div
                    class="group relative overflow-hidden rounded-3xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-6 transition hover:-translate-y-1 hover:shadow-lg">

                    <div
                        class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-amber-100 transition group-hover:scale-110">
                    </div>

                    <div class="relative">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm font-medium text-amber-700">
                                    Kelas XI
                                </p>

                                <h2 class="mt-3 text-4xl font-bold text-amber-900">
                                    {{ $levelStats['XI'] ?? 0 }}
                                </h2>
                            </div>

                            <div
                                class="rounded-2xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm">

                                → XII

                            </div>

                        </div>

                        <p class="mt-6 text-sm leading-relaxed text-amber-700">
                            Murid kelas XI akan dipindahkan ke kelas XII pada tahun ajaran berikutnya.
                        </p>

                    </div>

                </div>

                <!-- XII -->
                <div
                    class="group relative overflow-hidden rounded-3xl border border-rose-200 bg-gradient-to-br from-rose-50 to-white p-6 transition hover:-translate-y-1 hover:shadow-lg">

                    <div
                        class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-rose-100 transition group-hover:scale-110">
                    </div>

                    <div class="relative">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm font-medium text-rose-700">
                                    Kelas XII
                                </p>

                                <h2 class="mt-3 text-4xl font-bold text-rose-900">
                                    {{ $levelStats['XII'] ?? 0 }}
                                </h2>
                            </div>

                            <div
                                class="rounded-2xl bg-rose-500 px-4 py-2 text-sm font-semibold text-white shadow-sm">

                                Lulus

                            </div>

                        </div>

                        <p class="mt-6 text-sm leading-relaxed text-rose-700">
                            Murid kelas XII tidak naik lagi dan akan ditandai selesai saat proses dijalankan.
                        </p>

                    </div>

                </div>

                <!-- Lulus -->
                <div
                    class="group relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-6 transition hover:-translate-y-1 hover:shadow-lg">

                    <div
                        class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-slate-100 transition group-hover:scale-110">
                    </div>

                    <div class="relative">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm font-medium text-slate-700">
                                    Lulus
                                </p>

                                <h2 class="mt-3 text-4xl font-bold text-slate-900">
                                    {{ $levelStats['Lulus'] ?? 0 }}
                                </h2>
                            </div>

                            <div
                                class="rounded-2xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white shadow-sm">

                                Arsip

                            </div>

                        </div>

                        <p class="mt-6 text-sm leading-relaxed text-slate-600">
                            Murid yang sudah lulus disimpan sebagai arsip dan tidak tampil lagi di data aktif.
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!-- FOOTER -->
        <div
            class="flex flex-col gap-5 border-t border-slate-200 bg-slate-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <!-- info -->
            <div class="flex items-center gap-3">

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-200 text-slate-600">

                    <iconify-icon
                        icon="solar:history-bold"
                        width="24"
                        height="24">
                    </iconify-icon>

                </div>

                <div>
                    <p class="text-sm text-slate-500">
                        Terakhir diproses
                    </p>

                    <h2 class="font-semibold text-slate-700">
                        {{ $lastProcessedAt ? \Illuminate\Support\Carbon::parse($lastProcessedAt)->translatedFormat('d F Y H:i') : 'Belum pernah diproses' }}
                    </h2>
                </div>

            </div>

            <!-- button -->
            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">

                <button
                    class="rounded-2xl border border-slate-300 bg-white px-6 py-3 font-medium text-slate-700 transition hover:bg-slate-100 active:scale-95">

                    Batal

                </button>

                <livewire:components.modal.manajemen.tahun-ajaran.modal-verifikasi-tahun-ajaran />

            </div>

        </div>

    </div>

</div>
