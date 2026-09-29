<div class="space-y-6">

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main via-blue-deep to-[#07162f] p-6 shadow-lg">

        <!-- ornament -->
        <div class="absolute -top-10 -right-10 h-44 w-44 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 h-36 w-36 rounded-full bg-cyan-300/10 blur-2xl"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur sm:flex">

                    <iconify-icon
                        icon="solar:users-group-rounded-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Manajemen Foto Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Kelola foto murid berdasarkan data nama dan NIPD.
                    </p>
                </div>

            </div>

            <!-- actions -->
            <div class="grid grid-cols-1 gap-3 sm:flex sm:flex-row">

                <a href="{{ route('tambah-foto') }}" wire:navigate>
                    <button
                        class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-blue-main shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95">

                        <iconify-icon
                            icon="line-md:plus"
                            width="22"
                            height="22"
                            class="transition duration-300 group-hover:rotate-90">
                        </iconify-icon>

                        Tambah Foto Murid

                    </button>
                </a>

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
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

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

            <!-- kelas -->
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

        </div>

    </div>

    <!-- TABLE CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- table header -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Daftar Foto Murid
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    List murid yang dapat diperbarui foto profilnya.
                </p>
            </div>

        </div>

        {{-- DESKTOP TABLE --}}
        <div class="hidden overflow-x-auto lg:block">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="px-5 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Foto Murid
                        </th>

                        <th class="px-5 py-4 text-left font-semibold">
                            Nama
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            NIPD
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($listMurid as $murid)

                        <tr
                            class="border-t border-gray-100 transition hover:bg-gray-50">

                            {{-- no --}}
                            <td class="px-5 py-5 text-center font-medium text-gray-700">

                                {{ $loop->iteration }}

                            </td>

                            {{-- foto --}}
                            <td class="px-5 py-5">

                                <div class="flex justify-center">

                                    <x-murid-avatar
                                        :murid="$murid"
                                        class="h-16 w-16 rounded-2xl border border-gray-200 bg-gray-100 text-xl font-bold text-blue-main shadow-sm" />

                                </div>

                            </td>

                            {{-- nama --}}
                            <td class="px-5 py-5">

                                <div>

                                    <h3 class="font-semibold capitalize text-gray-800">
                                        {{ $murid->nama }}
                                    </h3>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Data foto murid
                                    </p>

                                </div>

                            </td>

                            {{-- nipd --}}
                            <td class="px-5 py-5 text-center">

                                <div
                                    class="inline-flex rounded-full bg-blue-50 px-4 py-1.5 text-xs font-semibold text-blue-main">

                                    {{ $murid->nipd }}

                                </div>

                            </td>

                            {{-- aksi --}}
                            <td class="px-5 py-5">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- edit foto --}}
                                    <a
                                        href="{{ route('edit-foto', ['murid' => $murid->uuid]) }}"
                                        wire:navigate>

                                        <button
                                            type="button"
                                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-400 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-500">

                                            <iconify-icon
                                                icon="solar:gallery-edit-bold"
                                                width="20"
                                                height="20">
                                            </iconify-icon>

                                        </button>

                                    </a>

                                    {{-- delete --}}
                                    <button
                                        @click="$dispatch('open-delete-foto', { id: {{ $murid->id }} })"
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-rose-600">

                                        <iconify-icon
                                            icon="lineicons:trash-3"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr class="border-t border-gray-100">

                            <td
                                colspan="5"
                                class="px-5 py-10 text-center text-sm text-gray-500">

                                Belum ada data murid untuk ditampilkan.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- MOBILE + TABLET COLLAPSIBLE --}}
        <div class="space-y-3 lg:hidden">

            @forelse ($listMurid as $murid)

                <div
                    x-data="{ open: false }"
                    class="overflow-hidden border border-t-0 border-gray-200 bg-white">

                    {{-- SUMMARY --}}
                    <div class="flex items-center gap-3 p-4">

                        {{-- no --}}
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-sm font-semibold text-gray-600">

                            {{ $loop->iteration }}

                        </div>

                        {{-- avatar --}}
                        <x-murid-avatar
                            :murid="$murid"
                            class="h-12 w-12 shrink-0 rounded-xl border border-gray-200 bg-gray-100 text-lg font-bold text-blue-main shadow-sm" />

                        {{-- information --}}
                        <div class="min-w-0 flex-1">

                            <h3 class="truncate font-semibold capitalize text-gray-800">

                                {{ $murid->nama }}

                            </h3>

                            <div class="mt-1">

                                <span
                                    class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-main">

                                    {{ $murid->nipd }}

                                </span>

                            </div>

                        </div>

                        {{-- expand --}}
                        <button
                            type="button"
                            @click="open = !open"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-gray-500 transition hover:bg-gray-100 hover:text-gray-700"
                            :aria-expanded="open">

                            <iconify-icon
                                icon="lineicons:chevron-down"
                                width="20"
                                height="20"
                                class="transition-transform duration-200"
                                :class="{ 'rotate-180': open }">
                            </iconify-icon>

                        </button>

                    </div>

                    {{-- COLLAPSED DETAIL --}}
                    <div
                        x-show="open"
                        x-collapse
                        class="border-t border-gray-200 bg-gray-50">

                        <div class="p-5">

                            {{-- foto --}}
                            <div class="flex flex-col items-center">

                                <x-murid-avatar
                                    :murid="$murid"
                                    class="h-24 w-24 rounded-3xl border border-gray-200 bg-gray-100 text-2xl font-bold text-blue-main shadow-sm" />

                                <p class="mt-3 text-xs text-gray-400">
                                    Foto Murid
                                </p>

                            </div>


                            {{-- information --}}
                            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">

                                {{-- nama --}}
                                <div>

                                    <p class="text-xs font-medium text-gray-400">
                                        Nama
                                    </p>

                                    <p class="mt-1 text-sm font-semibold capitalize text-gray-700">
                                        {{ $murid->nama }}
                                    </p>

                                </div>

                                {{-- nipd --}}
                                <div>

                                    <p class="text-xs font-medium text-gray-400">
                                        NIPD
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-700">
                                        {{ $murid->nipd ?: '-' }}
                                    </p>

                                </div>

                            </div>

                        </div>

                        {{-- ACTIONS --}}
                        <div class="flex items-center gap-2 border-t border-gray-200 p-4">

                            {{-- edit foto --}}
                            <a
                                href="{{ route('edit-foto', ['murid' => $murid->uuid]) }}"
                                wire:navigate
                                class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-amber-400 px-4 py-3 text-sm font-semibold text-white transition hover:bg-amber-500">

                                <iconify-icon
                                    icon="solar:gallery-edit-bold"
                                    width="18"
                                    height="18">
                                </iconify-icon>

                                Edit Foto

                            </a>

                            {{-- delete --}}
                            <button
                                type="button"
                                @click="$dispatch('open-delete-foto', { id: {{ $murid->id }} })"
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white transition hover:bg-rose-600">

                                <iconify-icon
                                    icon="lineicons:trash-3"
                                    width="20"
                                    height="20">
                                </iconify-icon>

                            </button>

                        </div>

                    </div>

                </div>

            @empty

                {{-- empty state --}}
                <div
                    class="px-5 py-10 text-center text-sm text-gray-500">

                    Belum ada data murid untuk ditampilkan.

                </div>

            @endforelse

        </div>

        <!-- footer -->
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <p class="text-sm text-gray-500">
                    Menampilkan data foto murid.
                </p>

                <div class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">
                    Total {{ $totalMurid }} Murid
                </div>

            </div>

            <div class="mt-4">
                {{ $listMurid->links('livewire.components.pagination') }}
            </div>

        </div>

    </div>

    <livewire:manajemen.murid-foto.components.modal.hapus/>
</div>
