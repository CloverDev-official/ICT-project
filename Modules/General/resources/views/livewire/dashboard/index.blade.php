<div class="space-y-6">

    <!-- HERO -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-lg">

        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-5">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:home-smile-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Dashboard Absensi
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Ringkasan data sekolah dan kehadiran hari ini.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- STAT CARDS -->
    @unless($isWaliKelas)
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

        @foreach ($statCards as $stat)

            <div
                class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-gray-100 blur-2xl"></div>

                <div class="relative flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-400">
                            {{ $stat['label'] }}
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-gray-800">
                            {{ is_numeric($stat['value']) ? number_format($stat['value']) : $stat['value'] }}
                        </h2>
                    </div>

                    <div class="{{ $stat['color'] }} flex h-14 w-14 items-center justify-center rounded-2xl">
                        <iconify-icon icon="{{ $stat['icon'] }}" width="28" height="28"></iconify-icon>
                    </div>

                </div>

            </div>

        @endforeach

    </div>
    @endunless

    <!-- ABSENSI HARI INI -->
    <div class="grid grid-cols-1 gap-6 {{ $isWaliKelas ? '' : 'xl:grid-cols-2' }}">

        <!-- MURID -->
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm col-span-2">

            <div
                class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        Absensi Murid Hari Ini
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Pantau kehadiran murid berdasarkan kelas.
                    </p>
                </div>

                <!-- filter kelas -->
                @php
                    $selectedRombel = collect($waliRombelList)->firstWhere('id', $selectedRombelId);
                @endphp
                <div
                    x-data="{ open: false }"
                    class="relative w-full lg:w-48">

                    <div
                        @click="open = !open"
                        class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                        <span
                            x-text="@js($selectedRombel?->nama_lengkap ?? 'Semua Kelas')"
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

                    <div
                        x-show="open"
                        x-transition
                        @click.outside="open = false"
                        style="display:none"
                        wire:ignore
                        class="absolute z-50 mt-2 max-h-52 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin">

                        @unless($isWaliKelas)
                            <div
                                @click="open = false; $wire.setRombelFilter(null)"
                                class="flex cursor-pointer items-center justify-between px-4 py-3 text-sm transition hover:bg-blue-main hover:text-white {{ $selectedRombelId === null ? 'bg-blue-main text-white' : '' }}">

                                <span>Semua Kelas</span>

                                <iconify-icon
                                    x-show="{{ $selectedRombelId === null ? 'true' : 'false' }}"
                                    icon="lineicons:check"
                                    width="18">
                                </iconify-icon>

                            </div>
                        @endunless

                        @forelse ($waliRombelList as $rombel)

                            <div
                                @click="open = false; $wire.setRombelFilter({{ (int) $rombel->id }})"
                                class="flex cursor-pointer items-center justify-between px-4 py-3 text-sm transition hover:bg-blue-main hover:text-white {{ $selectedRombelId === (int) $rombel->id ? 'bg-blue-main text-white' : '' }}">

                                <span>{{ $rombel->nama_lengkap }}</span>

                                <iconify-icon
                                    x-show="{{ $selectedRombelId === (int) $rombel->id ? 'true' : 'false' }}"
                                    icon="lineicons:check"
                                    width="18">
                                </iconify-icon>

                            </div>

                        @empty
                            <div class="px-4 py-3 text-sm text-gray-500">
                                Tidak ada kelas yang tersedia.
                            </div>
                        @endforelse

                    </div>

                </div>

            </div>

            <div class="p-6">
                <div id="main-murid" wire:ignore class="h-64 w-full"></div>
            </div>

        </div>

        {{-- @unless($isWaliKelas)
        <!-- GURU -->
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-200 px-6 py-5">

                <h2 class="text-xl font-bold text-gray-800">
                    Absensi Guru Hari Ini
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Pantau kehadiran guru hari ini.
                </p>

            </div>

            <div class="p-6">
                <div id="main-guru" wire:ignore class="h-64 w-full"></div>
            </div>

        </div>
        @endunless --}}

    </div>

    <!-- TINGKAT KEHADIRAN -->
    @unless($isWaliKelas)
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        <!-- MURID -->
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm col-span-2">

            <div
                class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5">

                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        Tingkat Kehadiran Murid
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Statistik kehadiran 7 hari terakhir.
                    </p>
                </div>

                <div
                    class="rounded-full bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-main">
                    7 Hari
                </div>

            </div>

            <div class="p-6">
                <div id="chart-tingkat-kehadiran-murid" wire:ignore class="h-72 w-full"></div>

                <a
                    wire:navigate
                    href="{{ route('data-murid') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main transition hover:bg-blue-main hover:text-white">

                    Lihat Data

                    <iconify-icon
                        icon="solar:arrow-right-linear"
                        width="18"
                        height="18">
                    </iconify-icon>

                </a>
            </div>

        </div>

        <!-- GURU -->
        {{-- <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

            <div
                class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5">

                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        Tingkat Kehadiran Guru
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Statistik kehadiran 7 hari terakhir.
                    </p>
                </div>

                <div
                    class="rounded-full bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-600">
                    7 Hari
                </div>

            </div>

            <div class="p-6">
                <div id="chart-tingkat-kehadiran-guru" wire:ignore class="h-72 w-full"></div>

                <a
                    wire:navigate
                    href="{{ route('data-guru') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-600 transition hover:bg-emerald-600 hover:text-white">

                    Lihat Data

                    <iconify-icon
                        icon="solar:arrow-right-linear"
                        width="18"
                        height="18">
                    </iconify-icon>

                </a>
            </div>

        </div> --}}

    </div>
    @endunless

</div>

@script
    <script type="module">
        import('{{ Vite::asset('Modules/General/resources/assets/js/dashboard-charts.js') }}')
        .then(() => {
            const { initDashboardCharts } = window;

            if (typeof initDashboardCharts !== 'function') {
                throw new Error('Dashboard chart initializer gagal dimuat.');
            }

            const dashboardData = @js($dashboardData);

            document.addEventListener('livewire:init', () => {
                Livewire.on('dashboard-data-updated', ({ dashboardData: updatedDashboardData }) => {
                    initDashboardCharts(updatedDashboardData);
                });
            });

            initDashboardCharts(dashboardData);
        });
    </script>
@endscript
