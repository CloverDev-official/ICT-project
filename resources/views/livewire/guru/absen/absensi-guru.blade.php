<div class="space-y-6">

    <!-- HERO -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-lg">

        <!-- glow -->
        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-white/10"></div>

        <div
            class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <!-- left -->
            <div class="flex items-center gap-5">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:user-check-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-white">
                        Absensi Guru
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Pantau seluruh data kehadiran guru berdasarkan tanggal dan jenis PTK.
                    </p>

                </div>

            </div>

            <!-- right -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Total Data
                </p>

                <h2 class="mt-1 text-2xl font-bold text-white">
                    3
                </h2>

            </div>

        </div>

    </div>

    <!-- FILTER -->
    <div
        class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">

        <!-- title -->
        <div
            class="mb-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">

            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Filter Data
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Filter absensi guru berdasarkan status, jenis PTK dan tanggal.
                </p>

            </div>

            <div
                class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                Data realtime

            </div>

        </div>

        <!-- filter grid -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

            <!-- STATUS -->
            <div
                x-data="{
                    open: false,
                    selectedId: null,
                    selectedLabel: null,

                    toggle() {
                        this.open = !this.open
                    },

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false

                        $wire.set('filterStatus', id)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Status Kepegawaian
                </label>

                <!-- trigger -->
                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                    <span
                        x-text="selectedLabel ?? 'Semua status'"
                        class="text-gray-700">
                    </span>

                    <iconify-icon
                        icon="lineicons:chevron-up"
                        width="20"
                        height="20"
                        class="text-gray-400 transition-transform"
                        :class="{ 'rotate-180': open }">
                    </iconify-icon>

                </div>

                <!-- dropdown -->
                <div
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    style="display:none"
                    class="absolute z-50 mt-2 max-h-52 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl">

                    <div
                        @click.prevent="select(null, 'Semua status')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                        <span>Semua status</span>

                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="18">
                        </iconify-icon>

                    </div>

                    @foreach (['PNS', 'Honorer'] as $index => $status)

                        <div
                            @click.prevent="select({{ $index }}, '{{ $status }}')"
                            class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                            <span>{{ $status }}</span>

                            <iconify-icon
                                x-show="selectedId == {{ $index }}"
                                icon="lineicons:check"
                                width="18">
                            </iconify-icon>

                        </div>

                    @endforeach

                </div>

            </div>

            <!-- JENIS PTK -->
            <div
                x-data="{
                    open: false,
                    selectedId: null,
                    selectedLabel: null,

                    toggle() {
                        this.open = !this.open
                    },

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false

                        $wire.set('filterJenis', id)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Jenis PTK
                </label>

                <!-- trigger -->
                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                    <span
                        x-text="selectedLabel ?? 'Semua jenis'"
                        class="text-gray-700">
                    </span>

                    <iconify-icon
                        icon="lineicons:chevron-up"
                        width="20"
                        height="20"
                        class="text-gray-400 transition-transform"
                        :class="{ 'rotate-180': open }">
                    </iconify-icon>

                </div>

                <!-- dropdown -->
                <div
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    style="display:none"
                    class="absolute z-50 mt-2 max-h-52 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl">

                    <div
                        @click.prevent="select(null, 'Semua jenis')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                        <span>Semua jenis</span>

                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="18">
                        </iconify-icon>

                    </div>

                    @foreach (['Guru Mapel', 'Guru BK'] as $index => $jenis)

                        <div
                            @click.prevent="select({{ $index }}, '{{ $jenis }}')"
                            class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                            <span>{{ $jenis }}</span>

                            <iconify-icon
                                x-show="selectedId == {{ $index }}"
                                icon="lineicons:check"
                                width="18">
                            </iconify-icon>

                        </div>

                    @endforeach

                </div>

            </div>

            <!-- tanggal -->
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
            class="flex flex-col gap-5 border-b border-gray-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Daftar Absensi Guru
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Rekap absensi guru pada tanggal
                    <span class="font-semibold text-gray-700">
                        {{ $dateNow ?? '14 mei 2026' }}
                    </span>
                </p>

            </div>

            <!-- search -->
            <div class="relative w-full lg:w-80">

                <input
                    type="search"
                    name="search"
                    placeholder="Cari nama guru..."
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

        <!-- table -->
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="px-5 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            NUPTK
                        </th>

                        <th class="px-5 py-4 text-left font-semibold">
                            Nama Guru
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Kehadiran
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Jam Masuk
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Jam Pulang
                        </th>

                        <th class="px-5 py-4 text-left font-semibold">
                            Keterangan
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach ([
                        [
                            'nuptk' => '123456789',
                            'nama' => 'Ghaizan',
                            'status' => 'hadir',
                            'masuk' => '07.00',
                            'pulang' => '16.30',
                            'keterangan' => '-',
                        ],
                        [
                            'nuptk' => '987654321',
                            'nama' => 'Budi Santoso',
                            'status' => 'izin',
                            'masuk' => '-',
                            'pulang' => '-',
                            'keterangan' => 'Acara keluarga',
                        ],
                    ] as $item)

                        @php
                            $statusValue = strtolower((string) $item['status']);

                            $statusClass = match ($statusValue) {
                                'izin' => 'bg-blue-100 text-blue-600',
                                'sakit' => 'bg-amber-100 text-amber-600',
                                'alpa' => 'bg-rose-100 text-rose-600',
                                default => 'bg-emerald-100 text-emerald-600',
                            };
                        @endphp

                        <tr
                            class="border-t border-gray-100 transition hover:bg-gray-50">

                            <!-- no -->
                            <td
                                class="px-5 py-5 text-center font-medium text-gray-700">

                                {{ $loop->iteration }}

                            </td>

                            <!-- nuptk -->
                            <td
                                class="px-5 py-5 text-center text-gray-600">

                                {{ $item['nuptk'] }}

                            </td>

                            <!-- guru -->
                            <td class="px-5 py-5">

                                <div class="flex items-center gap-4">

                                    <!-- avatar -->
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 font-bold uppercase text-blue-main">

                                        {{ substr($item['nama'], 0, 1) }}

                                    </div>

                                    <!-- info -->
                                    <div>

                                        <h2
                                            class="font-semibold text-gray-800">

                                            {{ $item['nama'] }}

                                        </h2>

                                        <p
                                            class="mt-1 text-xs text-gray-400">

                                            Guru aktif

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

                                    {{ $item['status'] }}

                                </div>

                            </td>

                            <!-- masuk -->
                            <td class="px-5 py-5 text-center">

                                <div
                                    class="inline-flex rounded-full bg-emerald-100 px-4 py-1 text-xs font-semibold text-emerald-600">

                                    {{ $item['masuk'] }}

                                </div>

                            </td>

                            <!-- pulang -->
                            <td class="px-5 py-5 text-center">

                                <div
                                    class="inline-flex rounded-full bg-blue-100 px-4 py-1 text-xs font-semibold text-blue-600">

                                    {{ $item['pulang'] }}

                                </div>

                            </td>

                            <!-- ket -->
                            <td
                                class="px-5 py-5 text-gray-600">

                                {{ $item['keterangan'] }}

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>