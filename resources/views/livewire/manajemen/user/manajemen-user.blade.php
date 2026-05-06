<div>
    <div class="flex items-center justify-start gap-5">
        <!-- btn tambah data siswa -->
        <a href="{{ route('tambah-user') }}" wire:navigate>
            <button
                class="px-4 py-2 rounded-lg bg-emerald-600 text-white transition-all duration-200 hover:bg-emerald-700 active:scale-95 flex items-center justify-center gap-1 capitalize ">
                <iconify-icon icon="line-md:plus" width="20" height="20"></iconify-icon>
                tambah user
            </button>
        </a>

        <!-- btn import CSV -->
        <div x-data="{ openModalImport: false }" >
            <button
                @click="openModalImport = true"
                class="px-4 py-2 rounded-lg bg-blue-main text-white transition-all duration-200 hover:bg-blue-deep-solid active:scale-95 flex items-center justify-center gap-1 capitalize ">
                <iconify-icon icon="line-md:file-import" width="20" height="20"></iconify-icon>
                import CSV
            </button>
            <!-- modal import murid  -->
            <livewire:components.modal.manajemen.user.modal-import-user/>
        </div>
    </div>
    <!-- TABLE DAFTAR PETUGAS -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-5">

        <!-- category -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-4 pt-4">
            <!-- ROLE -->
            <div 
                x-data="{
                    open: false,
                    selectedId: null,
                    selectedLabel: 'Semua Role',

                    select(id, label){
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false
                    },

                    toggle(){
                        this.open = !this.open
                    }
                }"
                class="relative w-full"
            >
                <!-- trigger -->
                <div 
                    @click="toggle()"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition"
                >
                    <span x-text="selectedLabel" class="text-gray-700"></span>

                    <iconify-icon 
                        class="text-gray-400 transition-transform" 
                        :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" 
                        width="20" 
                        height="20">
                    </iconify-icon>
                </div>

                <!-- dropdown -->
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="absolute mt-2 w-full max-h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50"
                >
                    <!-- semua -->
                    <div
                        @click.prevent="select(null, 'Semua role')"
                        class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition"
                    >
                        <span>Semua role</span>
                        <iconify-icon 
                            x-show="selectedId === null" 
                            icon="lineicons:check" 
                            width="20" 
                            height="20">
                        </iconify-icon>
                    </div>

                    <!-- list -->
                    @foreach (['operator', 'wali kelas', 'pengawas'] as $role)
                        <div
                            @click.prevent="select('{{ $role }}', '{{ $role }}')"
                            class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition"
                        >
                            <span>{{ $role }}</span>

                            <iconify-icon 
                                x-show="selectedId === '{{ $role }}'" 
                                icon="lineicons:check" 
                                width="20" 
                                height="20">
                            </iconify-icon>
                        </div>
                    @endforeach
                </div>
            </div>

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
                    transition"
                />

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
                        <th class="border-gray-400 px-6 py-3">UserName</th>
                        <th class="border-gray-400 px-6 py-3">nama guru</th>
                        <th class="border-gray-400 px-6 py-3">Role</th>
                        <th class="border-gray-400 px-6 py-3">Status</th>
                        <th class="border-gray-400 px-6 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ([
                        ['nama' => 'dodi', 'email' => 'op-skenda@gmail.com', 'role' => 'operator', 'status' => 'akitf'],
                        ['nama' => 'sincung', 'email' => 'wakel@gmail.com', 'role' => 'wali kelas', 'status' => 'nonaktif'],
                        ['nama' => 'bambang', 'email' => 'kepsek@gmail.com', 'role' => 'pengawas', 'status' => 'akitf'],
                    ] as $item)
                        @php
                            $statusValue = strtolower((string) $item['status']);
                            $statusClass = match ($statusValue) {
                                'nonaktif' => 'text-rose-500 bg-rose-100',
                                default => 'text-green-500 bg-green-100',
                            };
                        @endphp
                        <tr class="text-center hover:bg-gray-100" >
                            <td class="border-r border-gray-200 px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="border-r border-gray-200 px-6 py-4">{{$item['nama']}}</td>
                            <td class="border-r border-gray-200 px-6 py-4">{{ $item['email'] }}</td>
                            <td class="border-r border-gray-200 px-6 py-4">{{ $item['role'] }}</td>
                            <td class="border-r border-gray-200 px-6 py-4 flex items-center justify-center">
                                <p class="text-xs shadow-xs px-6 py-1 rounded-full font-semibold capitalize {{ $statusClass }}">
                                    {{ $item['status'] }}
                                </p>
                            </td>
                            <td class="border-r border-gray-200">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="w-8 h-8 flex items-center justify-center p-2 rounded-xl bg-blue-500  shadow-xs text-white transition-all duration-150 hover:bg-blue-600" >
                                            <iconify-icon icon="lineicons:ban-2" width="20" height="20"></iconify-icon>
                                    </button>
                                    <a href="{{ route('edit-user') }}">
                                        <button class="w-8 h-8 flex items-center justify-center p-2 rounded-xl bg-amber-400  shadow-xs text-white transition-all duration-150 hover:bg-amber-500" >
                                                <iconify-icon icon="lineicons:pencil-1" width="20" height="20"></iconify-icon>
                                        </button>
                                    </a>
                                    <div x-data="{ openModalDelete: false }">
                                        <button @click="openModalDelete = true" class="w-8 h-8 flex items-center justify-center p-2 rounded-xl bg-rose-500  shadow-xs text-white transition-all duration-150 hover:bg-rose-600" >
                                                <iconify-icon icon="lineicons:trash-3" width="20" height="20"></iconify-icon>
                                        </button>
                                        <livewire:components.modal.manajemen.user.modal-hapus-user/>
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
