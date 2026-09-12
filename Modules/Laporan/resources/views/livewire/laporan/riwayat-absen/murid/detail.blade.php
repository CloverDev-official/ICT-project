<div class="space-y-5">

    <!-- BACK BUTTON -->
    <div>
        <button
            onclick="history.back()"
            class="flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-main hover:text-blue-main hover:shadow-md">

            <iconify-icon
                icon="solar:arrow-left-linear"
                width="20"
                height="20">
            </iconify-icon>

            Kembali

        </button>
    </div>

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-sm">

        <!-- ornament -->
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-white/5"></div>

        <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

            <!-- profile -->
            <div class="flex items-center gap-4">

                <x-murid-avatar :murid="$murid" class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 text-2xl font-bold text-white backdrop-blur" />

                <div>
                    <h1 class="text-2xl font-semibold capitalize text-white">
                        {{ $murid->nama }}
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Riwayat Kehadiran Siswa
                    </p>
                </div>

            </div>

            <!-- Tanggal -->
            <div
                class="w-full md:w-auto rounded-2xl border border-white/20 bg-white/10 px-5 py-3 backdrop-blur">

                <p class="text-xs uppercase tracking-widest text-blue-100">
                    Tanggal
                </p>

                <h2 class="mt-1 text-sm md:text-base font-semibold text-white capitalize">
                    {{ $tanggalRangeLabel }}
                </h2>

            </div>

        </div>
    </div>

    <!-- filter -->
    <div class="bg-white rounded-2xl shadow-sm p-4 ">
        <div>
            <h1 class="font-semibold text-gray-800 text-xl capitalize">kategori</h1>
            <p class="text-sm text-gray-400">
                Filter detail absen murid berdasarkan dari tanggal berapa sampai ke tanggal.
            </p>
        </div>

        <div class="mt-5 pt-4 border-t border-gray-200 grid grid-cols-2 gap-5">
            <!-- TANGGAL dari -->
            <div class="relative w-full">

                <label class="text-sm text-gray-600 capitalize font-semibold">dari tanggal</label>

                <input type="date" wire:model.live="filterTanggalDari"
                    class="w-full text-sm mt-1 px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl
                    hover:border-blue-500
                    focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none
                    transition" />
            </div>
            <!-- tanggal sampai -->
            <div class="relative w-full">

                <label class="text-sm text-gray-600 capitalize font-semibold">sampai tanggal</label>

                <input type="date" wire:model.live="filterTanggalSampai"
                    class="w-full text-sm mt-1 px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl
                    hover:border-blue-500
                    focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none
                    transition" />
            </div>
        </div>
    </div>
    <!-- TABLE -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- top section -->
        <div
            class="flex flex-col gap-3 border-b border-gray-200 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Data Absensi
                </h2>

                <p class="text-sm text-gray-500">
                    Detail kehadiran selama tanggal yang dipilih
                </p>
            </div>
            
        </div>

        <!-- table -->
        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Nama
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Tanggal
                        </th>

                        <th class="px-6 py-4 text-center font-semibold">
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($listAbsensi as $item)

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

                    <tr class="border-t border-gray-100 transition hover:bg-gray-50">

                        <td class="px-6 py-4 text-center font-medium text-gray-700">
                            {{ $loop->iteration }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <x-murid-avatar :murid="$murid" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-sm font-semibold text-blue-main" />

                                <div>
                                    <p class="font-medium capitalize text-gray-800">
                                        {{ $murid->nama }}
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        Siswa aktif
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-gray-600 capitalize">
                            {{ $item->tanggal?->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">

                            <div class="flex items-center justify-center">

                                <div
                                    class="{{ $statusClass }} flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold capitalize">

                                    <div class="h-2 w-2 rounded-full bg-current"></div>

                                    {{ $item->status }}

                                </div>

                            </div>

                        </td>

                    </tr>

                    @empty
                        <tr class="border-t border-gray-100">
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">
                                Tidak ada data absensi pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

    </div>

</div>