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
                        Daftar dan laporan izin keluar murid secara realtime.
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
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

            <!-- tingkat -->
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

                        $wire.set('filterTingkat', id)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tingkat
                </label>

                <!-- toggle -->
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
                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua tingkat')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>Semua tingkat</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>

                    @foreach ($listRombel->pluck('tingkat')->filter()->sortBy('nama')->unique('id') as $tingkat)
                    <div
                        @click.prevent="select({{ (int) $tingkat->id }}, @js($tingkat->nama))"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>{{ $tingkat->nama }}</span>
                        <iconify-icon x-show="selectedId == {{ (int) $tingkat->id }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>
                    @endforeach

                </div>

            </div>

            <!-- jurusan -->
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

                        $wire.set('filterJurusan', id)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Jurusan
                </label>

                <!-- toggle -->
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
                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua jurusan')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>Semua jurusan</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>

                    @foreach ($this->filteredJurusan as $jurusan)
                    <div
                        @click.prevent="select({{ (int) $jurusan->id }}, @js($jurusan->nama))"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>{{ $jurusan->nama }}</span>
                        <iconify-icon x-show="selectedId == {{ (int) $jurusan->id }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>
                    @endforeach

                </div>
            </div>

            <!-- kelas -->
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

                        $wire.set('filterIndeks', id)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Kelas
                </label>

                <!-- toggle -->
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
                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua Kelas')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>Semua Kelas</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>

                    @foreach ($filteredIndeks as $indeks)
                    <div
                        @click.prevent="select({{ (int) $indeks->id }}, @js($indeks->nama))"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>{{ $indeks->nama }}</span>
                        <iconify-icon x-show="selectedId == {{ (int) $indeks->id }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
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

            <!-- left -->
            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Daftar Izin Keluar
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Data seluruh murid yang izin keluar.
                </p>

            </div>

        </div>

        <!-- table -->
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="px-5 py-4 text-center font-semibold w-10">
                            No
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Nama
                        </th>
                        
                        <th class="px-5 py-4 text-center font-semibold">
                            Kelas
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Nipd
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Keperluan
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            tanggal
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            waktu
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Status
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($dataIzin as $izin)

                    <tr class="border-t border-gray-100 transition hover:bg-gray-50">

                        <!-- nomor -->
                        <td class="px-5 py-5 text-center font-medium text-gray-700">
                            {{ $dataIzin->firstItem() + $loop->index }}
                        </td>

                        <!-- nama -->
                        <td class="px-5 py-5">

                            <h2 class="font-semibold text-center text-gray-800 capitalize">
                                {{ $izin->murid->nama ?? '-' }}
                            </h2>

                        </td>

                        <!-- kelas -->
                        <td class="px-5 py-5 text-center text-gray-700">
                            {{ $izin->murid->rombel->nama_lengkap ?? 'N/A' }}
                        </td>

                        <!-- nipd -->
                        <td class="px-5 py-5 text-center text-gray-700">
                            {{ $izin->murid->nipd ?? '-' }}
                        </td>

                        <!-- keperluan -->
                        <td class="px-5 py-5 text-center text-gray-700">
                            {{ $izin->alasan }}
                        </td>

                        <!--tanggal -->
                        <td class="px-5 py-5 text-center text-gray-700">
                            {{ optional($izin->tanggal)->format('d - m - Y') ?? '-' }}
                        </td>

                        <!-- waktu -->
                        <td class="px-5 py-5 text-center text-gray-700">
                            {{ $izin->dari_jam }}{{ $izin->sampai_jam ? ' - ' . $izin->sampai_jam : '' }}
                        </td>

                        <!-- status -->
                        <td class="px-5 py-5 text-center text-gray-700">
                            {{ $izin->status ?? '-' }}
                        </td>

                        <!-- aksi button -->
                        <td class="px-5 py-5 text-center text-gray-700">
                            <div class="flex items-center justify-center gap-2">
                                <!-- edit -->
                                <a
                                    href="{{ route('edit-izin', $izin->id) }}"
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

                                <!-- cetak ulang izin -->
                                <a href="{{ route('surat-izin', $izin->id) }}" wire:navigate>
                                    <button 
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-main text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-deep-solid">

                                    <iconify-icon
                                        icon="solar:printer-bold"
                                        width="20"
                                        height="20">
                                    </iconify-icon>

                                    </button>
                                </a>

                                <!-- delete -->
                                <div
                                    x-data="{ openModalDelete: false }"
                                >
                                    <button
                                        type="button"
                                        @click="$dispatch('open-delete-izin', { id: {{ $izin->id }} })"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-rose-600">

                                        <iconify-icon
                                            icon="lineicons:trash-3"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                        
                                    </button>
                                </div>
                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="10"
                            class="px-6 py-14 text-center">

                            <div
                                class="flex flex-col items-center justify-center">

                                <div
                                    class="mb-4 flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-100 text-gray-400">

                                    <iconify-icon
                                        icon="solar:user-cross-bold"
                                        width="38"
                                        height="38">
                                    </iconify-icon>

                                </div>

                                <h2
                                    class="text-lg font-bold text-gray-700">

                                    Data izin kosong

                                </h2>

                                <p
                                    class="mt-1 text-sm text-gray-500">

                                    Belum ada data izin yang tersedia.

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
            {{ $dataIzin->links() }}
        </div>

        <livewire:components.modal.pengawas.laporan.modal-hapus-izin />

    </div>
</div>