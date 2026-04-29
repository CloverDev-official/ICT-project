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
        <div x-data="{ openModalImport: false }">
            <button
                @click="openModalImport = true"
                class="px-4 py-2 rounded-lg bg-blue-main text-white transition-all duration-200 hover:bg-blue-deep-solid active:scale-95 flex items-center justify-center gap-1 capitalize ">
                <iconify-icon icon="line-md:file-import" width="20" height="20"></iconify-icon>
                import CSV
            </button>
            <!-- modal import murid  -->
            <livewire:components.modal.guru.modal-import-guru />
        </div>
    </div>
    <!-- table guru -->
    <div
        x-data="{
            selected: [],
            
            getPageUlids() {
                return Array.from(
                    this.$root.querySelectorAll('.guru-row-checkbox')
                ).map(el => el.value)
            },

            toggleAll(e) {
                if (e.target.checked) {
                    this.selected = this.getPageUlids()
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
                selectedGuruUlid: null,

                selectedGuru(guruUlid) {
                    this.selectedguruUlid = guruUlid
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
                    @php
                        $listGuru = [
                            [
                                'ulid' => '01HXYZ123ABC',
                                'nama' => 'Ghaizan',
                                'nuptk' => '1234567890',
                                'jk' => 'L',
                                'tempat_lahir' => 'Samarinda',
                                'tanggal_lahir' => '2005-01-15',
                                'nip' => '1987654321',
                                'status_kepegawaian' => 'PNS',
                                'jenis_ptk' => 'Guru Mapel',
                                'agama' => 'Islam',
                                'alamat_jalan' => 'Jl. Ahmad Yani',
                                'rt' => '01',
                                'rw' => '02',
                                'desa_kelurahan' => 'Air Putih',
                                'kecamatan' => 'Samarinda Ulu',
                                'kode_pos' => '75124',
                                'telepon' => '0541123456',
                                'hp' => '081234567890',
                                'email' => 'ghaizan@email.com',
                            ],
                            [
                                'ulid' => '01HXYZ456DEF',
                                'nama' => 'Budi Santoso',
                                'nuptk' => '0987654321',
                                'jk' => 'L',
                                'tempat_lahir' => 'Balikpapan',
                                'tanggal_lahir' => '1990-07-20',
                                'nip' => '1122334455',
                                'status_kepegawaian' => 'Honorer',
                                'jenis_ptk' => 'Guru BK',
                                'agama' => 'Islam',
                                'alamat_jalan' => 'Jl. Sudirman',
                                'rt' => '03',
                                'rw' => '01',
                                'desa_kelurahan' => 'Gunung Bahagia',
                                'kecamatan' => 'Balikpapan Selatan',
                                'kode_pos' => '76114',
                                'telepon' => '0542123456',
                                'hp' => '082345678901',
                                'email' => 'budi@email.com',
                            ],
                        ];
                    @endphp
                    @foreach ($listGuru as $index => $guru)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 border-lr border-gray-200">

                            <td class="px-4 py-3    ">
                                <input type="checkbox"  > 
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
                                {{ $guru['alamat_jalan'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['rt'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['rw'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['desa_kelurahan'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['kecamatan'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['kode_pos'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['telepon'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['hp'] }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $guru['email'] }}
                            </td>

                            <td class="px-4 py-3 flex justify-center gap-2">
                                <!-- edit -->
                                <a href="{{route('edit-guru')}}">
                                    <button class="bg-amber-400 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-amber-500 active:scale-95" >
                                        <iconify-icon icon="lineicons:pencil-1" width="20" height="20"></iconify-icon>
                                    </button>
                                </a>

                                <!-- hapus -->
                                <div x-data="{ openModalDelete: false }" >
                                    <button
                                        @click="openModalDelete = true"
                                        type="button"
                                        class="bg-rose-500 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-rose-700 active:scale-95"
                                    >
                                        <iconify-icon icon="lineicons:trash-3" width="20" height="20"></iconify-icon>
                                    </button>
                                    <livewire:components.modal.guru.modal-hapus-guru/>
                                </div>

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