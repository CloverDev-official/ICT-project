<div>
    <!-- TABLE DAFTAR PETUGAS -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-8">

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
                    placeholder="Cari Nama Murid..."
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

                    <tr class="text-center hover:bg-gray-100" >
                        <td class="border-r border-gray-200 px-6 py-4">1</td>
                        <td class="border-r border-gray-200 px-6 py-4">op-skenda</td>
                        <td class="border-r border-gray-200 px-6 py-4">Dodi</td>
                        <td class="border-r border-gray-200 px-6 py-4">operator</td>
                        <td class="px-6 py-4 flex items-center justify-center">
                            <p class="shadow-xs px-6 py-1 text-green-500 bg-green-100 rounded-full font-semibold capitalize">
                                aktif
                            </p>
                        </td>
                        <td class="border-r border-gray-200 px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button class="flex items-center justify-center p-2 rounded-xl bg-blue-500  shadow-xs text-white" >
                                        <iconify-icon icon="lineicons:ban-2" width="24" height="24"></iconify-icon>
                                </button>
                                <button class="flex items-center justify-center p-2 rounded-xl bg-amber-400  shadow-xs text-white" >
                                        <iconify-icon icon="lineicons:pencil-1" width="24" height="24"></iconify-icon>
                                </button>
                                <button class="flex items-center justify-center p-2 rounded-xl bg-rose-500  shadow-xs text-white" >
                                        <iconify-icon icon="lineicons:trash-3" width="24" height="24"></iconify-icon>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr class="text-center bg-gray-50 hover:bg-gray-100" >
                        <td class="border-r border-gray-200 px-6 py-4">2</td>
                        <td class="border-r border-gray-200 px-6 py-4 capitalize">wakel@gmail.com</td>
                        <td class="border-r border-gray-200 px-6 py-4 capitalize">sincung</td>
                        <td class="border-r border-gray-200 px-6 py-4 capitalize">wali kelas</td>
                        <td class="px-6 py-4 flex items-center justify-center">
                            <p class="shadow-xs px-6 py-1 text-green-500 bg-green-100 rounded-full font-semibold capitalize">
                                aktif
                            </p>
                        </td>
                        <td class="border-r border-gray-200 px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button class="flex items-center justify-center p-2 rounded-xl bg-blue-500  shadow-xs text-white" >
                                        <iconify-icon icon="lineicons:ban-2" width="24" height="24"></iconify-icon>
                                </button>
                                <button class="flex items-center justify-center p-2 rounded-xl bg-amber-400  shadow-xs text-white" >
                                        <iconify-icon icon="lineicons:pencil-1" width="24" height="24"></iconify-icon>
                                </button>
                                <button class="flex items-center justify-center p-2 rounded-xl bg-rose-500  shadow-xs text-white" >
                                        <iconify-icon icon="lineicons:trash-3" width="24" height="24"></iconify-icon>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr class="text-center hover:bg-gray-100" >

                        <td class="border-r border-gray-200 px-6 py-4">3</td>
                        <td class="border-r border-gray-200 px-6 py-4 capitalize">kepsek@gmail.com</td>
                        <td class="border-r border-gray-200 px-6 py-4 capitalize">bambang</td>
                        <td class="border-r border-gray-200 px-6 py-4 capitalize">pengawas</td>
                        <td class="px-6 py-4 flex items-center justify-center">
                            <p class="shadow-xs px-6 py-1 text-green-500 bg-green-100 rounded-full font-semibold capitalize">
                                aktif
                            </p>
                        </td>
                        <td class="border-r border-gray-200 px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button class="flex items-center justify-center p-2 rounded-xl bg-blue-500  shadow-xs text-white" >
                                        <iconify-icon icon="lineicons:ban-2" width="24" height="24"></iconify-icon>
                                </button>
                                <button class="flex items-center justify-center p-2 rounded-xl bg-amber-400  shadow-xs text-white" >
                                        <iconify-icon icon="lineicons:pencil-1" width="24" height="24"></iconify-icon>
                                </button>
                                <button class="flex items-center justify-center p-2 rounded-xl bg-rose-500  shadow-xs text-white" >
                                        <iconify-icon icon="lineicons:trash-3" width="24" height="24"></iconify-icon>
                                </button>
                            </div>
                        </td>
                    </tr>

                </tbody>

            </table>
        </div>
    </div>
</div>
