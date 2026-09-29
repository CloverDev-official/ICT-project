<div
    x-data="{
        selected: [],

        getPageIds() {
            return Array.from(
                this.$root.querySelectorAll('.jurusan-row-checkbox')
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
    class="space-y-6"
>

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-lg">

        <!-- effect -->
        <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-white/4"></div>

        <div
            class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:library-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-white">
                        Data Jurusan
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Kelola seluruh data jurusan sekolah dengan mudah.
                    </p>

                </div>

            </div>

            <!-- actions -->
            <div class="sm:flex grid grid-cols-1  gap-3 sm:flex-row">

                <!-- tambah -->
                <a href="{{ route('tambah-jurusan') }}" wire:navigate>

                    <button
                        class="w-full group flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-blue-main shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">

                        <iconify-icon
                            icon="line-md:plus"
                            width="22"
                            height="22"
                            class="transition duration-300 group-hover:rotate-90">
                        </iconify-icon>

                        Tambah Jurusan

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

    <!-- TABLE -->
    <div
        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- top -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- left -->
            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Daftar Jurusan
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Data seluruh jurusan yang tersedia pada sistem sekolah.
                </p>

            </div>

            <!-- right -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                <!-- selected -->
                <div
                    class="flex items-center gap-3">

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

                        <livewire:components.modal.modal-colon />

                    </div>

                    <!-- selected count -->
                    <div
                        class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                        <span x-text="selected.length"></span>
                        dipilih

                    </div>

                </div>

                <!-- search -->
                <div class="relative w-full sm:w-72">

                    <input
                        type="search"
                        name="search"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Cari nama jurusan..."
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

        </div>

        {{-- DESKTOP TABLE --}}
        <div class="hidden overflow-x-auto lg:block">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="w-5 px-5 py-4 text-center">
                            <input
                                type="checkbox"
                                @click="toggleAll">
                        </th>

                        <th class="w-10 px-5 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Nama Jurusan
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($listJurusan as $index => $jurusan)

                        <tr
                            class="border-t border-gray-100 transition hover:bg-gray-50">

                            {{-- checkbox --}}
                            <td class="px-5 py-5 text-center">

                                <input
                                    type="checkbox"
                                    class="jurusan-row-checkbox rounded border-gray-300"
                                    value="{{ $jurusan->id }}"
                                    x-model="selected">

                            </td>

                            {{-- nomor --}}
                            <td class="px-5 py-5 text-center font-medium text-gray-700">

                                {{ $index + 1 }}

                            </td>

                            {{-- jurusan --}}
                            <td class="px-5 py-5">

                                <div class="flex items-center justify-center gap-4">

                                    {{-- icon --}}
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">

                                        <iconify-icon
                                            icon="solar:book-bold"
                                            width="22"
                                            height="22">
                                        </iconify-icon>

                                    </div>

                                    {{-- text --}}
                                    <div>

                                        <h2 class="font-semibold text-gray-800">
                                            {{ $jurusan->nama }}
                                        </h2>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Data jurusan aktif
                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- aksi --}}
                            <td class="px-5 py-5">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- edit --}}
                                    <a
                                        href="{{ route('edit-jurusan', $jurusan->id) }}"
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

                                    {{-- delete --}}
                                    <button
                                        type="button"
                                        @click="$dispatch('open-delete-jurusan', { id: {{ (int) $jurusan->id }} })"
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
                                colspan="4"
                                class="px-6 py-14 text-center">

                                <div class="flex flex-col items-center justify-center">

                                    <div
                                        class="mb-4 flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-100 text-gray-400">

                                        <iconify-icon
                                            icon="solar:box-bold"
                                            width="38"
                                            height="38">
                                        </iconify-icon>

                                    </div>

                                    <h2 class="text-lg font-bold text-gray-700">
                                        Data jurusan kosong
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Belum ada data jurusan yang tersedia.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- MOBILE + TABLET COLLAPSIBLE --}}
        <div class="space-y-3 lg:hidden">

            @forelse ($listJurusan as $index => $jurusan)

                <div
                    x-data="{ open: false }"
                    class="overflow-hidden border border-t-0 border-gray-200 bg-white">

                    {{-- SUMMARY --}}
                    <div class="flex items-center gap-3 p-4">

                        {{-- checkbox --}}
                        <input
                            type="checkbox"
                            class="jurusan-row-checkbox shrink-0 rounded border-gray-300"
                            value="{{ $jurusan->id }}"
                            x-model="selected">

                        {{-- number --}}
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-sm font-semibold text-gray-600">

                            {{ $index + 1 }}

                        </div>

                        {{-- icon --}}
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-main">

                            <iconify-icon
                                icon="solar:book-bold"
                                width="22"
                                height="22">
                            </iconify-icon>

                        </div>

                        {{-- information --}}
                        <div class="min-w-0 flex-1">

                            <h2 class="truncate font-semibold text-gray-800">
                                {{ $jurusan->nama }}
                            </h2>

                            <p class="mt-1 text-xs text-gray-400">
                                Data jurusan aktif
                            </p>

                        </div>

                        {{-- expand --}}
                        <button
                            type="button"
                            @click="open = !open"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-gray-500 transition hover:bg-gray-100 hover:text-gray-700"
                            :aria-expanded="open">

                            <iconify-icon
                                icon="lineicons:chevron-down"
                                width="20"
                                height="20"
                                class="transition-transform duration-200"
                                :class="{ 'rotate-180': open }">
                            </iconify-icon>

                        </button>

                    </div>

                    {{-- COLLAPSED DETAIL --}}
                    <div
                        x-show="open"
                        x-collapse
                        class="border-t border-gray-200 bg-gray-50">

                        <div class="p-5">

                            <div>

                                <p class="text-xs font-medium text-gray-400">
                                    Nama Jurusan
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-700">
                                    {{ $jurusan->nama }}
                                </p>

                            </div>

                        </div>

                        {{-- ACTIONS --}}
                        <div class="flex items-center gap-2 border-t border-gray-200 p-4">

                            {{-- edit --}}
                            <a
                                href="{{ route('edit-jurusan', $jurusan->id) }}"
                                wire:navigate
                                class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-amber-400 px-4 py-3 text-sm font-semibold text-white transition hover:bg-amber-500">

                                <iconify-icon
                                    icon="lineicons:pencil-1"
                                    width="18"
                                    height="18">
                                </iconify-icon>

                                Edit

                            </a>

                            {{-- delete --}}
                            <button
                                type="button"
                                @click="$dispatch('open-delete-jurusan', { id: {{ (int) $jurusan->id }} })"
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white transition hover:bg-rose-600">

                                <iconify-icon
                                    icon="lineicons:trash-3"
                                    width="20"
                                    height="20">
                                </iconify-icon>

                            </button>

                        </div>

                    </div>

                </div>

            @empty

                {{-- empty state --}}
                <div class="px-6 py-14 text-center">

                    <div class="flex flex-col items-center justify-center">

                        <div
                            class="mb-4 flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-100 text-gray-400">

                            <iconify-icon
                                icon="solar:box-bold"
                                width="38"
                                height="38">
                            </iconify-icon>

                        </div>

                        <h2 class="text-lg font-bold text-gray-700">
                            Data jurusan kosong
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Belum ada data jurusan yang tersedia.
                        </p>

                    </div>

                </div>

            @endforelse

        </div>

        <!-- pagination -->
        <div
            class="border-t border-gray-200 bg-gray-50 px-6 py-4">

            {{ $listJurusan->links('livewire.components.pagination') }}

        </div>

    </div>

    <!-- modal -->
    <livewire:datamaster.data-jurusan.components.modal.hapus />

</div>
