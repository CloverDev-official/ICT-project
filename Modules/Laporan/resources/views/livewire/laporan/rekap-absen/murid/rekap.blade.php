<div class="space-y-6">

    <!-- HERO -->
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
                        Rekap Kehadiran Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Statistik dan laporan kehadiran murid secara realtime.
                    </p>

                </div>

            </div>

            <!-- info -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Kehadiran Hari Ini
                </p>

                <h2 class="mt-1 text-3xl font-bold text-white">
                    {{ $statistik['persentase'] ?? 0 }}%
                </h2>

            </div>

        </div>

    </div>

    <!-- STATISTIC -->
    <div class="grid grid-cols-2 gap-5 xl:grid-cols-6">

        <!-- total -->
        <div
            class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-blue-100 blur-2xl"></div>

            <div class="relative flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Jumlah Murid
                    </p>

                    <h2 class="mt-2 text-3xl font-bold text-gray-800">
                        {{ number_format($statistik['totalMurid'] ?? 0) }}
                    </h2>

                </div>

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">

                    <iconify-icon
                        icon="mdi:account-group"
                        width="28">
                    </iconify-icon>

                </div>

            </div>

        </div>

        <!-- hadir -->
        <div
            class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-emerald-100 blur-2xl"></div>

            <div class="relative flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Hadir
                    </p>

                    <h2 class="mt-2 text-3xl font-bold text-emerald-600">
                        {{ number_format($statistik['hadir'] ?? 0) }}
                    </h2>

                </div>

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">

                    <iconify-icon
                        icon="mdi:check-circle"
                        width="28">
                    </iconify-icon>

                </div>

            </div>

        </div>

        <!-- sakit -->
        <div
            class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-amber-100 blur-2xl"></div>

            <div class="relative flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Sakit
                    </p>

                    <h2 class="mt-2 text-3xl font-bold text-amber-600">
                        {{ number_format($statistik['sakit'] ?? 0) }}
                    </h2>

                </div>

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">

                    <iconify-icon
                        icon="mdi:emoticon-sick-outline"
                        width="28">
                    </iconify-icon>

                </div>

            </div>

        </div>

        <!-- izin -->
        <div
            class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-orange-100 blur-2xl"></div>

            <div class="relative flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Izin
                    </p>

                    <h2 class="mt-2 text-3xl font-bold text-orange-500">
                        {{ number_format($statistik['izin'] ?? 0) }}
                    </h2>

                </div>

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100 text-orange-500">

                    <iconify-icon
                        icon="mdi:file-document-outline"
                        width="28">
                    </iconify-icon>

                </div>

            </div>

        </div>

        <!-- alpa -->
        <div
            class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-rose-100 blur-2xl"></div>

            <div class="relative flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Alpa
                    </p>

                    <h2 class="mt-2 text-3xl font-bold text-rose-600">
                        {{ number_format($statistik['alpa'] ?? 0) }}
                    </h2>

                </div>

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">

                    <iconify-icon
                        icon="mdi:close-circle-outline"
                        width="28">
                    </iconify-icon>

                </div>

            </div>

        </div>

        <!-- persentase -->
        <div
            class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-cyan-100 blur-2xl"></div>

            <div class="relative flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Persentase
                    </p>

                    <h2 class="mt-2 text-3xl font-bold text-cyan-600">
                        {{ $statistik['persentase'] ?? 0 }}%
                    </h2>

                </div>

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-600">

                    <iconify-icon
                        icon="mdi:chart-donut"
                        width="28">
                    </iconify-icon>

                </div>

            </div>

        </div>

    </div>

    <!-- FILTER -->
    <div
        class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">

        <!-- heading -->
        <div
            class="mb-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">

            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Filter Rekap
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Filter data berdasarkan tanggal, tingkat, jurusan dan kelas.
                </p>

            </div>

            <div
                class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                Data realtime

            </div>

        </div>

        <!-- filter grid -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

            <!-- tanggal -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-gray-700">
                        Dari Tanggal
                    </label>

                    <input
                        type="date"
                        wire:model.live="filterTanggalDari"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-gray-700">
                        Sampai Tanggal
                    </label>

                    <input
                        type="date"
                        wire:model.live="filterTanggalSampai"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                </div>

            </div>

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
        </div>
    </div>

    <!-- CHART -->
    <div
        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm" wire:ignore>

        <!-- top -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Statistik Kehadiran
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Grafik kehadiran murid selama 1 tahun terakhir.
                </p>

            </div>

            <div class="flex items-center gap-3">

                <div
                    class="flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-600">

                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>

                    Hadir

                </div>

            </div>

        </div>

        <!-- chart -->
        <div class="p-6">

            <div
                id="chart-rekap-absen-murid"
                class="h-[380px] w-full">
            </div>

        </div>

    </div>

    <!-- EXPORT -->
    <div
        class=" rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- header -->
        <div class="border-b border-gray-200 px-6 py-5">

            <h2 class="text-xl font-bold text-gray-800">
                Export Laporan
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Unduh laporan absensi murid dalam berbagai format.
            </p>

        </div>

        <!-- content -->
        <div class="grid gap-6 p-6 lg:grid-cols-2">

            <!-- left -->
            <div class="space-y-5">

                <!-- tanggal export -->
                <div class="grid gap-4 sm:grid-cols-2">

                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Dari Tanggal
                        </label>

                        <input
                            type="date"
                            wire:model.live="exportTanggalDari"
                            class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm transition hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Sampai Tanggal
                        </label>

                        <input
                            type="date"
                            wire:model.live="exportTanggalSampai"
                            class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-sm transition hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                    </div>

                </div>

                <!-- Kelas -->
                <div x-data="{
                    open: false,
                    selectedId: null,
                    selectedLabel: 'Semua kelas',
                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false
                        $wire.set('exportRombelId', id)
                    }
                }" class="relative">

                    <label class="block text-sm font-medium text-gray-600 mb-2">
                        Pilih Kelas
                    </label>

                    <!-- Button -->
                    <div @click="open = !open"
                        class="flex items-center justify-between px-4 p-3 border border-gray-300 rounded-2xl cursor-pointer hover:border-blue-500 hover:bg-white transition">

                        <span x-text="selectedLabel" class="text-sm text-gray-700"></span>

                        <iconify-icon icon="lineicons:chevron-down" width="18" class="text-gray-400 transition"
                            :class="{ 'rotate-180': open }">
                        </iconify-icon>

                    </div>

                    <!-- Dropdown -->
                    <div x-show="open" x-transition @click.outside="open=false"
                        class="absolute mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-52 overflow-y-auto z-50">

                        <div @click="select(null, 'Semua kelas')"
                            class="flex items-center justify-between px-4 py-2.5 text-sm cursor-pointer hover:bg-blue-50 hover:text-blue-600 transition">

                            <span>Semua kelas</span>

                            <iconify-icon x-show="selectedId === null" icon="lineicons:check"
                                width="18">
                            </iconify-icon>

                        </div>

                        @foreach ($listRombel as $rombel)
                        <div @click="select({{ (int) $rombel->id }}, @js($rombel->nama_lengkap))"
                            class="flex items-center justify-between px-4 py-2.5 text-sm cursor-pointer hover:bg-blue-50 hover:text-blue-600 transition">

                            <span>{{ $rombel->nama_lengkap }}</span>

                            <iconify-icon x-show="selectedId === {{ (int) $rombel->id }}" icon="lineicons:check"
                                width="18">
                            </iconify-icon>

                        </div>
                        @endforeach

                    </div>

                </div>

            </div>

            <!-- right -->
            <div
                class="flex flex-col justify-center rounded-3xl bg-gradient-to-br from-blue-main to-blue-deep p-6 text-white">

                <h2 class="text-2xl font-bold">
                    Export Cepat
                </h2>

                <p class="mt-2 text-sm text-blue-100">
                    Download laporan kehadiran murid dengan format yang dibutuhkan.
                </p>

                <div class="mt-6">

                    <button
                        wire:click="exportExcel"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-semibold transition hover:bg-emerald-600 disabled:opacity-60">

                        <span wire:loading.remove>
                            Export Excel
                        </span>

                        <span wire:loading>
                            Export...
                        </span>

                    </button>

                </div>

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
                    Murid Tidak Hadir
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Daftar murid yang tidak hadir hari ini.
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

        <!-- table -->
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="px-5 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-5 py-4 text-left font-semibold">
                            Nama Murid
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Kelas
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($listAbsen as $index => $item)

                    @php
                    $statusValue = strtolower((string) $item->status);

                    $statusClass = match ($statusValue) {
                    'sakit' => 'text-amber-600 bg-amber-100',
                    'izin' => 'text-blue-600 bg-blue-100',
                    'alpa' => 'text-rose-600 bg-rose-100',
                    default => 'text-green-600 bg-green-100',
                    };
                    @endphp

                    <tr
                        class="border-t border-gray-100 transition hover:bg-gray-50">

                        <td
                            class="px-5 py-5 text-center font-medium text-gray-700">

                            {{ $index + 1 }}

                        </td>

                        <td class="px-5 py-5">

                            <div class="flex items-center gap-4">

                                <!-- avatar -->
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 font-bold uppercase text-blue-main">

                                    {{ substr($item->murid->nama, 0, 1) }}

                                </div>

                                <div>

                                    <h2
                                        class="font-semibold text-gray-800">

                                        {{ $item->murid->nama }}

                                    </h2>

                                    <p
                                        class="mt-1 text-xs text-gray-400">

                                        Murid aktif

                                    </p>

                                </div>

                            </div>

                        </td>

                        <td
                            class="px-5 py-5 text-center text-gray-600">

                            {{ $item->murid->rombel->nama_lengkap ?? '-' }}

                        </td>

                        <td class="px-5 py-5 text-center">

                            <div
                                class="{{ $statusClass }} inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold capitalize">

                                <div
                                    class="h-2 w-2 rounded-full bg-current">
                                </div>

                                {{ $item->status }}

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="4"
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

                                    Tidak ada data murid yang tersedia.

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

@script
<script type="module">
    import('{{ Vite::asset('Modules/Laporan/resources/assets/js/rekap-absen-murid-chart.js') }}')
    .then(({ initRekapAbsenMuridChart }) => {
        console.log(@js($chartRekap));
        initRekapAbsenMuridChart(@js($chartRekap));
    });
</script>
@endscript
