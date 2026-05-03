<div>
    <!-- ================= STAT CARD ================= -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-8">

        <!-- JUMLAH GURU -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Jumlah Guru</p>
                <h3 class="text-2xl font-semibold text-gray-800">80</h3>
            </div>

            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <iconify-icon icon="mdi:account-group" width="24"></iconify-icon>
            </div>
        </div>

        <!-- HADIR -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Hadir</p>
                <h3 class="text-2xl font-semibold text-gray-800">65</h3>
            </div>

            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <iconify-icon icon="mdi:check-circle" width="24"></iconify-icon>
            </div>
        </div>

        <!-- SAKIT -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Sakit</p>
                <h3 class="text-2xl font-semibold text-gray-800">10</h3>
            </div>

            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-yellow-50 text-yellow-600">
                <iconify-icon icon="mdi:emoticon-sick-outline" width="24"></iconify-icon>
            </div>
        </div>

        <!-- IZIN -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Izin</p>
                <h3 class="text-2xl font-semibold text-gray-800">5</h3>
            </div>

            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                <iconify-icon icon="mdi:file-document-outline" width="24"></iconify-icon>
            </div>
        </div>

        <!-- PERSENTASE -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Persentase</p>
                <h3 class="text-2xl font-semibold text-emerald-600">85%</h3>
            </div>

            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <iconify-icon icon="mdi:chart-donut" width="24"></iconify-icon>
            </div>
        </div>

    </div>
    <div class="grid grid-cols-1 gap-8">
        
        <!-- ================= CHART ================= -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800">
                    Rekap Kehadiran Guru
                </h2>
    
                <span class="text-xs text-gray-400">
                    Statistik Kehadiran
                </span>
            </div>
    
            <!-- Chart -->
            <div class="p-6">
                <div id="chart-rekap-absen-guru" class="w-full h-96"></div>
            </div>
    
        </div>
    
    
        <!-- ================= DOWNLOAD ================= -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
    
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100">
                <h1 class="text-xl font-semibold text-gray-800">
                    Unduh Laporan Kehadiran
                </h1>
                <p class="text-sm text-gray-400">
                    Unduh laporan kehadiran Guru berdasarkan tanggal dan kelas
                </p>
            </div>
    
    
            <!-- Content -->
            <div class="p-6 space-y-6">
    
                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">
                        Pilih Tanggal
                    </label>
    
                    <input 
                        type="date"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl 
                        focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                        outline-none transition">
                </div>
    
    
                <!-- Kelas -->
                <div 
                x-data="{                
                    open:false,
                    selected:'',
                    select(item){
                        this.selected=item
                        this.open=false
                    }
                }"
                class="relative">
    
                    <label class="block text-sm font-medium text-gray-600 mb-2">
                        Pilih Kelas
                    </label>
    
                    <!-- Button -->
                    <div 
                    @click="open = !open"
                    class="flex items-center justify-between px-4 py-2.5 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
    
                        <span x-text="selected ? selected : 'Semua Kelas'" class="text-sm text-gray-700"></span>
    
                        <iconify-icon
                            icon="lineicons:chevron-down"
                            width="18"
                            class="text-gray-400 transition"
                            :class="{'rotate-180':open}">
                        </iconify-icon>
    
                    </div>
    
    
                    <!-- Dropdown -->
                    <div
                    x-show="open"
                    x-transition
                    @click.outside="open=false"
                    class="absolute mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-52 overflow-y-auto z-50">
    
                        @foreach ( [ 'Semua Kelas', 'X PPLG A', 'X PPLG B', 'XI PPLG A', 'XI PPLG B', 'XII PPLG A', 'XII PPLG B' ] as $kelas )
    
                        <div
                        @click="select('{{ $kelas }}')"
                        class="flex items-center justify-between px-4 py-2.5 text-sm cursor-pointer hover:bg-blue-50 hover:text-blue-600 transition">
    
                            <span>{{ $kelas }}</span>
    
                            <iconify-icon 
                            x-show="selected === '{{ $kelas }}'"
                            icon="lineicons:check"
                            width="18">
                            </iconify-icon>
    
                        </div>
    
                        @endforeach
    
                    </div>
    
                </div>
    
    
                <!-- Divider -->
                <div class="border-t border-gray-100 pt-6">
    
                    <p class="text-sm text-gray-500 mb-4">
                        Pilih format file untuk mengunduh laporan
                    </p>
    
                    <!-- Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    
                        <button class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-medium transition">
                            <iconify-icon icon="mdi:file-word" width="18"></iconify-icon>
                            DOCX
                        </button>
    
                        <button class="flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl text-sm font-medium transition">
                            <iconify-icon icon="mdi:file-pdf-box" width="18"></iconify-icon>
                            PDF
                        </button>
    
                        <button class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl text-sm font-medium transition">
                            <iconify-icon icon="mdi:file-excel" width="18"></iconify-icon>
                            Excel
                        </button>
    
                    </div>
    
                </div>
    
            </div>
    
        </div>
    
    </div>

    <!-- TABLE MURID TIDAK HADIR -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-8">

        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800">
                Murid Tidak Hadir Hari Ini
            </h2>
            <p class="text-sm text-gray-400">
                Temukan murid yang tidak hadir berdasarkan tingkat, jurusan dan kelas
            </p>
        </div>
        <!-- category -->
        <div class="flex justify-end m-4 items-center gap-4">
            <!-- STATUS -->
            <div 
                x-data="{
                    open: false,
                    selectedId: null,
                    selectedLabel: 'Semua status',

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
                        @click.prevent="select(null, 'Semua status')"
                        class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition"
                    >
                        <span>Semua status</span>
                        <iconify-icon 
                            x-show="selectedId === null" 
                            icon="lineicons:check" 
                            width="20" 
                            height="20">
                        </iconify-icon>
                    </div>

                    <!-- list -->
                    @foreach (['sakit', 'izin', 'sakit'] as $status)
                        <div
                            @click.prevent="select('{{ $status }}', '{{ $status }}')"
                            class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition"
                        >
                            <span>{{ $status }}</span>

                            <iconify-icon 
                                x-show="selectedId === '{{ $status }}'" 
                                icon="lineicons:check" 
                                width="20" 
                                height="20">
                            </iconify-icon>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Jenis PTK -->
            <div 
                x-data="{
                    open: false,
                    selectedId: null,
                    selectedLabel: 'Semua jenis PTK',

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
                    <span x-text="selectedLabel" class="text-gray-700 line-clamp-1"></span>

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
                    class="absolute mt-2 w-full max-h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50"
                >
                    <!-- semua -->
                    <div
                        @click.prevent="select(null, 'Semua Jenis PTK')"
                        class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition "
                    >
                        <span>Semua Jenis PTK</span>
                        <iconify-icon 
                            x-show="selectedId === null" 
                            icon="lineicons:check" 
                            width="20" 
                            height="20">
                        </iconify-icon>
                    </div>

                    <!-- list -->
                    @foreach (['Mapel', 'BK'] as $jenis)
                        <div
                            @click.prevent="select('{{ $jenis }}', '{{ $jenis }}')"
                            class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition"
                        >
                            <span>{{ $jenis }}</span>

                            <iconify-icon 
                                x-show="selectedId === '{{ $jenis }}'" 
                                icon="lineicons:check" 
                                width="20" 
                                height="20">
                            </iconify-icon>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- input search -->
            <div class="relative w-40 md:w-sm">
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

            <table class="w-full text-sm">

                <thead class="bg-blue-main text-white">
                    <tr  >
                        <th class="border-gray-400 px-6 py-3">Nama</th>
                        <th class="border-gray-400 px-6 py-3">Jenis PTK</th>
                        <th class="border-gray-400 px-6 py-3">Status</th>
                    </tr>
                </thead>

                <tbody>

                    <tr class="text-center hover:bg-gray-100" >
                        <td class="border-r border-gray-200 px-6 py-4">Budi</td>
                        <td class="border-r border-gray-200 px-6 py-4">Mapel</td>
                        <td class="px-6 py-4 text-blue-500 font-semibold">Sakit</td>
                    </tr>

                    <tr class="text-center bg-gray-50 hover:bg-gray-100" >
                        <td class="border-r border-gray-200 px-6 py-4">Andi</td>
                        <td class="border-r border-gray-200 px-6 py-4">BK</td>
                        <td class="px-6 py-4 text-amber-500 font-semibold">Izin</td>
                    </tr>

                    <tr class="text-center hover:bg-gray-100" >

                        <td class="border-r border-gray-200 px-6 py-4">Anda</td>
                        <td class="border-r border-gray-200 px-6 py-4">Mapel</td>
                        <td class="px-6 py-4 text-rose-500 font-semibold">Alfa</td>
                    </tr>

                </tbody>

            </table>
        </div>
    </div>
</div>