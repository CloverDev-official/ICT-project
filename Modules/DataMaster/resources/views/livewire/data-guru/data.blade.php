<div class="space-y-6">

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-lg">

        <!-- glow -->
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-white/5"></div>

        <div
            class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:user-id-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-white">
                        Data Guru
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Kelola seluruh data guru dan tenaga pendidik sekolah.
                    </p>

                </div>

            </div>

            <!-- action -->
            <div class="sm:flex grid grid-cols-1  gap-3 sm:flex-row">

                <!-- tambah -->
                <a
                    href="{{ route('tambah-guru') }}"
                    wire:navigate>

                    <button
                        class="w-full group flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-blue-main shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">

                        <iconify-icon
                            icon="line-md:plus"
                            width="22"
                            height="22"
                            class="transition duration-300 group-hover:rotate-90">
                        </iconify-icon>

                        Tambah Guru

                    </button>

                </a>

                <!-- import -->
                <div x-data="{ openModalImport: false }">

                    <button
                        @click="openModalImport = true"
                        class="w-full flex items-center justify-center gap-2 rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">

                        <iconify-icon
                            icon="line-md:file-import"
                            width="22"
                            height="22">
                        </iconify-icon>

                        Import XLSX

                    </button>

                    <livewire:datamaster.data-guru.components.modal.import />

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
                    Filter Guru
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Filter berdasarkan status kepegawaian dan jenis PTK.
                </p>

            </div>

            <!-- search -->
            <div class="relative w-full lg:w-80">

                <input
                    type="search"
                    name="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari nama guru..."
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

        <!-- filter -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- status -->
            <livewire:components.searchable-select
                    wire:model.live="filterStatus"
                    :options="$statusOptions"
                    value-key="id" label-key="nama"
                    label="Status Kepegawaian" placeholder="Cari status kepegawaian..."
                    all-label="Semua status"
                    not-found-text="Pilihan tidak ditemukan." />

            <!-- jenis -->
            <livewire:components.searchable-select
                    wire:model.live="filterjenis"
                    :options="$jenisOptions"
                    value-key="id" label-key="nama"
                    label="Jenis PTK" placeholder="Cari jenis ptk..."
                    all-label="Semua jenis"
                    not-found-text="Pilihan tidak ditemukan." />

        </div>

    </div>

    <!-- TABLE -->
    <div
        x-data="{
            selected: [],

            getPageUuids() {
                return Array.from(
                    this.$root.querySelectorAll('.guru-row-checkbox')
                ).map(el => el.value)
            },

            toggleAll(e) {
                if (e.target.checked) {
                    this.selected = this.getPageUuids()
                } else {
                    this.selected = []
                }
            }
        }"
        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- top -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Daftar Guru
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Seluruh data tenaga pendidik yang tersedia pada sistem.
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

                        <th class="px-5 py-4 text-center">
                            No
                        </th>

                        <th class="px-5 py-4 text-left font-semibold">
                            Nama Guru
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            NUPTK
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Jenis Kelamin
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Tempat Lahir
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Tanggal Lahir
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            NIP
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Status
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Jenis PTK
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Agama
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Alamat
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            RT
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            RW
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Kelurahan
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Kecamatan
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Kode pos
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Telepon
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            HP
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($listGuru as $index => $guru)

                    @php
                    $statusClass = $guru->status_kepegawaian === 'PNS'
                    ? 'bg-emerald-100 text-emerald-600'
                    : 'bg-amber-100 text-amber-600';
                    @endphp

                    <tr
                        class="border-t border-gray-100 transition hover:bg-gray-50">

                        <!-- checkbox -->
                        <td class="px-5 py-5 text-center">

                            <input
                                type="checkbox"
                                class="guru-row-checkbox rounded border-gray-300"
                                value="{{ $guru->public_id }}"
                                x-model="selected">

                        </td>

                        <td class="px-5 py-5 text-center">
                            {{ $index + 1 }}
                        </td>

                        <!-- guru -->
                        <td class="px-5 py-5">

                            <div class="flex items-center gap-4">

                                <!-- avatar -->
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 font-bold uppercase text-blue-main">

                                    {{ substr($guru->nama, 0, 1) }}

                                </div>

                                <!-- info -->
                                <div>

                                    <h2
                                        class="font-semibold text-gray-800">

                                        {{ $guru->nama }}

                                    </h2>

                                    <p
                                        class="mt-1 text-xs text-gray-400">

                                        {{ $guru->email }}

                                    </p>

                                </div>

                            </div>

                        </td>

                        <!-- nuptk -->
                        <td class="px-5 py-5 text-center text-gray-600">
                            {{ $guru->nuptk ?? '-' }}
                        </td>

                        <!-- jk -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->jk ?? '-' }}
                        </td>

                        <!-- tempat lahir -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->tempat_lahir ?? '-' }}
                        </td>

                        <!-- tanggal lahir -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->tanggal_lahir ? $guru->tanggal_lahir->format('Y-m-d') : '-' }}
                        </td>

                        <!-- NIP -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->nip ?? '-' }}
                        </td>

                        <!-- status -->
                        <td class="px-5 py-5 text-center">

                            <div
                                class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold {{ $statusClass }}">

                                <div
                                    class="h-2 w-2 rounded-full bg-current">
                                </div>

                                {{ $guru->status_kepegawaian ?? '-' }}

                            </div>

                        </td>


                        <!-- jenis -->
                        <td class="px-5 py-5 text-center">

                            <div
                                class="min-w-28 flex justify-center items-center rounded-full bg-blue-100 px-4 py-1 text-xs font-semibold text-blue-600">

                                {{ $guru->jenis_ptk ?? '-' }}

                            </div>

                        </td>

                        <!-- agama -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->agama ?? '-' }}
                        </td>

                        <!-- ALAMAT -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->alamat_jalan ?? '-' }}
                        </td>

                        <!-- RT -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->rt ?? '-' }}
                        </td>

                        <!-- rw -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->rw ?? '-' }}
                        </td>

                        <!-- desa -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->desa_kelurahan ?? '-' }}
                        </td>

                        <!-- kecamatan -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->kecamatan ?? '-' }}
                        </td>

                        <!-- kode -->
                        <td class="px-5 py-5 text-center">
                            {{ $guru->kode_pos ?? '-' }}
                        </td>

                        <!-- kontak -->
                        <td class="px-5 py-5 text-center text-gray-600">
                            {{ $guru->telepon ?? '-' }}
                        </td>

                        <!-- kontak -->
                        <td class="px-5 py-5 text-center text-gray-600">
                            {{ $guru->hp ?? '-' }}
                        </td>

                        <!-- aksi -->
                        <td class="px-5 py-5">

                            <div
                                class="flex items-center justify-center gap-2">

                                <!-- edit -->
                                <a
                                    href="{{ route('edit-guru', $guru->id) }}"
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
                                <div>

                                    <button
                                        @click="$dispatch('open-delete-guru', { id: {{ $guru->id }} })"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-rose-600">

                                        <iconify-icon
                                            icon="lineicons:trash-3"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                    </button>

                                </div>

                                <!-- qr -->
                                <button
                                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-main text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-deep-solid">

                                    <iconify-icon
                                        icon="la:qrcode"
                                        width="20"
                                        height="20">
                                    </iconify-icon>

                                </button>

                            </div>

                        </td>

                    </tr>

                    @empty
                        <tr>
                            <td colspan="20" class="px-6 py-10 text-center text-sm text-gray-400">
                                Data guru belum tersedia.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        </div>

        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            {{ $listGuru->links('livewire.components.pagination') }}
        </div>

        <livewire:datamaster.data-guru.components.modal.hapus />

    </div>

</div>
