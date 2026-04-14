<div>
    <div class="flex items-center justify-start gap-5">
        <!-- btn tambah data siswa -->
        <a 
            href="{{ route('tambah-guru') }}"
            wire:navigate>
            <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white transition-all duration-200 hover:bg-emerald-700 active:scale-95 flex items-center justify-center gap-1 capitalize " >
                <iconify-icon icon="line-md:plus" width="20" height="20"></iconify-icon>
                tambah data guru
            </button>
        </a>

        <!-- btn import CSV -->
        <a href="" wire:navigate>
            <button class="px-4 py-2 rounded-lg bg-blue-main text-white transition-all duration-200 hover:bg-blue-deep-solid active:scale-95 flex items-center justify-center gap-1 capitalize " >
                <iconify-icon icon="line-md:file-import" width="20" height="20"></iconify-icon>
                import CSV
            </button>
        </a> 
    </div>
    <!-- table guru -->
    <div
        x-data="{
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
                <div class="mb-3 flex items-center gap-3 relative" x-data="{ openModalColon: false }" >
        
                    <button
                        @click="openModalColon = !openModalColon"
                        class="p-2 rounded-lg text-gray-500 duration-200 transition-all hover:bg-blue-deep-solid hover:text-white active:scale-95 flex items-center justify-center "
                        >
                            <iconify-icon icon="lineicons:menu-meatballs-1" width="25" height="24"></iconify-icon>
                    </button>
        
                    <livewire:components.modal.modal-colon/>
        
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
                        placeholder="Cari Nama Guru..."
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

        <div
            x-data="{
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

                <thead class="bg-blue-main text-white uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">
                            <input type="checkbox" @click="toggleAll">
                        </th>
                        <th class="text-center px-4 py-3">No</th>
                        <th class="text-center px-4 py-3">Nama Guru</th>
                        <th class="text-center px-4 py-3">NUPTK</th>
                        <th class="text-center px-4 py-3">Jenis Kelamin</th>
                        <th class="text-center px-4 py-3">Tempat Lahir</th>
                        <th class="text-center px-4 py-3">Tanggal Lahir</th>
                        <th class="text-center px-4 py-3">NIP</th>
                        <th class="text-center px-4 py-3">Status Kepegawaian</th>
                        <th class="text-center px-4 py-3">Jenis PTK</th>
                        <th class="text-center px-4 py-3">Agama</th>
                        <th class="text-center px-4 py-3">Alamat Jalan</th>
                        <th class="text-center px-4 py-3">RT</th>
                        <th class="text-center px-4 py-3">RW</th>
                        <th class="text-center px-4 py-3">Desa / Kelurahan</th>
                        <th class="text-center px-4 py-3">Kecamatan</th>
                        <th class="text-center px-4 py-3">Kode Pos</th>
                        <th class="text-center px-4 py-3">Telepon</th>
                        <th class="text-center px-4 py-3">HP</th>
                        <th class="text-center px-4 py-3">Email</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($listGuru as $index -> $guru)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 border-lr border-gray-200">

                            <td class="px-4 py-3    ">
                                <input type="checkbox" value="{{ $guru->ulid }}" :checked="selected.includes('{{ $guru->ulid }}')"x-model="selected" > 
                            </td>

                            <td class="px-4 py-3">
                                {{ $index + 1}}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['nama'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['nuptk'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['jk'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['tempat_lahir'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['tanggal_lahir'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['nip'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['status_kepegawaian'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['jenis_ptk'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['agama'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru->alamat_jalan }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru->rt }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru->rw }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru->desa_kelurahan }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru->kecamatan }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru->kode_pos }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru->telepon }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru->hp }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru->email }}
                            </td>

                            <td class="px-4 py-3 flex justify-center gap-2">
                                <!-- edit -->
                                <a href="{{route('edit-guru')}}">
                                    <button class="bg-amber-400 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-amber-500 active:scale-95" >
                                        <iconify-icon icon="lineicons:pencil-1" width="20" height="20"></iconify-icon>
                                    </button>
                                </a>

                                <!-- hapus -->
                                    <button
                                        type="button"
                                        @click="
                                            selectedGuru('{{ $guru->ulid }}');
                                            toggleDelete();
                                            $dispatch( 'open-delete-guru', { ulid: selectedGuruUlid });
                                        "
                                        class="bg-rose-500 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-rose-600 active:scale-95" >
                                            <iconify-icon icon="lineicons:trash-3" width="20" height="20"></iconify-icon>
                                    </button>

                                <!-- qr -->
                                <div>
                                    <button class="bg-blue-deep-solid w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-blue-deep active:scale-95" >
                                        <iconify-icon icon="la:qrcode" width="20" height="20"></iconify-icon>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>