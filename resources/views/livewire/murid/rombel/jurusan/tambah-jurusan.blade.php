<div class="mb-20 space-y-6">

    <!-- BACK -->
    <div>
        <a href="{{ route('data-jurusan') }}" wire:navigate>
            <button
                class="group flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-main hover:text-blue-main hover:shadow-md">

                <iconify-icon
                    icon="lineicons:chevron-left"
                    width="20"
                    height="20"
                    class="transition group-hover:-translate-x-1">
                </iconify-icon>

                Kembali

            </button>
        </a>
    </div>

    <!-- HERO -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main via-blue-deep to-[#07162f] p-6 shadow-lg">

        <!-- ornament -->
        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-cyan-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-5">

                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:library-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Tambah Jurusan
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Tambahkan data jurusan baru untuk digunakan pada data kelas dan murid.
                    </p>
                </div>

            </div>

            <!-- badge -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Status
                </p>

                <h2 class="mt-1 text-lg font-bold text-white">
                    Form Input Data
                </h2>

            </div>

        </div>
    </div>

    <!-- FORM CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- CARD HEADER -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Informasi Jurusan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Isi nama jurusan dengan benar agar mudah digunakan pada data kelas.
                </p>
            </div>

            <div
                class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                <iconify-icon
                    icon="solar:shield-check-bold"
                    width="18"
                    height="18">
                </iconify-icon>

                Data Aman

            </div>

        </div>

        <!-- FORM -->
        <form
            wire:submit.prevent="store"
            class="p-6">

            <!-- input section -->
            <div class="grid grid-cols-1 gap-6">

                <!-- nama jurusan -->
                <div>

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Nama Jurusan
                    </label>

                    <div class="relative">

                        <input
                            type="text"
                            wire:model.defer="nama"
                            placeholder="Contoh : Animasi"
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm capitalize transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100" />

                        <div
                            class="absolute inset-y-0 right-4 flex items-center text-gray-400">

                            <iconify-icon
                                icon="solar:book-bold"
                                width="22"
                                height="22">
                            </iconify-icon>

                        </div>

                    </div>

                    @error('nama')
                        <p class="mt-2 text-sm text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <!-- info -->
                <div
                    class="rounded-3xl border border-blue-100 bg-blue-50 p-5">

                    <div class="flex gap-4">

                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-main text-white">

                            <iconify-icon
                                icon="solar:info-circle-bold"
                                width="24"
                                height="24">
                            </iconify-icon>

                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-800">
                                Catatan
                            </h3>

                            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                Nama jurusan akan tampil pada data kelas, filter murid, dan laporan absensi. Gunakan penamaan yang singkat dan jelas.
                            </p>
                        </div>

                    </div>

                </div>

            </div>

            <!-- ACTION -->
            <div
                class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">

                <a href="{{ route('data-jurusan') }}" wire:navigate>
                    <button
                        type="button"
                        class="w-full rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-auto">

                        Batal

                    </button>
                </a>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="store"
                    class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                    <iconify-icon
                        wire:loading.remove
                        wire:target="store"
                        icon="lineicons:save"
                        width="20"
                        height="20"
                        class="transition group-hover:scale-110">
                    </iconify-icon>

                    <iconify-icon
                        wire:loading
                        wire:target="store"
                        icon="line-md:loading-twotone-loop"
                        width="20"
                        height="20">
                    </iconify-icon>

                    <span wire:loading.remove wire:target="store">
                        Simpan Jurusan
                    </span>

                    <span wire:loading wire:target="store">
                        Menyimpan...
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>