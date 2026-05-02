<div class="flex flex-col w-full gap-5" >
    <!-- kategori -->
    <div class="bg-white p-4 rounded-lg shadow-sm" >
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <!-- tingkat -->
            <div x-data="{
                open: false,
                selected: '',
                select(id, label) {
                    this.selected = label
                    this.open = false
                    $wire.set('filterTingkat', id)
                }
            }" class="relative w-full">

                <label class="text-sm text-gray-600 capitalize font-semibold">tingkat</label>

                <div @click="open = !open"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer  hover:border-blue-500 transition">

                    <span x-text="selected ? selected : 'Semua Tingkat'" class="text-gray-700 text-sm"></span>

                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </div>

                <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">

                    <div @click="select(null, 'Semua Tingkat')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition">
                        Semua Tingkat
                    </div>

                    @foreach ($listRombel->pluck('tingkat')->filter()->unique('id') as $tingkat)
                        <div @click="select({{ $tingkat->id }}, '{{ $tingkat->nama }}')"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition">
                            {{ $tingkat->nama }}
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- jurusan -->
            <div x-data="{
                open: false,
                selected: '',
                select(id, label) {
                    this.selected = label
                    this.open = false
                    $wire.set('filterJurusan', id)
                }
            }" class="relative w-full">

                <label class="text-sm text-gray-600 capitalize font-semibold ">jurusan</label>

                <div @click="open = !open"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer  hover:border-blue-500 transition">

                    <span x-text="selected ? selected : 'Semua jurusan'" class="text-gray-700 text-sm line-clamp-1 "></span>

                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </div>

                <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div @click="select(null, 'Semua jurusan')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition">
                        Semua jurusan
                    </div>

                    @foreach ($filteredJurusan as $jurusan)
                        <div @click="select({{ $jurusan->id }}, '{{ $jurusan->nama }}')"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition">
                            {{ $jurusan->nama }}
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- kelas -->
            <div x-data="{
                open: false,
                selected: '',
                select(id, label) {
                    this.selected = label
                    this.open = false
                    $wire.set('filterIndeks', id)
                }
            }" class="relative w-full">

                <label class="text-sm text-gray-600 capitalize font-semibold">kelas</label>

                <div @click="open = !open"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer  hover:border-blue-500 transition">

                    <span x-text="selected ? selected : 'Semua kelas'" class="text-gray-700 text-sm"></span>

                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </div>

                <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div @click="select(null, 'Semua kelas')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition">
                        Semua kelas
                    </div>

                    @foreach ($filteredIndeks as $indeks)
                        <div @click="select({{ $indeks->id }}, '{{ $indeks->nama }}')"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition">
                            {{ $indeks->nama }}
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- tanggal absen -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Tanggal  Absen</label>
                <input type="date" wire:model.live="filterTanggal"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2">
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white p-4 rounded-lg shadow-sm">
        <div class="flex justify-end mb-5">
            <div class="relative w-40 md:w-sm">
                <input 
                    type="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari Nama Murid..."
                    class="w-full text-sm mt-1 px-4 pr-10 py-2 bg-gray-100 border border-gray-300 rounded-xl
                    hover:border-blue-500
                    focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none
                    transition"
                />

                <div class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none">
                    <iconify-icon icon="mdi:account-search-outline" width="20" height="20"></iconify-icon>
                </div>
            </div> 
        </div>
        
        <div class="overflow-x-auto table-auto md:table-fixed rounded-t-lg">
            <table class="min-w-full text-sm text-left text-gray-600" >
                <thead class="bg-blue-main border border-gray-200 text-white uppercase text-xs" >
                    <tr>
                        <th class="capitalize text-center px-4 py-3">no.</th>
                        <th class="capitalize text-center px-4 py-3">NIPD</th>
                        <th class="capitalize text-center px-4 py-3">nama Murid</th>
                        <th class="capitalize text-center px-4 py-3">kehadiran</th>
                        <th class="capitalize text-center px-4 py-3">tanggal</th>
                        <th class="capitalize text-center px-4 py-3">jam masuk</th>
                        <th class="capitalize text-center px-4 py-3">jam pulang</th>
                        <th class="capitalize text-center px-4 py-3">keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($listAbsen as $item)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 border-lr border-gray-200" > 
                            <td class="border-r text-center border-gray-200 px-4 py-3">{{ $loop->iteration }}</td>
                            <td class="border-r text-center border-gray-200 px-4 py-3">{{ $item->murid->nipd }}</td>
                            <td class="border-r text-center border-gray-200 px-4 py-3">{{ $item->murid->nama }}</td>
                            <td class="border-r text-center border-gray-200 px-4 py-3">{{ $item->status }}</td>
                            <td class="border-r text-center border-gray-200 px-4 py-3">{{ $item->tanggal->format('d-m-Y')}}</td>
                            <td class="border-r text-center border-gray-200 px-4 py-3">{{ $item->waktu_masuk }}</td>
                            <td class="border-r text-center border-gray-200 px-4 py-3">{{ $item->waktu_keluar }}</td>
                            <td class="border-r text-center border-gray-200 px-4 py-3">{{ $item->keterangan }}</td>
                        </tr>                   
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $listAbsen->links('livewire.components.pagination') }}
    </div>
</div>