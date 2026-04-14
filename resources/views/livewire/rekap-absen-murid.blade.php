<div>
    <!-- ================= STAT CARD ================= -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-8">

        <!-- JUMLAH MURID -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Jumlah Murid</p>
                <h3 class="text-2xl font-semibold text-gray-800">120</h3>
            </div>

            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <iconify-icon icon="mdi:account-group" width="24"></iconify-icon>
            </div>
        </div>

        <!-- HADIR -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Hadir</p>
                <h3 class="text-2xl font-semibold text-gray-800">100</h3>
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
                <h3 class="text-2xl font-semibold text-gray-800">10</h3>
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
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        <!-- ================= CHART ================= -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">
                    Rekap Kehadiran Murid
                </h2>
    
                <span class="text-xs text-gray-400">
                    Statistik Kehadiran
                </span>
            </div>
    
            <!-- Chart -->
            <div class="p-6">
                <div id="chart-rekap-absen-murid" class="w-full h-96"></div>
            </div>
    
        </div>
    
    
        <!-- ================= DOWNLOAD ================= -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100">
                <h1 class="text-lg font-semibold text-gray-800">
                    Unduh Laporan Kehadiran
                </h1>
                <p class="text-sm text-gray-400 mt-1">
                    Unduh laporan kehadiran murid berdasarkan tanggal dan kelas
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

    <!-- KEHADIRAN PER KELAS -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-8">

        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">
                Kehadiran per Kelas
            </h2>
        </div>

        <div class="p-6">
            <div id="chart-kehadiran-kelas" class="w-full h-96"></div>
        </div>

    </div>

    <!-- TABLE MURID TIDAK HADIR -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-8">

        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">
                Murid Tidak Hadir Hari Ini
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-blue-main text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">Nama</th>
                        <th class="px-6 py-3 text-left">Kelas</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    <tr>
                        <td class="px-6 py-4">Budi</td>
                        <td class="px-6 py-4">X PPLG A</td>
                        <td class="px-6 py-4 text-yellow-600 font-medium">Sakit</td>
                    </tr>

                    <tr>
                        <td class="px-6 py-4">Andi</td>
                        <td class="px-6 py-4">XI PPLG B</td>
                        <td class="px-6 py-4 text-orange-600 font-medium">Izin</td>
                    </tr>

                </tbody>

            </table>
        </div>
    </div>
</div>