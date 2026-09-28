<div class="space-y-6">
    <!-- Hero -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-lg">

        <!-- glow -->
        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-white/5"></div>

        <div
            class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <!-- left -->
            <div class="flex items-center gap-5">

                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:clipboard-check-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-white">
                        Laporan Izin Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Daftar dan laporan izin telat murid secara realtime.
                    </p>

                </div>
            </div>
        </div>
    </div>

        <!-- FILTER -->
    <div
        class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">

        <!-- top -->
        <div
            class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <h2 class="text-lg font-bold text-gray-800">
                    Filter Data
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Filter berdasarkan tingkat, jurusan, dan kelas.
                </p>

            </div>

            <!-- search -->
            <div class="relative w-full lg:w-80">

                <input
                    type="search"
                    name="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari nama murid..."
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 pl-4 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

                <div
                    class="absolute inset-y-0 right-4 flex items-center text-gray-400">

                    <iconify-icon
                        icon="mdi:account-search-outline"
                        width="22"
                        height="22">
                    </iconify-icon>

                </div>

            </div>

        </div>

        <!-- dropdown -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-4">

            <!-- tingkat -->
            <livewire:components.searchable-select
                wire:model.live="filterTingkat"
                label="Tingkat"
                placeholder="Cari tingkat..."
                :options="$listRombel->pluck('tingkat')->filter()->sortBy('nama')->unique('id')->values()"
                value-key="id"
                label-key="nama"
                all-label="Semua Tingkat"
                not-found-text="Tingkat tidak ditemukan."
                input-size="md"
                width="full"
                icon="mdi:magnify"
                icon-size="22"
                icon-position="right"
                icon-offset="4"
                icon-class="text-gray-500"
                dropdown-height="md"
                input-class="bg-white"
                dropdown-class="shadow-2xl"
                option-class="font-medium"
                check-icon="mdi:check"
                check-icon-size="20"
                check-icon-class="text-black-500" />

            <!-- jurusan -->
            <livewire:components.searchable-select
                wire:model.live="filterJurusan"
                label="Jurusan"
                placeholder="Cari jurusan..."
                :options="$filteredJurusan"
                value-key="id"
                label-key="nama"
                all-label="Semua Jurusan"
                not-found-text="Jurusan tidak ditemukan."
                input-size="md"
                width="full"
                icon="mdi:magnify"
                icon-size="22"
                icon-position="right"
                icon-offset="4"
                icon-class="text-gray-500"
                dropdown-height="md"
                input-class="bg-white"
                dropdown-class="shadow-2xl"
                option-class="font-medium"
                check-icon="mdi:check"
                check-icon-size="20"
                check-icon-class="text-black-500" />

            <!-- kelas / indexs -->
            <livewire:components.searchable-select
                wire:model.live="filterIndeks"
                label="Kelas"
                placeholder="Cari kelas..."
                :options="$filteredIndeks"
                value-key="id"
                label-key="nama"
                all-label="Semua Kelas"
                not-found-text="Kelas tidak ditemukan."
                input-size="md"
                width="full"
                icon="mdi:magnify"
                icon-size="22"
                icon-position="right"
                icon-offset="4"
                icon-class="text-gray-500"
                dropdown-height="md"
                input-class="bg-white"
                dropdown-class="shadow-2xl"
                option-class="font-medium"
                check-icon="mdi:check"
                check-icon-size="20"
                check-icon-class="text-black-500" />

            <!-- tanggal absen -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tanggal Absen
                </label>

                <input
                    type="date"
                    wire:model.live="filterTanggal"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div
        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- top -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- left -->
            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Daftar Izin telat
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Data seluruh murid yang izin telat.
                </p>

            </div>

        </div>

        {{-- DESKTOP: TABLE --}}
        <div class="hidden overflow-x-auto lg:block">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="w-10 px-5 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Nama
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Kelas
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            NIPD
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Alasan
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Tanggal
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Waktu
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Status
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($listMuridTerlambat as $terlambat)

                        <tr class="border-t border-gray-100 transition hover:bg-gray-50">

                            {{-- Nomor --}}
                            <td class="px-5 py-5 text-center font-medium text-gray-700">
                                {{ $listMuridTerlambat->firstItem() + $loop->index }}
                            </td>

                            {{-- Nama --}}
                            <td class="px-5 py-5">
                                <h2 class="text-center font-semibold capitalize text-gray-800">
                                    {{ $terlambat->murid->nama ?? '-' }}
                                </h2>
                            </td>

                            {{-- Kelas --}}
                            <td class="px-5 py-5 text-center text-gray-700">
                                {{ $terlambat->murid->rombel->nama_lengkap ?? 'N/A' }}
                            </td>

                            {{-- NIPD --}}
                            <td class="px-5 py-5 text-center text-gray-700">
                                {{ $terlambat->murid->nipd ?? '-' }}
                            </td>

                            {{-- Alasan --}}
                            <td class="px-5 py-5 text-center text-gray-700">
                                {{ $terlambat->keterangan }}
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-5 py-5 text-center text-gray-700">
                                {{ optional($terlambat->tanggal)->format('d - m - Y') ?? '-' }}
                            </td>

                            {{-- Waktu --}}
                            <td class="px-5 py-5 text-center text-gray-700">
                                {{ $terlambat->waktu_masuk ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-5 text-center text-gray-700">
                                {{ $terlambat->status ?? '-' }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-5 text-center text-gray-700">
                                <div class="flex items-center justify-center gap-2">

                                    <button
                                        type="button"
                                        wire:click="bukaAlasan({{ $terlambat->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="bukaAlasan"
                                        aria-haspopup="dialog"
                                        aria-controls="modal-alasan-telat"
                                        aria-label="Cetak Surat Keterlambatan"
                                        title="Cetak Surat Keterlambatan"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-main text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-deep-solid"
                                    >
                                        <iconify-icon
                                            icon="solar:printer-bold"
                                            width="20"
                                            height="20"
                                        ></iconify-icon>
                                    </button>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="px-6 py-14 text-center">

                                <div class="flex flex-col items-center justify-center">

                                    <div
                                        class="mb-4 flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-100 text-gray-400"
                                    >
                                        <iconify-icon
                                            icon="solar:user-cross-bold"
                                            width="38"
                                            height="38"
                                        ></iconify-icon>
                                    </div>

                                    <h2 class="text-lg font-bold text-gray-700">
                                        Data izin kosong
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Belum ada data izin yang tersedia.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- MOBILE + TABLET: COLLAPSED CARD --}}
        <div class="space-y-3 lg:hidden">

            @forelse ($listMuridTerlambat as $terlambat)

                <div
                    x-data="{ open: false }"
                    class="overflow-hidden border border-t-0 border-gray-200 bg-white"
                >

                    {{-- SUMMARY --}}
                    <div class="flex items-center gap-3 p-4">

                        {{-- Nomor --}}
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-sm font-semibold text-gray-600"
                        >
                            {{ $listMuridTerlambat->firstItem() + $loop->index }}
                        </div>

                        {{-- Informasi utama --}}
                        <div class="min-w-0 flex-1">

                            <h3 class="truncate font-semibold capitalize text-gray-800">
                                {{ $terlambat->murid->nama ?? '-' }}
                            </h3>

                            <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500">

                                <span>
                                    {{ $terlambat->murid->rombel->nama_lengkap ?? 'N/A' }}
                                </span>

                                <span class="text-gray-300">•</span>

                                <span>
                                    {{ $terlambat->waktu_masuk ?? '-' }}
                                </span>

                            </div>

                        </div>

                        {{-- Status --}}
                        <div class="hidden shrink-0 sm:block">
                            <span
                                class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600"
                            >
                                {{ $terlambat->status ?? '-' }}
                            </span>
                        </div>

                        {{-- Chevron --}}
                        <button
                            type="button"
                            @click="open = !open"
                            :aria-expanded="open"
                            aria-label="Lihat detail keterlambatan"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-gray-500 transition hover:bg-gray-100 hover:text-gray-700"
                        >
                            <iconify-icon
                                icon="lineicons:chevron-down"
                                width="20"
                                height="20"
                                class="transition-transform duration-200"
                                :class="{ 'rotate-180': open }"
                            ></iconify-icon>
                        </button>

                    </div>


                    {{-- DETAIL --}}
                    <div
                        x-show="open"
                        x-collapse
                        class="border-t border-gray-200 bg-gray-50"
                    >

                        <div class="space-y-4 p-4">

                            {{-- Nama --}}
                            <div>
                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    Nama
                                </p>

                                <p class="font-semibold capitalize text-gray-800">
                                    {{ $terlambat->murid->nama ?? '-' }}
                                </p>
                            </div>


                            {{-- Kelas --}}
                            <div>
                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    Kelas
                                </p>

                                <p class="text-sm text-gray-700">
                                    {{ $terlambat->murid->rombel->nama_lengkap ?? 'N/A' }}
                                </p>
                            </div>


                            {{-- NIPD --}}
                            <div>
                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    NIPD
                                </p>

                                <p class="text-sm text-gray-700">
                                    {{ $terlambat->murid->nipd ?? '-' }}
                                </p>
                            </div>


                            {{-- Alasan --}}
                            <div>
                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    Alasan
                                </p>

                                <p class="text-sm leading-relaxed text-gray-700">
                                    {{ $terlambat->keterangan ?? '-' }}
                                </p>
                            </div>


                            {{-- Tanggal --}}
                            <div>
                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    Tanggal
                                </p>

                                <p class="text-sm text-gray-700">
                                    {{ optional($terlambat->tanggal)->format('d - m - Y') ?? '-' }}
                                </p>
                            </div>


                            {{-- Waktu --}}
                            <div>
                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    Waktu
                                </p>

                                <p class="text-sm text-gray-700">
                                    {{ $terlambat->waktu_masuk ?? '-' }}
                                </p>
                            </div>


                            {{-- Status --}}
                            <div>
                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    Status
                                </p>

                                <span
                                    class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600"
                                >
                                    {{ $terlambat->status ?? '-' }}
                                </span>
                            </div>

                        </div>


                        {{-- ACTION --}}
                        <div class="flex items-center justify-end border-t border-gray-200 bg-gray-50 p-4">

                            <button
                                type="button"
                                wire:click="bukaAlasan({{ $terlambat->id }})"
                                wire:loading.attr="disabled"
                                wire:target="bukaAlasan"
                                aria-haspopup="dialog"
                                aria-controls="modal-alasan-telat"
                                aria-label="Cetak Surat Keterlambatan"
                                title="Cetak Surat Keterlambatan"
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-main text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-deep-solid"
                            >
                                <iconify-icon
                                    icon="solar:printer-bold"
                                    width="20"
                                    height="20"
                                ></iconify-icon>
                            </button>

                        </div>

                    </div>

                </div>

            @empty

                {{-- EMPTY STATE MOBILE + TABLET --}}
                <div class="px-6 py-14 text-center">

                    <div class="flex flex-col items-center justify-center">

                        <div
                            class="mb-4 flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-100 text-gray-400"
                        >
                            <iconify-icon
                                icon="solar:user-cross-bold"
                                width="38"
                                height="38"
                            ></iconify-icon>
                        </div>

                        <h2 class="text-lg font-bold text-gray-700">
                            Data izin kosong
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Belum ada data izin yang tersedia.
                        </p>

                    </div>

                </div>

            @endforelse

        </div>

        <!-- pagination -->
        <div
            class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            {{ $listMuridTerlambat->links('livewire.components.pagination') }}
        </div>

    </div>
    @include('laporan::livewire.laporan.pengawas.izin-telat.components.modal.alasan')
</div>
