<div>
    @php
    $tingkatList = [
    ['id' => 1, 'nama' => 'X'],
    ['id' => 2, 'nama' => 'XI'],
    ['id' => 3, 'nama' => 'XII'],
    ];

    $jurusanList = [
    ['id' => 1, 'nama' => 'PPLG'],
    ['id' => 2, 'nama' => 'Animasi'],
    ['id' => 3, 'nama' => 'DKV'],
    ];

    $kelasList = [
    ['id' => 1, 'nama' => 'A'],
    ['id' => 2, 'nama' => 'B'],
    ['id' => 3, 'nama' => 'C'],
    ];
    @endphp

    <!-- kategori -->
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm grid grid-cols-2 md:grid-cols-3 gap-5 ">

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
                }
            }"
            class="relative w-full">

            <label class="text-sm text-gray-600 capitalize font-semibold">
                tingkat
            </label>

            <div
                @click="toggle()"
                class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">

                <span x-text="selectedLabel ?? 'Semua tingkat'" class="text-gray-700 text-sm"></span>

                <iconify-icon
                    class="text-gray-400 transition-transform"
                    :class="{ 'rotate-180': open }"
                    icon="lineicons:chevron-up"
                    width="25"
                    height="24">
                </iconify-icon>
            </div>

            <div
                x-show="open"
                @click.outside="open = false"
                x-transition
                style="display:none"
                class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                <div
                    @click.prevent="select(null, 'Semua tingkat')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                    <span>Semua tingkat</span>

                    <iconify-icon
                        x-show="selectedId === null"
                        icon="lineicons:check"
                        width="24"
                        height="24">
                    </iconify-icon>
                </div>

                @foreach ($tingkatList as $tingkat)
                <div
                    @click.prevent="select({{ $tingkat['id'] }}, '{{ $tingkat['nama'] }}')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                    <span>{{ $tingkat['nama'] }}</span>

                    <iconify-icon
                        x-show="selectedId == {{ $tingkat['id'] }}"
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
                }
            }"
            class="relative w-full">

            <label class="text-sm text-gray-600 capitalize font-semibold">
                jurusan
            </label>

            <div
                @click="toggle()"
                class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">

                <span x-text="selectedLabel ?? 'Semua jurusan'" class="text-gray-700 text-sm"></span>

                <iconify-icon
                    class="text-gray-400 transition-transform"
                    :class="{ 'rotate-180': open }"
                    icon="lineicons:chevron-up"
                    width="25"
                    height="24">
                </iconify-icon>
            </div>

            <div
                x-show="open"
                @click.outside="open = false"
                x-transition
                style="display:none"
                class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                <div
                    @click.prevent="select(null, 'Semua jurusan')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                    <span>Semua jurusan</span>

                    <iconify-icon
                        x-show="selectedId === null"
                        icon="lineicons:check"
                        width="24"
                        height="24">
                    </iconify-icon>
                </div>

                @foreach ($jurusanList as $jurusan)
                <div
                    @click.prevent="select({{ $jurusan['id'] }}, '{{ $jurusan['nama'] }}')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                    <span>{{ $jurusan['nama'] }}</span>

                    <iconify-icon
                        x-show="selectedId == {{ $jurusan['id'] }}"
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
                }
            }"
            class="relative w-full">

            <label class="text-sm text-gray-600 capitalize font-semibold">
                kelas
            </label>

            <div
                @click="toggle()"
                class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">

                <span x-text="selectedLabel ?? 'Semua kelas'" class="text-gray-700 text-sm"></span>

                <iconify-icon
                    class="text-gray-400 transition-transform"
                    :class="{ 'rotate-180': open }"
                    icon="lineicons:chevron-up"
                    width="25"
                    height="24">
                </iconify-icon>
            </div>

            <div
                x-show="open"
                @click.outside="open = false"
                x-transition
                style="display:none"
                class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                <div
                    @click.prevent="select(null, 'Semua kelas')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                    <span>Semua kelas</span>

                    <iconify-icon
                        x-show="selectedId === null"
                        icon="lineicons:check"
                        width="24"
                        height="24">
                    </iconify-icon>
                </div>

                @foreach ($kelasList as $kelas)
                <div
                    @click.prevent="select({{ $kelas['id'] }}, '{{ $kelas['nama'] }}')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                    <span>{{ $kelas['nama'] }}</span>

                    <iconify-icon
                        x-show="selectedId == {{ $kelas['id'] }}"
                        icon="lineicons:check"
                        width="24"
                        height="24">
                    </iconify-icon>
                </div>
                @endforeach
            </div>
        </div>

    </div>
    <!-- TABLE DAFTAR PETUGAS -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-5">

        <!-- category -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-4 pt-4">

            <!-- input search -->
            <div class="col-span-2 md:col-span-2 md:col-start-3 relative w-full">
                <input
                    type="search"
                    name="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari Nama User..."
                    class="min-w-xs w-full text-sm mt-1 px-4 pr-10 py-2 bg-gray-100 border border-gray-300 rounded-xl
                    hover:border-blue-500
                    focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none
                    transition" />

                <div class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none">
                    <iconify-icon icon="mdi:account-search-outline" width="20" height="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto m-4 rounded-lg shadow-sm">
            <!-- TABLE -->
            <table class="w-full text-sm">

                <thead class="bg-blue-main text-white">
                    <tr>
                        <th class="border-gray-400 px-6 py-3">No</th>
                        <th class="border-gray-400 px-6 py-3">NIPD</th>
                        <th class="border-gray-400 px-6 py-3">NISN</th>
                        <th class="border-gray-400 px-6 py-3">Nama</th>                    
                        <th class="border-gray-400 px-6 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ([
                    ['nama' => 'obeh', 'nipd' => '11122', 'nisn' => '009786817'],
                    ['nama' => 'atul', 'nipd' => '11116', 'nisn' => '009676767' ],
                    ['nama' => 'bambang', 'nipd' => '696969', 'nisn' => '69696969'],
                    ] as $item)

                    <tr class="text-center hover:bg-gray-100">
                        <td class="border-r border-gray-200 px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="border-r border-gray-200 px-6 py-4">{{ $item['nipd'] }}</td>
                        <td class="border-r border-gray-200 px-6 py-4">{{ $item['nisn'] }}</td>
                        <td class="border-r border-gray-200 px-6 py-4">{{$item['nama']}}</td>
                        <td class="border-r border-gray-200">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('riwayat-detail-absen-murid') }}" wire:navigate >
                                    <button class="w-8 h-8 flex items-center justify-center p-2 rounded-xl bg-blue-500  shadow-xs text-white transition-all duration-150 hover:bg-blue-600 active:scale-95">
                                        <iconify-icon icon="mdi:eye" width="20" height="20"></iconify-icon>
                                    </button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>