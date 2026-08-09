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
                    query: '',
                    selectedId: null,
                    selectedLabel: null,

                    toggle() {
                        this.open = !this.open
                    },

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.query = label
                        this.open = false

                        $wire.set('filterTingkat', id)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tingkat
                </label>

                <div class="relative">
                    <input
                        x-model="query"
                        @focus="open = true; query = ''"
                        @input="open = true"
                        @keydown.escape="open = false"
                        type="text"
                        autocomplete="off"
                        placeholder="Cari tingkat..."
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-11 text-sm text-gray-700 transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100">

                    <iconify-icon
                        icon="mdi:magnify"
                        width="20"
                        height="20"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                    </iconify-icon>
                </div>

                <!-- dropdown -->
                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full max-h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua tingkat')"
                        x-show="!query || 'semua tingkat'.includes(query.toLowerCase())"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>Semua tingkat</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>

                    @foreach ($listRombel->pluck('tingkat')->filter()->sortBy('nama')->unique('id') as $tingkat)
                    <div
                        @click.prevent="select({{ (int) $tingkat->id }}, @js($tingkat->nama))"
                        x-show="!query || @js(strtolower($tingkat->nama)).includes(query.toLowerCase())"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>{{ $tingkat->nama }}</span>
                        <iconify-icon x-show="selectedId == {{ (int) $tingkat->id }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>
                    @endforeach

                    <p
                        x-show="query && !(@js($listRombel->pluck('tingkat')->filter()->sortBy('nama')->unique('id')->pluck('nama')->prepend('Semua tingkat')->values())).some((item) => item.toLowerCase().includes(query.toLowerCase()))"
                        class="px-4 py-3 text-sm text-gray-500">
                        Tingkat tidak ditemukan.
                    </p>

                </div>

            </div>

            <!-- jurusan -->
            <div
                x-data="{
                    open: false,
                    query: '',
                    selectedId: null,
                    selectedLabel: null,

                    toggle() {
                        this.open = !this.open
                    },

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.query = label
                        this.open = false

                        $wire.set('filterJurusan', id)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Jurusan
                </label>

                <div class="relative">
                    <input
                        x-model="query"
                        @focus="open = true; query = ''"
                        @input="open = true"
                        @keydown.escape="open = false"
                        type="text"
                        autocomplete="off"
                        placeholder="Cari jurusan..."
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-11 text-sm text-gray-700 transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100">

                    <iconify-icon
                        icon="mdi:magnify"
                        width="20"
                        height="20"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                    </iconify-icon>
                </div>

                <!-- dropdown -->
                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full max-h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua jurusan')"
                        x-show="!query || 'semua jurusan'.includes(query.toLowerCase())"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>Semua jurusan</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>

                    @foreach ($this->filteredJurusan as $jurusan)
                    <div
                        @click.prevent="select({{ (int) $jurusan->id }}, @js($jurusan->nama))"
                        x-show="!query || @js(strtolower($jurusan->nama)).includes(query.toLowerCase())"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>{{ $jurusan->nama }}</span>
                        <iconify-icon x-show="selectedId == {{ (int) $jurusan->id }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>
                    @endforeach

                    <p
                        x-show="query && !(@js($this->filteredJurusan->pluck('nama')->prepend('Semua jurusan')->values())).some((item) => item.toLowerCase().includes(query.toLowerCase()))"
                        class="px-4 py-3 text-sm text-gray-500">
                        Jurusan tidak ditemukan.
                    </p>

                </div>
            </div>

            <!-- kelas -->
            <div
                x-data="{
                    open: false,
                    query: '',
                    selectedId: null,
                    selectedLabel: null,

                    toggle() {
                        this.open = !this.open
                    },

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.query = label
                        this.open = false

                        $wire.set('filterIndeks', id)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Kelas
                </label>

                <div class="relative">
                    <input
                        x-model="query"
                        @focus="open = true; query = ''"
                        @input="open = true"
                        @keydown.escape="open = false"
                        type="text"
                        autocomplete="off"
                        placeholder="Cari kelas..."
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-11 text-sm text-gray-700 transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100">

                    <iconify-icon
                        icon="mdi:magnify"
                        width="20"
                        height="20"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                    </iconify-icon>
                </div>

                <!-- dropdown -->
                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full max-h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua Kelas')"
                        x-show="!query || 'semua kelas'.includes(query.toLowerCase())"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>Semua Kelas</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>

                    @foreach ($filteredIndeks as $indeks)
                    <div
                        @click.prevent="select({{ (int) $indeks->id }}, @js($indeks->nama))"
                        x-show="!query || @js(strtolower($indeks->nama)).includes(query.toLowerCase())"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>{{ $indeks->nama }}</span>
                        <iconify-icon x-show="selectedId == {{ (int) $indeks->id }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>
                    @endforeach

                    <p
                        x-show="query && !(@js($filteredIndeks->pluck('nama')->prepend('Semua kelas')->values())).some((item) => item.toLowerCase().includes(query.toLowerCase()))"
                        class="px-4 py-3 text-sm text-gray-500">
                        Kelas tidak ditemukan.
                    </p>

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
                                    <a href="{{ route('edit-foto', ['murid' => $murid->uuid]) }}" wire:navigate>
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

                                    <div>
                                        <button
                                            @click="$dispatch('open-delete-foto', { id: {{ $murid->id }} })"
                                            type="button"
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
    
    <livewire:manajemen.murid-foto.components.modal.hapus/>
</div>