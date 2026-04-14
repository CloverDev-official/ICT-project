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
        <div
        x-data="{
            open: false,
            selectedId: @entangle('filterTingkat').live,
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
        class="relative w-full"
        >
            <label class="text-sm text-gray-600 capitalize font-semibold">tingkat</label>

            <div @click="toggle()"
                class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                <span x-text="selectedLabel ?? 'Semua tingkat'" class="text-gray-700 text-sm"></span>
                <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                    icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
            </div>

            <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                <div
                    @click.prevent="select(null, 'Semua tingkat')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                >
                    <span>Semua tingkat</span>
                    <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                </div>

                @foreach ($tingkatRombel as $tingkat)
                    <div
                        @click.prevent="select({{ (int) $tingkat->id }}, @js($tingkat->nama))"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                    >
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
            selectedId: @entangle('filterJurusan').live,
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
        class="relative w-full"
        >
            <label class="text-sm text-gray-600 capitalize font-semibold">jurusan</label>

            <div @click="toggle()"
                class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                <span x-text="selectedLabel ?? 'Semua jurusan'" class="text-gray-700 text-sm"></span>
                <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                    icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
            </div>

            <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                <div
                    @click.prevent="select(null, 'Semua jurusan')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                >
                    <span>Semua jurusan</span>
                    <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                </div>

                @foreach ($this->filteredJurusan as $jurusan)
                    <div
                        @click.prevent="select({{ (int) $jurusan->id }}, @js($jurusan->nama))"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                    >
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
            selectedId: @entangle('filterIndeks').live,
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
        class="relative w-full"
        >
            <label class="text-sm text-gray-600 capitalize font-semibold">Kelas</label>

            <div @click="toggle()"
                class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                <span x-text="selectedLabel ?? 'Semua Kelas'" class="text-gray-700 text-sm"></span>
                <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                    icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
            </div>

            <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                <div
                    @click.prevent="select(null, 'Semua Kelas')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                >
                    <span>Semua Kelas</span>
                    <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                </div>

                @foreach ($this->filteredRombel as $rombel)
                    <div
                        @click.prevent="select({{ (int) $rombel->id }}, @js($rombel->nama_lengkap))"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                    >
                        <span>{{ $rombel->nama_lengkap }}</span>
                        <iconify-icon x-show="selectedId == {{ (int) $rombel->id }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
    <!-- table murid -->
    <div x-data="{
            selected: [],
            muridList: @js($listMurid->pluck('ulid')),

            toggleAll(e) {
                if (e.target.checked) {
                    this.selected = this.muridList
                } else {
                    this.selected = []
                }
            }
        }"

        class="mt-2 bg-white p-4 rounded-xl shadow-sm">

        <div class="flex justify-between items-center gap-4 mb-5">
            <!-- tombol hapus -->
            <div class="mb-3 flex items-center gap-3 relative" x-data="{ openModalColon: false }">
    
                <button @click="openModalColon = !openModalColon"
                    class="p-2 rounded-lg text-gray-500 duration-200  transition-all hover:bg-blue-deep-solid hover:text-white active:scale-95 flex items-center justify-center ">
                    <iconify-icon icon="lineicons:menu-meatballs-1" width="25" height="24"></iconify-icon>
                </button>
    
                <livewire:components.modal.modal-colon />
    
                <span class="text-sm text-gray-500">
                    <span x-text="selected.length"></span> dipilih
                </span>
    
            </div>
            <!-- input search -->
            <div class="relative w-40 md:w-sm">
                <input 
                    type="search"
                    name="search"
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

        <div x-data="{
            openModalDelete: false,
            selectedMuridUlid: null,

            selectedMurid(muridUlid) {
                this.selectedMuridUlid = muridUlid
            },
            
            toggleDelete() {
                this.openModalDelete = !this.openModalDelete
            }
        }"
        
        class="overflow-x-auto table-auto md:table-fixed rounded-t-lg">
            <table class="min-w-full text-sm text-left text-gray-600">

                <thead class="bg-blue-main border border-gray-200 text-white uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">
                            <input type="checkbox" @click="toggleAll">
                        </th>
                        <th class="text-center px-4 py-3">No.</th>
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
                                <input type="checkbox" value="{{ $murid->ulid }}" :checked="selected.includes('{{ $murid->ulid }}')"x-model="selected" >
                            </td>

                            <td class="border-r border-gray-200 px-4 py-3">{{ $index + 1 }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->nama }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->nipd }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->nisn }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->jk }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->tempat_lahir }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->tanggal_lahir }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->agama }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->alamat }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->rt }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->rw }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->kelurahan }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->kecamatan }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->rombel?->nama_lengkap ?? '-' }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->hp }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->email }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->nama_ayah }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->nama_ibu }}</td>
                            <td class="border-r border-gray-200 px-4 py-3">{{ $murid->nama_wali }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- edit -->
                                    <a href="{{route('edit-murid', $murid->ulid)}}" wire:navigate>
                                        <button class="bg-amber-400 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-amber-600 active:scale-95" >
                                            <iconify-icon icon="lineicons:pencil-1" width="20" height="20"></iconify-icon>
                                        </button>
                                    </a>

                                    <button
                                        type="button"
                                        @click="
                                            selectedMurid('{{ $murid->ulid }}');
                                            toggleDelete();
                                            $dispatch('open-delete-murid', { ulid: selectedMuridUlid });
                                        "
                                        class="bg-rose-500 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-rose-700 active:scale-95"
                                    >
                                        <iconify-icon icon="lineicons:trash-3" width="20" height="20"></iconify-icon>
                                    </button>
    
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

                    <!-- modal peringatan hapus murid -->
                    
                </tbody>
                <livewire:murid.delete/>
            </table>
        </div>
    </div>
</div>
