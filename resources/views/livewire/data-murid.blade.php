<div>
    <div class="flex items-center justify-start gap-5">
        <!-- btn tambah data siswa -->
        <a href="{{ route('tambah-murid') }}" wire:navigate>
            <button
                class="px-4 py-2 rounded-lg bg-emerald-600 text-white transition-all duration-200 hover:bg-emerald-700 active:scale-95 flex items-center justify-center gap-1 capitalize ">
                <iconify-icon icon="line-md:plus" width="20" height="20"></iconify-icon>
                tambah data murid
            </button>
        </a>

        <!-- btn import CSV -->
        <a href="" wire:navigate>
            <button
                class="px-4 py-2 rounded-lg bg-blue-main text-white transition-all duration-200 hover:bg-blue-deep-solid active:scale-95 flex items-center justify-center gap-1 capitalize ">
                <iconify-icon icon="line-md:file-import" width="20" height="20"></iconify-icon>
                import CSV
            </button>
        </a>
    </div>

    <!-- kategori -->
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm grid grid-cols-2 md:grid-cols-3 gap-5">
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
    </div>
    <!-- table murid -->
    <div x-data="{
            selected: [],
            muridList: @js($listMurid),

            toggleAll(e) {
                if (e.target.checked) {
                    this.selected = this.muridList.map(m => m.id)
                } else {
                    this.selected = []
                }
            }
        }"

        class="mt-2 bg-white p-4 rounded-xl shadow-sm">

        <!-- tombol hapus -->
        <div class="mb-3 flex items-center gap-3 relative" x-data="{ openModalColon: false }">

            <button @click="openModalColon = !openModalColon"
                class="p-2 rounded-lg text-gray-500 duration-200 transition-all hover:bg-blue-deep-solid hover:text-white active:scale-95 flex items-center justify-center ">
                <iconify-icon icon="lineicons:menu-meatballs-1" width="25" height="24"></iconify-icon>
            </button>

            <livewire:components.modal.modal-colon />

            <span class="text-sm text-gray-500">
                <span x-text="selected.length"></span> dipilih
            </span>

        </div>

        <div class="overflow-x-auto table-auto md:table-fixed rounded-t-lg">
            <table class="min-w-full text-sm text-left text-gray-600">

                <thead class="bg-blue-main border border-gray-200 text-white uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">
                            <input type="checkbox" @click="toggleAll">
                        </th>
                        <th class="text-center px-4 py-3">No</th>
                        <th class="text-center px-4 py-3">Nama Murid</th>
                        <th class="text-center px-4 py-3">NIPD</th>
                        <th class="text-center px-4 py-3">NISN</th>
                        <th class="text-center px-4 py-3">Jenis Kelamin</th>
                        <th class="text-center px-4 py-3">Tempat Lahir</th>
                        <th class="text-center px-4 py-3">Tanggal Lahir</th>
                        <th class="text-center px-4 py-3">Agama</th>
                        <th class="text-center px-4 py-3">Alamat</th>
                        <th class="text-center px-4 py-3">RT</th>
                        <th class="text-center px-4 py-3">RW</th>
                        <th class="text-center px-4 py-3">Kelurahan</th>
                        <th class="text-center px-4 py-3">Kecamatan</th>
                        <th class="text-center px-4 py-3">Kelas</th>
                        <th class="text-center px-4 py-3">No HP</th>
                        <th class="text-center px-4 py-3">Email</th>
                        <th class="text-center px-4 py-3">Nama Ayah</th>
                        <th class="text-center px-4 py-3">Nama Ibu</th>
                        <th class="text-center px-4 py-3">Nama Wali</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($listMurid as $index => $murid)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 border-lr border-gray-200">

                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $murid['id'] }}" x-model="selected" >
                            </td>

                            <td class="border-r border-gray-200 px-4 py-3">{{ $index + 1 }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['nama'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['nipd'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['nisn'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['jk'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['tempat_lahir'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['tanggal_lahir'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['agama'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['alamat'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['rt'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['rw'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['kelurahan'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['kecamatan'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->rombel?->nama_lengkap ?? '-' }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['hp'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['email'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['nama_ayah'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['nama_ibu'] }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid['nama_wali'] }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- edit -->
                                    <a href="{{route('edit-murid')}}">
                                        <button class="bg-amber-400 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-amber-600 active:scale-95" >
                                            <iconify-icon icon="lineicons:pencil-1" width="20" height="20"></iconify-icon>
                                        </button>
                                    </a>
    
                                    <!-- hapus -->
                                    <div x-data="{openModal: false}" >
                                        <button @click="openModal = !openModal "  class="bg-rose-500 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-rose-700 active:scale-95" >
                                            <iconify-icon icon="lineicons:trash-3" width="20" height="20"></iconify-icon>
                                        </button>

                                        <!-- modal peringatan hapus murid -->
                                        <livewire:components.modal.modal-hapus-murid/>
                                    </div>

                                    <!-- qr -->
                                    <div>
                                        <button class="bg-blue-deep-solid w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-blue-deep hover:text-gray-400 active:scale-95" >
                                            <iconify-icon icon="la:qrcode" width="20" height="20"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                                
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
