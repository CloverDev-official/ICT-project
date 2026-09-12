<div class="space-y-6">

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-lg">

        <!-- effect -->
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-1 -left-1 h-32 w-32 rounded-full bg-white/5"></div>

        <div
            class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:clipboard-check-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-white">
                        Absensi Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Pantau data kehadiran murid secara realtime.
                    </p>

                </div>

            </div>

            <!-- info -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Total Data
                </p>

                <h2 class="mt-1 text-2xl font-bold text-white">
                    {{ $listAbsen->total() }}
                </h2>

            </div>

        </div>

    </div>

    <!-- FILTER -->
    <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">

        <!-- top -->
        <div
            class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <h2 class="text-lg font-bold text-gray-800">
                    Filter Data
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Filter berdasarkan tingkat, jurusan, kelas, dan tanggal.
                </p>

            </div>

            <!-- search -->
            <div class="relative w-full lg:w-80">

                <input
                    type="search"
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

            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Data Kehadiran
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Rekap absensi murid berdasarkan filter yang dipilih.
                </p>

            </div>

            <!-- filter kehadiran -->
            <div>
                <livewire:components.searchable-select
                    wire:model.live="status"
                    placeholder="Cari status kehadiran..."
                    :options="self::statusOptions()"
                    value-key="id"
                    label-key="nama"
                    all-label="Semua status"
                    not-found-text="status kehadiran tidak ditemukan."
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

            <!-- badge -->
            <!-- <div class="flex items-center gap-2">

                <div
                    class="rounded-full bg-green-100 px-4 py-1.5 text-xs font-semibold text-green-600">
                    Hadir
                </div>

                <div
                    class="rounded-full bg-blue-100 px-4 py-1.5 text-xs font-semibold text-blue-600">
                    Izin
                </div>

                <div
                    class="rounded-full bg-amber-100 px-4 py-1.5 text-xs font-semibold text-amber-600">
                    Sakit
                </div>

                <div
                    class="rounded-full bg-rose-100 px-4 py-1.5 text-xs font-semibold text-rose-600">
                    Alpa
                </div>

            </div> -->

        </div>

        <!-- table -->
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="px-5 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Murid
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Kehadiran
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Tanggal
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Jam Masuk
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Jam Pulang
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Keterangan
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($listAbsen as $item)

                    @php
                    $statusValue = strtolower((string) $item->status);

                    $statusClass = match ($statusValue) {
                    'sakit' => 'text-amber-700 bg-amber-100',
                    'izin' => 'text-blue-700 bg-blue-100',
                    'alpa' => 'text-rose-700 bg-rose-100',
                    'masuk' => 'text-cyan-700 bg-cyan-100',
                    'hadir' => 'text-emerald-700 bg-emerald-100',
                    'terlambat' => 'text-orange-700 bg-orange-100',
                    default => 'text-gray-700 bg-gray-100',
                    };
                    @endphp

                    <tr
                        class="border-t border-gray-100 transition hover:bg-gray-50">

                        <!-- no -->
                        <td
                            class="px-5 py-5 text-center font-medium text-gray-700">

                            {{ $loop->iteration }}

                        </td>

                        <!-- murid -->
                        <td class="px-5 py-5">

                            <div class="flex items-center gap-4">

                                <!-- avatar -->
                                <x-murid-avatar :murid="$item->murid" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 font-bold uppercase text-blue-main" />

                                <!-- info -->
                                <div>

                                    <h2
                                        class="font-semibold text-gray-800">

                                        {{ $item->murid->nama }}

                                    </h2>

                                    <p
                                        class="mt-1 text-xs text-gray-400">

                                        NIPD :
                                        {{ $item->murid->nipd }}

                                    </p>

                                </div>

                            </div>

                        </td>

                        <!-- status -->
                        <td class="px-5 py-5 text-center">

                            <div
                                class="{{ $statusClass }} inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold capitalize">

                                <div
                                    class="h-2 w-2 rounded-full bg-current">
                                </div>

                                {{ $item->status }}

                            </div>

                        </td>

                        <!-- tanggal -->
                        <td
                            class="px-5 py-5 text-center text-gray-600">

                            {{ $item->tanggal->format('d-m-Y') }}

                        </td>

                        <!-- masuk -->
                        <td class="px-5 py-5 text-center">

                            <div
                                class="inline-flex rounded-full bg-emerald-100 px-4 py-1 text-xs font-semibold text-emerald-600">

                                {{ $item->waktu_masuk }}

                            </div>

                        </td>

                        <!-- pulang -->
                        <td class="px-5 py-5 text-center">

                            <div
                                class="inline-flex rounded-full bg-blue-100 px-4 py-1 text-xs font-semibold text-blue-600">

                                {{ $item->waktu_keluar }}

                            </div>

                        </td>

                        <!-- ket -->
                        <td class="px-5 py-5 text-gray-600 text-center">
                            {{ $item->keterangan }}
                        </td>

                        <td class="py-5 px-5">
                            <div class="flex items-center justify-center">
                                <!-- edit -->
                                <a
                                    href="{{ route('edit-absen-murid', $item->id) }}"
                                    wire:navigate>

                                    <button
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-400 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-500">

                                        <iconify-icon
                                            icon="lineicons:pencil-1"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                    </button>

                                </a>
                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="8"
                            class="px-6 py-14 text-center">

                            <div
                                class="flex flex-col items-center justify-center">

                                <div
                                    class="mb-4 flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-100 text-gray-400">

                                    <iconify-icon
                                        icon="solar:box-bold"
                                        width="38"
                                        height="38">
                                    </iconify-icon>

                                </div>

                                <h2
                                    class="text-lg font-bold text-gray-700">

                                    Data tidak ditemukan

                                </h2>

                                <p
                                    class="mt-1 text-sm text-gray-500">

                                    Tidak ada data absensi yang tersedia.

                                </p>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <!-- pagination -->
        <div
            class="border-t border-gray-200 bg-gray-50 px-6 py-4">

            {{ $listAbsen->links('livewire.components.pagination') }}

        </div>

    </div>

</div>
