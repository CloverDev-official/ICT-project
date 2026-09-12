<div class="mb-20 space-y-6">

    @php
    $labelClass = 'mb-2 block text-sm font-semibold text-gray-700';
    $triggerClass = 'flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white';
    $dropdownClass = 'absolute z-50 mt-2 max-h-56 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin';
    $optionClass = 'flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white';
    $errorClass = 'mt-2 text-sm text-rose-500';
    @endphp

    <!-- BACK -->
    <div>
        <a href="{{ route('data-kelas') }}" wire:navigate>
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
                        icon="solar:pen-new-square-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Edit Kelas
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Perbarui data tingkat, jurusan, dan indeks kelas rombel.
                    </p>
                </div>

            </div>

            <!-- badge -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Rombel
                </p>

                <h2 class="mt-1 text-lg font-bold text-white">
                    {{ $rombel->nama_lengkap ?? 'Data Kelas' }}
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
                    Informasi Kelas
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Pastikan kombinasi tingkat, jurusan, dan kelas sudah sesuai.
                </p>
            </div>

            <div
                class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-600">

                <iconify-icon
                    icon="solar:pen-bold"
                    width="18"
                    height="18">
                </iconify-icon>

                Sedang diedit

            </div>

        </div>

        <!-- FORM -->
        <form
            wire:submit.prevent="update"
            class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

            <!-- TINGKAT -->
            <div>
                <livewire:components.searchable-select
                    wire:model.live="tingkat_id"
                    :options="$listTingkat"
                    value-key="id" label-key="nama"
                    label="Tingkat" placeholder="Cari tingkat..."
                    all-label="Pilih tingkat"
                    not-found-text="Pilihan tidak ditemukan." />
                @if ($hasMoreTingkat)
                    <button type="button" wire:click="loadMoreTingkat" wire:loading.attr="disabled"
                        class="mt-2 text-xs font-semibold text-blue-main hover:underline disabled:opacity-50">Muat selanjutnya</button>
                @endif
                @error('tingkat_id')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <!-- JURUSAN -->
            <div>
                <livewire:components.searchable-select
                    wire:model.live="jurusan_id"
                    :options="$listJurusan"
                    value-key="id" label-key="nama"
                    label="Jurusan" placeholder="Cari jurusan..."
                    all-label="Pilih jurusan"
                    not-found-text="Pilihan tidak ditemukan." />
                @error('jurusan_id')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <!-- KELAS -->
            <div>
                <livewire:components.searchable-select
                    wire:model.live="indeks_id"
                    :options="$listIndeks"
                    value-key="id" label-key="nama"
                    label="Indeks Kelas (Opsional)" placeholder="Cari indeks kelas (opsional)..."
                    all-label="Tanpa indeks"
                    not-found-text="Pilihan tidak ditemukan." />
                @if ($hasMoreIndeks)
                    <button type="button" wire:click="loadMoreIndeks" wire:loading.attr="disabled"
                        class="mt-2 text-xs font-semibold text-blue-main hover:underline disabled:opacity-50">Muat selanjutnya</button>
                @endif
                @error('indeks_id')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <!-- TAHUN MASUK -->
            <div>
                <livewire:components.searchable-select
                    wire:model.live="tahun_masuk"
                    :options="collect(range(now()->year - 10, now()->year + 10))->map(fn ($year) => ['id' => $year, 'nama' => (string) $year])"
                    value-key="id" label-key="nama"
                    label="Angkatan / Tahun Masuk" placeholder="Cari angkatan / tahun masuk..."
                    all-label="Pilih tahun masuk"
                    not-found-text="Pilihan tidak ditemukan." />
                @error('tahun_masuk')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <!-- WALI KELAS -->
            <div>
                <livewire:components.searchable-select
                    wire:model.live="guru_id"
                    :options="$listGuru"
                    value-key="id" label-key="nama"
                    label="Wali Kelas (Opsional)" placeholder="Cari wali kelas (opsional)..."
                    all-label="Tanpa wali kelas"
                    not-found-text="Pilihan tidak ditemukan." />
                @error('guru_id')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror
            </div>

            <!-- INFO BOX -->
            <div
                class="rounded-3xl border border-amber-100 bg-amber-50 p-5 md:col-span-2">

                <div class="flex gap-4">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white">

                        <iconify-icon
                            icon="solar:info-circle-bold"
                            width="24"
                            height="24">
                        </iconify-icon>

                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            Catatan Perubahan
                        </h3>

                        <p class="mt-1 text-sm leading-relaxed text-gray-500">
                            Perubahan pada tingkat, jurusan, atau kelas akan memengaruhi nama rombel yang digunakan pada data murid dan laporan absensi.
                        </p>
                    </div>

                </div>

            </div>

            <!-- ACTION -->
            <div
                class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end md:col-span-2">

                <a href="{{ route('data-kelas') }}" wire:navigate>
                    <button
                        type="button"
                        class="w-full rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-auto">

                        Batal

                    </button>
                </a>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="update"
                    class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                    <iconify-icon
                        wire:loading.remove
                        wire:target="update"
                        icon="lineicons:save"
                        width="20"
                        height="20"
                        class="transition group-hover:scale-110">
                    </iconify-icon>

                    <iconify-icon
                        wire:loading
                        wire:target="update"
                        icon="line-md:loading-twotone-loop"
                        width="20"
                        height="20">
                    </iconify-icon>

                    <span wire:loading.remove wire:target="update">
                        Simpan Perubahan
                    </span>

                    <span wire:loading wire:target="update">
                        Menyimpan...
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>
