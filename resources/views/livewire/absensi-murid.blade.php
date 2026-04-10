<div class="flex flex-col w-full gap-5" >
    <!-- kategori -->
    <div class="bg-white p-4 rounded-lg shadow-sm" >
        <div>
            <h1 class="text-2xl font-bold capitalize" >daftar kategori</h1>
            <p class="text-sm text-gray-400 line-clamp-2" >Silahkan pilih kategori agar bisa menentukan kelas yang diinginkan</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4" >
            <!-- tingkat -->
            <div x-data="{
                open: false,
            
                selected: '',
            
                select(item) {
                    this.selected = item
                    this.open = false
            
                }
            
            }" class="relative w-full">
                <label class="text-sm text-gray-600 capitalize font-semibold">tingkat</label>

                <input type="hidden" x-model="selected" wire:model.live="filterTingkat">
                <!-- Button -->
                <div @click="open = !open"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer  hover:border-blue-500 transition">

                    <span x-text="selected ? selected : 'Semua Tingkat'" class="text-gray-700 text-sm"></span>

                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </div>

                <!-- Dropdown -->
                <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
                    @foreach ($tingkatRombel as $tingkat)
                        <div @click="select('{{ $tingkat }}')"
                            class="px-4 py-2  cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                            <span>{{ $tingkat }}</span>

                            <!-- icon check -->
                            <iconify-icon x-show="selected === '{{ $tingkat }}'" icon="lineicons:check" width="24"
                                height="24"></iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- jurusan -->
            <div x-data="{
                open: false,
            
                selected: '',
            
                select(item) {
                    this.selected = item
                    this.open = false
            
                }
            
            }" class="relative w-full">
                <label class="text-sm text-gray-600 capitalize font-semibold">jurusan</label>

                <input type="hidden" x-model="selected" wire:model.live="filterJurusan">
                <!-- Button -->
                <div @click="open = !open"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer  hover:border-blue-500 transition">

                    <span x-text="selected ? selected : 'Semua jurusan'" class="text-gray-700 text-sm"></span>

                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </div>

                <!-- Dropdown -->
                <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">
                    @foreach ($jurusanRombel as $jurusan)
                        <div @click="select('{{ $jurusan }}')"
                            class="px-4 py-2  cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                            <span>{{ $jurusan }}</span>

                            <!-- icon check -->
                            <iconify-icon x-show="selected === '{{ $jurusan }}'" icon="lineicons:check" width="24"
                                height="24"></iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- kelas -->
            <div x-data="{
                open: false,
            
                selected: '',
            
                select(item) {
                    this.selected = item
                    this.open = false
            
                }
            
            }" class="relative w-full">
                <label class="text-sm text-gray-600 capitalize font-semibold">kelas</label>

                <input type="hidden" x-model="selected" wire:model.live="filterKelas">
                <!-- Button -->
                <div @click="open = !open"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer  hover:border-blue-500 transition">

                    <span x-text="selected ? selected : 'Semua kelas'" class="text-gray-700 text-sm"></span>

                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </div>

                <!-- Dropdown -->
                <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">
                    @foreach ($listRombel->pluck('nama_lengkap') as $rombel)
                        <div @click="select('{{ $rombel }}')"
                            class="px-4 py-2  cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                            <span>{{ $rombel }}</span>

                            <!-- icon check -->
                            <iconify-icon x-show="selected === '{{ $rombel }}'" icon="lineicons:check" width="24"
                                height="24"></iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- tanggal absen -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Tanggal  Absen</label>
                <input type="date" wire:model.defer="tanggal"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2">
                @error('tanggal')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
        <div class="flex justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold capitalize" >daftar Absen Murid</h1>
                <p class="text-sm text-gray-400 line-clamp-2" >daftar absen murid pada tanggal ( nanti tanggal dari yang dipilih)</p>
            </div>

            <!-- input search -->
            <div class="relative w-40 md:w-sm">
                <input 
                    type="search"
                    name="search"
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
                        <th class="capitalize text-center px-4 py-3">NUPTK</th>
                        <th class="capitalize text-center px-4 py-3">nama guru</th>
                        <th class="capitalize text-center px-4 py-3">kehadiran</th>
                        <th class="capitalize text-center px-4 py-3">jam masuk</th>
                        <th class="capitalize text-center px-4 py-3">jam pulang</th>
                        <th class="capitalize text-center px-4 py-3">keterangan</th>
                        <th class="capitalize text-center px-4 py-3">aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 border-lr border-gray-200" > 
                        <td class="border-r border-gray-200 px-4 py-3" >1</td>
                        <td class="border-r border-gray-200 px-4 py-3" >ghaizan</td>
                        <td class="border-r border-gray-200 px-4 py-3" >hadir</td>
                        <td class="border-r border-gray-200 px-4 py-3" >07.00</td>
                        <td class="border-r border-gray-200 px-4 py-3" >16.30</td>
                        <td class="border-r border-gray-200 px-4 py-3" >-</td>
                        <td class="border-r border-gray-200 px-4 py-3" >
                            <div x-data="{openModal: false}" class="flex items-center justify-center">
                                <!-- button -->
                                <button @click="open = !open" class="bg-amber-400 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-amber-600 active:scale-95" >
                                        <iconify-icon icon="lineicons:pencil-1" width="20" height="20"></iconify-icon>
                                        Edit
                                </button>
                                <livewire:components.modal.modal-edit-absen/>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
