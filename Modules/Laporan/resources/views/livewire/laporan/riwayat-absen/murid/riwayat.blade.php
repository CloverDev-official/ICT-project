<div class="space-y-6">

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-lg">

        <!-- effect -->
        <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-white/5"></div>

        <div
            class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- left -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:history-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-white">
                        Riwayat Absensi Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Lihat seluruh riwayat kehadiran murid berdasarkan kelas dan jurusan.
                    </p>

                </div>

            </div>

            <!-- info -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Total Murid
                </p>

                <h2 class="mt-1 text-2xl font-bold text-white">
                    {{ $totalMurid }}
                </h2>

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
                    Filter data berdasarkan tingkat, jurusan, dan kelas.
                </p>

            </div>

            <!-- search -->
            <div class="relative w-full lg:w-80">

                <input
                    type="search"
                    placeholder="Cari nama murid..."
                    wire:model.live="search"
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

        <!-- filter grid -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

            <!-- tingkat -->
            <livewire:components.searchable-select
                    wire:model.live="filterTingkat"
                    :options="$tingkatList"
                    value-key="id" label-key="nama"
                    label="Tingkat" placeholder="Cari tingkat..."
                    all-label="Semua tingkat"
                    not-found-text="Pilihan tidak ditemukan." />

            <!-- jurusan -->
            <livewire:components.searchable-select
                    wire:model.live="filterJurusan"
                    :options="$filteredJurusan"
                    value-key="id" label-key="nama"
                    label="Jurusan" placeholder="Cari jurusan..."
                    all-label="Semua jurusan"
                    not-found-text="Pilihan tidak ditemukan." />

            <!-- kelas -->
            <livewire:components.searchable-select
                    wire:model.live="filterIndeks"
                    :options="$filteredIndeks"
                    value-key="id" label-key="nama"
                    label="Kelas" placeholder="Cari kelas..."
                    all-label="Semua kelas"
                    not-found-text="Pilihan tidak ditemukan." />

        </div>

    </div>

    <!-- TABLE -->
    <div
        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- top -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Daftar Murid
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Riwayat detail absensi seluruh murid yang tersedia.
                </p>

            </div>

            <!-- badge -->
            <div
                class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                Total {{ $totalMurid }} Murid

            </div>

        </div>

        {{-- DESKTOP: TABLE --}}
        <div class="hidden overflow-x-auto lg:block">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">
                    <tr>

                        <th class="px-5 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            NIPD
                        </th>

                        <th class="px-5 py-4 text-left font-semibold">
                            Nama Murid
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            NISN
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Detail
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse ($listMurid as $item)

                        <tr class="border-t border-gray-100 transition hover:bg-gray-50">

                            {{-- No --}}
                            <td class="px-5 py-5 text-center font-medium text-gray-700">
                                {{ $loop->iteration }}
                            </td>

                            {{-- NIPD --}}
                            <td class="px-5 py-5 text-center text-gray-600">
                                {{ $item->nipd }}
                            </td>

                            {{-- Nama Murid --}}
                            <td class="px-5 py-5">

                                <div class="flex items-center gap-4">

                                    <x-murid-avatar
                                        :murid="$item"
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 font-bold uppercase text-blue-main"
                                    />

                                    <div>

                                        <h2 class="font-semibold capitalize text-gray-800">
                                            {{ $item->nama }}
                                        </h2>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Data murid aktif
                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- NISN --}}
                            <td class="px-5 py-5 text-center text-gray-600">
                                {{ $item->nisn }}
                            </td>

                            {{-- Detail --}}
                            <td class="px-5 py-5">

                                <div class="flex items-center justify-center">

                                    <a
                                        href="{{ route('riwayat-detail-absen-murid', $item->uuid) }}"
                                        wire:navigate
                                    >
                                        <button
                                            type="button"
                                            class="group flex items-center gap-2 rounded-2xl bg-blue-main px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-deep-solid"
                                        >
                                            <iconify-icon
                                                icon="mdi:eye"
                                                width="18"
                                                height="18"
                                                class="transition group-hover:scale-110"
                                            ></iconify-icon>

                                            Detail
                                        </button>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-5 py-8 text-center text-sm text-gray-400"
                            >
                                Data murid belum tersedia.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- MOBILE + TABLET: COLLAPSED CARD --}}
        <div class="space-y-3 lg:hidden">

            @forelse ($listMurid as $item)

                <div
                    x-data="{ open: false }"
                    class="overflow-hidden border border-t-0 border-gray-200 bg-white"
                >

                    {{-- SUMMARY --}}
                    <div class="flex items-center gap-3 p-4">

                        {{-- No --}}
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-sm font-semibold text-gray-600"
                        >
                            {{ $loop->iteration }}
                        </div>

                        {{-- Avatar --}}
                        <x-murid-avatar
                            :murid="$item"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-100 font-bold uppercase text-blue-main"
                        />

                        {{-- Informasi utama --}}
                        <div class="min-w-0 flex-1">

                            <h3 class="truncate font-semibold capitalize text-gray-800">
                                {{ $item->nama }}
                            </h3>

                            <p class="mt-0.5 truncate text-xs text-gray-500">
                                NIPD: {{ $item->nipd }}
                            </p>

                        </div>

                        {{-- Chevron --}}
                        <button
                            type="button"
                            @click="open = !open"
                            :aria-expanded="open"
                            aria-label="Lihat detail murid"
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

                            {{-- Nama Murid --}}
                            <div>

                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    Nama Murid
                                </p>

                                <p class="font-semibold capitalize text-gray-800">
                                    {{ $item->nama }}
                                </p>

                            </div>


                            {{-- NIPD --}}
                            <div>

                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    NIPD
                                </p>

                                <p class="text-sm text-gray-700">
                                    {{ $item->nipd }}
                                </p>

                            </div>


                            {{-- NISN --}}
                            <div>

                                <p class="mb-1 text-xs font-medium text-gray-500">
                                    NISN
                                </p>

                                <p class="text-sm text-gray-700">
                                    {{ $item->nisn }}
                                </p>

                            </div>

                        </div>


                        {{-- ACTION --}}
                        <div
                            class="flex items-center justify-end border-t border-gray-200 bg-gray-50 p-4"
                        >

                            <a
                                href="{{ route('riwayat-detail-absen-murid', $item->uuid) }}"
                                wire:navigate
                            >
                                <button
                                    type="button"
                                    class="group flex items-center gap-2 rounded-xl bg-blue-main px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-deep-solid"
                                >
                                    <iconify-icon
                                        icon="mdi:eye"
                                        width="18"
                                        height="18"
                                        class="transition group-hover:scale-110"
                                    ></iconify-icon>

                                    Detail
                                </button>
                            </a>

                        </div>

                    </div>

                </div>

            @empty

                {{-- EMPTY STATE MOBILE + TABLET --}}
                <div class="px-5 py-8 text-center text-sm text-gray-400">
                    Data murid belum tersedia.
                </div>

            @endforelse

        </div>

        <div
            class="border-t border-gray-200 bg-gray-50 px-6 py-4">

            {{ $listMurid->links('livewire.components.pagination') }}

        </div>

    </div>

</div>
