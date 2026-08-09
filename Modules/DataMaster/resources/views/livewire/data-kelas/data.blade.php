<div
    x-data="{
        selected: [],

        getPageIds() {
            return Array.from(
                this.$root.querySelectorAll('.kelas-row-checkbox')
            ).map(el => el.value)
        },

        toggleAll(e) {
            if (e.target.checked) {
                this.selected = this.getPageIds()
            } else {
                this.selected = []
            }
        }
    }"
    class="space-y-6">

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep  p-6 shadow-lg">

        <!-- effect -->
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-white/5"></div>

        <div
            class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:buildings-2-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-white">
                        Data Kelas
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Kelola data kelas, tingkat, jurusan, dan rombel sekolah.
                    </p>

                </div>

            </div>

            <!-- actions -->
            <div class="sm:flex grid grid-cols-1 gap-3 sm:flex-row">

                <!-- tambah -->
                <a href="{{ route('tambah-kelas') }}" wire:navigate>

                    <button
                        class="w-full group flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-blue-main shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">

                        <iconify-icon
                            icon="line-md:plus"
                            width="22"
                            height="22"
                            class="transition duration-300 group-hover:rotate-90">
                        </iconify-icon>

                        Tambah Kelas

                    </button>

                </a>

                <!-- import -->
                {{-- <div x-data="{ openModalImport: false }">

                    <button
                        @click="openModalImport = true"
                        class="w-full flex items-center justify-center gap-2 rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">

                        <iconify-icon
                            icon="line-md:file-import"
                            width="22"
                            height="22">
                        </iconify-icon>

                        Import CSV

                    </button>

                </div> --}}

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
                :options="$listTingkat"
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

    <!-- TABLE -->
    <div
        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- top -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Daftar Kelas
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Data seluruh rombel yang tersedia pada sistem.
                </p>

            </div>

            <!-- action -->
            <div class="flex items-center gap-3">

                <!-- menu -->
                <div
                    class="relative"
                    x-data="{ openModalColon: false }">

                    <button
                        @click="openModalColon = !openModalColon"
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-100 text-gray-600 transition hover:bg-blue-main hover:text-white">

                        <iconify-icon
                            icon="lineicons:menu-meatballs-1"
                            width="20"
                            height="20">
                        </iconify-icon>

                    </button>

                </div>

                <!-- selected -->
                <div
                    class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                    <span x-text="selected.length"></span>
                    dipilih

                </div>

            </div>

        </div>

        <!-- table -->
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="px-5 py-4 text-center">
                            <input type="checkbox" @click="toggleAll">
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Tingkat
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Jurusan
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Kelas
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($listRombel as $index => $rombel)

                    <tr
                        class="border-t border-gray-100 transition hover:bg-gray-50">

                        <!-- checkbox -->
                        <td class="px-5 py-5 text-center">

                            <input
                                type="checkbox"
                                class="kelas-row-checkbox rounded border-gray-300"
                                value="{{ $rombel->id }}"
                                x-model="selected">

                        </td>

                        <!-- no -->
                        <td class="px-5 py-5 text-center font-medium text-gray-700">
                            {{ $index + 1 }}
                        </td>

                        <!-- tingkat -->
                        <td class="px-5 py-5 text-center">

                            <div
                                class="inline-flex rounded-full bg-blue-100 px-4 py-1 text-xs font-semibold text-blue-600">

                                {{ $rombel->tingkat?->nama ?? '-' }}

                            </div>

                        </td>

                        <!-- jurusan -->
                        <td class="px-5 py-5 text-center">

                            <div
                                class="inline-flex rounded-full bg-emerald-100 px-4 py-1 text-xs font-semibold text-emerald-600">

                                {{ $rombel->jurusan?->nama ?? '-' }}

                            </div>

                        </td>

                        <!-- kelas -->
                        <td class="px-5 py-5 text-center">

                            <div
                                class="inline-flex rounded-full bg-amber-100 px-4 py-1 text-xs font-semibold uppercase text-amber-600">

                                {{ $rombel->indeks?->nama ?? '-' }}

                            </div>

                        </td>

                        <!-- aksi -->
                        <td class="px-5 py-5">

                            <div
                                class="flex items-center justify-center gap-2">

                                <!-- edit -->
                                <a
                                    href="{{ route('edit-kelas', $rombel->id) }}"
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

                                <!-- delete -->
                                <button
                                    type="button"
                                    @click="$dispatch('open-delete-kelas', { id: {{ (int) $rombel->id }} })"
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

                    <tr>

                        <td
                            colspan="6"
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

                                    Data kelas kosong

                                </h2>

                                <p
                                    class="mt-1 text-sm text-gray-500">

                                    Belum ada data kelas yang tersedia.

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

            {{ $listRombel->links('livewire.components.pagination') }}

        </div>

    </div>

    <!-- modal -->
    <livewire:datamaster.data-kelas.components.modal.hapus />

</div>