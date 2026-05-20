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
            <div
                x-data="{
                    open: false,
                    selectedId: @entangle('filterTingkat'),
                    selectedLabel: null,
                    options: @js($tingkatList->map(fn ($item) => [
                        'id' => $item->id,
                        'nama' => $item->nama,
                    ])),

                    toggle() {
                        this.open = !this.open
                    },

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false

                        $wire.set('filterTingkat', id)
                    },

                    syncLabel() {
                        const found = this.options.find((item) => item.id === this.selectedId)
                        this.selectedLabel = found ? found.nama : null
                    }
                }"
                x-init="syncLabel(); $watch('selectedId', () => syncLabel())"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tingkat
                </label>

                <!-- trigger -->
                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 transition hover:border-blue-main hover:bg-white">

                    <span
                        x-text="selectedLabel ?? 'Semua tingkat'"
                        class="text-sm text-gray-700">
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
                        @click.prevent="select(null, 'Semua tingkat')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                        <span>Semua tingkat</span>

                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>

                    @foreach ($tingkatList as $tingkat)

                        <div
                            @click.prevent="select({{ $tingkat['id'] }}, '{{ $tingkat['nama'] }}')"
                            class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                            <span>{{ $tingkat['nama'] }}</span>

                            <iconify-icon
                                x-show="selectedId == {{ $tingkat['id'] }}"
                                icon="lineicons:check"
                                width="18"
                                height="18">
                            </iconify-icon>

                        </div>

                    @endforeach

                </div>

            </div>

            <!-- jurusan -->
            <div
                x-data="{
                    open: false,
                    selectedId: @entangle('filterJurusan'),
                    selectedLabel: null,
                    options: @js($filteredJurusan->map(fn ($item) => [
                        'id' => $item->id,
                        'nama' => $item->nama,
                    ])),

                    toggle() {
                        this.open = !this.open
                    },

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false

                        $wire.set('filterJurusan', id)
                    },

                    syncLabel() {
                        const found = this.options.find((item) => item.id === this.selectedId)
                        this.selectedLabel = found ? found.nama : null
                    }
                }"
                x-init="syncLabel(); $watch('selectedId', () => syncLabel())"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Jurusan
                </label>

                <!-- trigger -->
                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 transition hover:border-blue-main hover:bg-white">

                    <span
                        x-text="selectedLabel ?? 'Semua jurusan'"
                        class="text-sm text-gray-700">
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
                        @click.prevent="select(null, 'Semua jurusan')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                        <span>Semua jurusan</span>

                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>

                    @foreach ($filteredJurusan as $jurusan)

                        <div
                            @click.prevent="select({{ $jurusan['id'] }}, '{{ $jurusan['nama'] }}')"
                            class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                            <span>{{ $jurusan['nama'] }}</span>

                            <iconify-icon
                                x-show="selectedId == {{ $jurusan['id'] }}"
                                icon="lineicons:check"
                                width="18"
                                height="18">
                            </iconify-icon>

                        </div>

                    @endforeach

                </div>

            </div>

            <!-- kelas -->
            <div
                x-data="{
                    open: false,
                    selectedId: @entangle('filterIndeks'),
                    selectedLabel: null,
                    options: @js($filteredIndeks->map(fn ($item) => [
                        'id' => $item->id,
                        'nama' => $item->nama,
                    ])),

                    toggle() {
                        this.open = !this.open
                    },

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false

                        $wire.set('filterIndeks', id)
                    },

                    syncLabel() {
                        const found = this.options.find((item) => item.id === this.selectedId)
                        this.selectedLabel = found ? found.nama : null
                    }
                }"
                x-init="syncLabel(); $watch('selectedId', () => syncLabel())"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Kelas
                </label>

                <!-- trigger -->
                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 transition hover:border-blue-main hover:bg-white">

                    <span
                        x-text="selectedLabel ?? 'Semua kelas'"
                        class="text-sm text-gray-700">
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
                        @click.prevent="select(null, 'Semua kelas')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                        <span>Semua kelas</span>

                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>

                    @foreach ($filteredIndeks as $kelas)

                        <div
                            @click.prevent="select({{ $kelas['id'] }}, '{{ $kelas['nama'] }}')"
                            class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                            <span>{{ $kelas['nama'] }}</span>

                            <iconify-icon
                                x-show="selectedId == {{ $kelas['id'] }}"
                                icon="lineicons:check"
                                width="18"
                                height="18">
                            </iconify-icon>

                        </div>

                    @endforeach

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

        <!-- table -->
        <div class="overflow-x-auto">

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

                        <tr
                            class="border-t border-gray-100 transition hover:bg-gray-50">

                            <!-- no -->
                            <td
                                class="px-5 py-5 text-center font-medium text-gray-700">

                                {{ $loop->iteration }}

                            </td>

                            <!-- nipd -->
                            <td
                                class="px-5 py-5 text-center text-gray-600">

                                {{ $item->nipd }}

                            </td>

                            <!-- murid -->
                            <td class="px-5 py-5">

                                <div class="flex items-center gap-4">

                                    <!-- avatar -->
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 font-bold uppercase text-blue-main">

                                        {{ substr($item->nama, 0, 1) }}

                                    </div>

                                    <!-- name -->
                                    <div>

                                        <h2
                                            class="font-semibold capitalize text-gray-800">

                                            {{ $item->nama }}

                                        </h2>

                                        <p
                                            class="mt-1 text-xs text-gray-400">

                                            Data murid aktif

                                        </p>

                                    </div>

                                </div>

                            </td>

                            <!-- nisn -->
                            <td
                                class="px-5 py-5 text-center text-gray-600">

                                {{ $item->nisn }}

                            </td>

                            <!-- action -->
                            <td class="px-5 py-5">

                                <div
                                    class="flex items-center justify-center">

                                    <a
                                        href="{{ route('riwayat-detail-absen-murid', $item->ulid) }}"
                                        wire:navigate>

                                        <button
                                            class="group flex items-center gap-2 rounded-2xl bg-blue-main px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-deep-solid">

                                            <iconify-icon
                                                icon="mdi:eye"
                                                width="18"
                                                height="18"
                                                class="transition group-hover:scale-110">
                                            </iconify-icon>

                                            Detail

                                        </button>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400">
                                Data murid belum tersedia.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div
            class="border-t border-gray-200 bg-gray-50 px-6 py-4">

            {{ $listMurid->links('livewire.components.pagination') }}

        </div>

    </div>

</div>