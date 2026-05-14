<div class="space-y-6">

    <!-- HERO -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-lg">

        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-5">
                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">
                    <iconify-icon icon="solar:user-check-bold" width="34" height="34" class="text-white"></iconify-icon>
                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Rekap Kehadiran Guru
                    </h1>
                    <p class="mt-1 text-sm text-blue-100">
                        Pantau statistik, grafik, dan laporan kehadiran guru.
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Persentase Hari Ini
                </p>
                <h2 class="mt-1 text-3xl font-bold text-white">
                    85%
                </h2>
            </div>

        </div>
    </div>

    <!-- STAT CARD -->
    <div class="grid grid-cols-2 gap-5 xl:grid-cols-5">

        @foreach ([
            ['label' => 'Jumlah Guru', 'value' => '80', 'icon' => 'mdi:account-group', 'class' => 'bg-blue-100 text-blue-main'],
            ['label' => 'Hadir', 'value' => '65', 'icon' => 'mdi:check-circle', 'class' => 'bg-emerald-100 text-emerald-600'],
            ['label' => 'Sakit', 'value' => '10', 'icon' => 'mdi:emoticon-sick-outline', 'class' => 'bg-amber-100 text-amber-600'],
            ['label' => 'Izin', 'value' => '5', 'icon' => 'mdi:file-document-outline', 'class' => 'bg-orange-100 text-orange-500'],
            ['label' => 'Persentase', 'value' => '85%', 'icon' => 'mdi:chart-donut', 'class' => 'bg-cyan-100 text-cyan-600'],
        ] as $stat)

            <div
                class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-gray-100 blur-2xl"></div>

                <div class="relative flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-400">
                            {{ $stat['label'] }}
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-gray-800">
                            {{ $stat['value'] }}
                        </h2>
                    </div>

                    <div class="{{ $stat['class'] }} flex h-14 w-14 items-center justify-center rounded-2xl">
                        <iconify-icon icon="{{ $stat['icon'] }}" width="28"></iconify-icon>
                    </div>

                </div>

            </div>

        @endforeach

    </div>

    <!-- FILTER -->
    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">

        <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Filter Rekap
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Filter rekap absensi guru berdasarkan tanggal dan jenis PTK.
                </p>
            </div>

            <div class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">
                Data realtime
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- tanggal -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tanggal
                </label>

                <input
                    type="date"
                    wire:model.live="filterTanggal"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
            </div>

            <!-- jenis ptk -->
            <div
                x-data="{
                    open: false,
                    selectedId: null,
                    selectedLabel: 'Semua jenis PTK',

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Jenis PTK
                </label>

                <div
                    @click="open = !open"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                    <span x-text="selectedLabel" class="line-clamp-1 text-gray-700"></span>

                    <iconify-icon
                        icon="lineicons:chevron-up"
                        width="20"
                        height="20"
                        class="text-gray-400 transition-transform"
                        :class="{ 'rotate-180': open }">
                    </iconify-icon>
                </div>

                <div
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    style="display:none"
                    class="absolute z-50 mt-2 max-h-44 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl">

                    <div
                        @click.prevent="select(null, 'Semua jenis PTK')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">
                        <span>Semua jenis PTK</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="18"></iconify-icon>
                    </div>

                    @foreach (['Mapel', 'BK'] as $jenis)
                        <div
                            @click.prevent="select('{{ $jenis }}', '{{ $jenis }}')"
                            class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">
                            <span>{{ $jenis }}</span>
                            <iconify-icon x-show="selectedId === '{{ $jenis }}'" icon="lineicons:check" width="18"></iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>
    </div>

    <!-- CHART -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <div class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Statistik Kehadiran
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Grafik rekap kehadiran guru berdasarkan periode.
                </p>
            </div>

            <div class="flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-600">
                <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                Hadir
            </div>
        </div>

        <div class="p-6">
            <div id="chart-rekap-absen-guru" class="h-[380px] w-full"></div>
        </div>

    </div>

    <!-- EXPORT -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <div class="border-b border-gray-200 px-6 py-5">
            <h2 class="text-xl font-bold text-gray-800">
                Export Laporan
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Unduh laporan kehadiran guru dalam format DOCX, PDF, atau Excel.
            </p>
        </div>

        <div class="grid gap-6 p-6 lg:grid-cols-2">

            <div class="space-y-5">

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Pilih Tanggal
                    </label>

                    <input
                        type="date"
                        class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm transition hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                </div>

                <div
                    x-data="{
                        open: false,
                        selected: '',

                        select(item) {
                            this.selected = item
                            this.open = false
                        }
                    }"
                    class="relative">

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Pilih Jenis PTK
                    </label>

                    <div
                        @click="open = !open"
                        class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 px-4 py-3 text-sm text-gray-700 transition hover:border-blue-main">

                        <span x-text="selected ? selected : 'Semua jenis PTK'"></span>

                        <iconify-icon
                            icon="lineicons:chevron-down"
                            width="18"
                            class="text-gray-400 transition"
                            :class="{ 'rotate-180': open }">
                        </iconify-icon>

                    </div>

                    <div
                        x-show="open"
                        x-transition
                        @click.outside="open = false"
                        style="display:none"
                        class="absolute z-50 mt-2 max-h-52 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl">

                        @foreach (['Semua jenis PTK', 'Mapel', 'BK'] as $jenis)
                            <div
                                @click="select('{{ $jenis }}')"
                                class="flex cursor-pointer items-center justify-between px-4 py-3 text-sm transition hover:bg-blue-main hover:text-white">

                                <span>{{ $jenis }}</span>

                                <iconify-icon
                                    x-show="selected === '{{ $jenis }}'"
                                    icon="lineicons:check"
                                    width="18">
                                </iconify-icon>

                            </div>
                        @endforeach

                    </div>
                </div>

            </div>

            <div class="flex flex-col justify-center rounded-3xl bg-gradient-to-br from-blue-main to-blue-deep p-6 text-white">

                <h2 class="text-2xl font-bold">
                    Export Cepat
                </h2>

                <p class="mt-2 text-sm text-blue-100">
                    Pilih format file yang ingin digunakan untuk laporan kehadiran.
                </p>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">

                    <button class="flex items-center justify-center gap-2 rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold backdrop-blur transition hover:bg-white/20">
                        <iconify-icon icon="mdi:file-word" width="18"></iconify-icon>
                        DOCX
                    </button>

                    <button class="flex items-center justify-center gap-2 rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold backdrop-blur transition hover:bg-white/20">
                        <iconify-icon icon="mdi:file-pdf-box" width="18"></iconify-icon>
                        PDF
                    </button>

                    <button class="flex items-center justify-center gap-2 rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-semibold transition hover:bg-emerald-600">
                        <iconify-icon icon="mdi:file-excel" width="18"></iconify-icon>
                        Excel
                    </button>

                </div>

            </div>

        </div>
    </div>

    <!-- TABLE -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <div class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Guru Tidak Hadir
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Daftar guru yang tidak hadir berdasarkan status dan nama.
                </p>
            </div>

            <div class="relative w-full lg:w-80">
                <input
                    type="search"
                    name="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari nama guru..."
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 pl-4 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

                <div class="absolute inset-y-0 right-4 flex items-center text-gray-400">
                    <iconify-icon icon="mdi:account-search-outline" width="22" height="22"></iconify-icon>
                </div>
            </div>

        </div>

        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">

            <div
                x-data="{
                    open: false,
                    selectedId: null,
                    selectedLabel: 'Semua status',

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false
                    }
                }"
                class="relative w-full md:w-72">

                <div
                    @click="open = !open"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm transition hover:border-blue-main">

                    <span x-text="selectedLabel" class="text-gray-700"></span>

                    <iconify-icon
                        icon="lineicons:chevron-up"
                        width="20"
                        height="20"
                        class="text-gray-400 transition-transform"
                        :class="{ 'rotate-180': open }">
                    </iconify-icon>
                </div>

                <div
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    style="display:none"
                    class="absolute z-50 mt-2 w-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">

                    <div
                        @click.prevent="select(null, 'Semua status')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">
                        <span>Semua status</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="18"></iconify-icon>
                    </div>

                    @foreach (['sakit', 'izin', 'alpa'] as $status)
                        <div
                            @click.prevent="select('{{ $status }}', '{{ $status }}')"
                            class="flex cursor-pointer items-center justify-between px-4 py-3 capitalize transition hover:bg-blue-main hover:text-white">
                            <span>{{ $status }}</span>
                            <iconify-icon x-show="selectedId === '{{ $status }}'" icon="lineicons:check" width="18"></iconify-icon>
                        </div>
                    @endforeach

                </div>

            </div>

        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-5 py-4 text-center font-semibold">No</th>
                        <th class="px-5 py-4 text-left font-semibold">Guru</th>
                        <th class="px-5 py-4 text-center font-semibold">Jenis PTK</th>
                        <th class="px-5 py-4 text-center font-semibold">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ([
                        ['nama' => 'Budi', 'jenis' => 'Mapel', 'status' => 'Sakit'],
                        ['nama' => 'Andi', 'jenis' => 'BK', 'status' => 'Izin'],
                        ['nama' => 'Anda', 'jenis' => 'Mapel', 'status' => 'Alpa'],
                    ] as $item)

                        @php
                            $statusValue = strtolower((string) $item['status']);

                            $statusClass = match ($statusValue) {
                                'sakit' => 'bg-amber-100 text-amber-600',
                                'izin' => 'bg-blue-100 text-blue-600',
                                'alpa' => 'bg-rose-100 text-rose-600',
                                default => 'bg-green-100 text-green-600',
                            };
                        @endphp

                        <tr class="border-t border-gray-100 transition hover:bg-gray-50">

                            <td class="px-5 py-5 text-center font-medium text-gray-700">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-5 py-5">

                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 font-bold uppercase text-blue-main">

                                        {{ substr($item['nama'], 0, 1) }}

                                    </div>

                                    <div>
                                        <h2 class="font-semibold text-gray-800">
                                            {{ $item['nama'] }}
                                        </h2>
                                        <p class="mt-1 text-xs text-gray-400">
                                            Guru aktif
                                        </p>
                                    </div>

                                </div>

                            </td>

                            <td class="px-5 py-5 text-center">
                                <div class="inline-flex rounded-full bg-blue-100 px-4 py-1 text-xs font-semibold text-blue-600">
                                    {{ $item['jenis'] }}
                                </div>
                            </td>

                            <td class="px-5 py-5 text-center">
                                <div class="{{ $statusClass }} inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold capitalize">
                                    <div class="h-2 w-2 rounded-full bg-current"></div>
                                    {{ $item['status'] }}
                                </div>
                            </td>

                        </tr>

                    @endforeach
                </tbody>

            </table>
        </div>

    </div>

</div>