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
                        Manajemen Murid
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
    <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">

        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Filter Data
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Filter berdasarkan tingkat, jurusan, dan kelas.
                </p>
            </div>

            <div class="relative w-full lg:w-80">

                <input
                    type="search"
                    name="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari nama atau NIPD..."
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 pl-4 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

                <div class="absolute inset-y-0 right-4 flex items-center text-gray-400">

                    <iconify-icon
                        icon="mdi:account-search-outline"
                        width="22"
                        height="22">
                    </iconify-icon>

                </div>

            </div>

        </div>

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

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    style="display:none"
                    class="absolute mt-2 h-40 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua tingkat')"
                        class="flex cursor-pointer items-center justify-between px-4 py-2 transition hover:bg-blue-deep-solid hover:text-white">
                        <span>Semua tingkat</span>
                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="24"
                            height="24">
                        </iconify-icon>
                    </div>

                    @foreach ($listRombel->pluck('tingkat')->filter()->sortBy('nama')->unique('id') as $tingkat)
                        <div
                            @click.prevent="select({{ (int) $tingkat->id }}, @js($tingkat->nama))"
                            class="flex cursor-pointer items-center justify-between px-4 py-2 transition hover:bg-blue-deep-solid hover:text-white">
                            <span>{{ $tingkat->nama }}</span>
                            <iconify-icon
                                x-show="selectedId == {{ (int) $tingkat->id }}"
                                icon="lineicons:check"
                                width="24"
                                height="24">
                            </iconify-icon>
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

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    style="display:none"
                    class="absolute mt-2 h-40 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua jurusan')"
                        class="flex cursor-pointer items-center justify-between px-4 py-2 transition hover:bg-blue-deep-solid hover:text-white">
                        <span>Semua jurusan</span>
                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="24"
                            height="24">
                        </iconify-icon>
                    </div>

                    @foreach ($filteredJurusan as $jurusan)
                        <div
                            @click.prevent="select({{ (int) $jurusan->id }}, @js($jurusan->nama))"
                            class="flex cursor-pointer items-center justify-between px-4 py-2 transition hover:bg-blue-deep-solid hover:text-white">
                            <span>{{ $jurusan->nama }}</span>
                            <iconify-icon
                                x-show="selectedId == {{ (int) $jurusan->id }}"
                                icon="lineicons:check"
                                width="24"
                                height="24">
                            </iconify-icon>
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

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    style="display:none"
                    class="absolute mt-2 h-40 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua kelas')"
                        class="flex cursor-pointer items-center justify-between px-4 py-2 transition hover:bg-blue-deep-solid hover:text-white">
                        <span>Semua kelas</span>
                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="24"
                            height="24">
                        </iconify-icon>
                    </div>

                    @foreach ($filteredIndeks as $indeks)
                        <div
                            @click.prevent="select({{ (int) $indeks->id }}, @js($indeks->nama))"
                            class="flex cursor-pointer items-center justify-between px-4 py-2 transition hover:bg-blue-deep-solid hover:text-white">
                            <span>{{ $indeks->nama }}</span>
                            <iconify-icon
                                x-show="selectedId == {{ (int) $indeks->id }}"
                                icon="lineicons:check"
                                width="24"
                                height="24">
                            </iconify-icon>
                        </div>
                    @endforeach

                </div>

            </div>

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

        <!-- table -->
        <div class="overflow-x-auto">

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

                        <tr class="border-t border-gray-100 transition hover:bg-gray-50">

                            <!-- no -->
                            <td class="px-5 py-5 text-center font-medium text-gray-700">
                                {{ $loop->iteration }}
                            </td>

                            <!-- foto -->
                            <td class="px-5 py-5">

                                <div class="flex justify-center">

                                    <div
                                        class="relative h-16 w-16 overflow-hidden rounded-2xl border border-gray-200 bg-gray-100 shadow-sm">

                                        <img
                                            src="{{ $murid->image_path ?: asset('assets/img/default-avatar.png') }}"
                                            alt="Foto {{ $murid->nama }}"
                                            class="h-full w-full object-cover">

                                    </div>

                                </div>

                            </td>

                            <!-- nama -->
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

                            <!-- nipd -->
                            <td class="px-5 py-5 text-center">

                                <div
                                    class="inline-flex rounded-full bg-blue-50 px-4 py-1.5 text-xs font-semibold text-blue-main">

                                    {{ $murid->nipd }}

                                </div>

                            </td>

                            <!-- aksi -->
                            <td class="px-5 py-5">

                                <div class="flex items-center justify-center gap-2">

                                    <!-- edit foto -->
                                    <a href="{{ route('edit-foto', ['murid' => $murid->ulid]) }}" wire:navigate>
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

                                    <div x-data>
                                        <button
                                            @click="$dispatch('open-delete-modal')"
                                            type="button"
                                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-rose-600">

                                            <iconify-icon
                                                icon="lineicons:trash-3"
                                                width="20"
                                                height="20">
                                            </iconify-icon>

                                        </button>

                                        <livewire:components.modal.manajemen.murid.modal-hapus-foto />
                                    </div>
                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr class="border-t border-gray-100">
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">
                                Belum ada data murid untuk ditampilkan.
                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>

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

</div>