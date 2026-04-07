<div class="grid grid-cols-1 gap-5 lg:gap-10" >
    <!-- jumlah -->
    <div class="grid grid-cols-1 md:grid-cols-2  lg:grid-cols-4 gap-4">
        <!-- jumlah murid -->
        <div class="w-full p-6 rounded-2xl bg-linear-to-t/hsl from-blue-deep-solid  to-blue-main  shadow-lg flex items-center justify-start md:justify-center gap-8">
            <div class="order-2 md:order-1" >
                <h4 class="text-white font-extrabold text-xl">1606 <!-- jumlah murid --> </h4>
                <p class="text-gray-300 text-sm font-semibold capitalize">jumlah murid</p>
            </div>
            <div class="order-1 md:order-2 w-12 h-12 p-5 rounded-full bg-blue-main text-white flex items-center justify-center shadow-md">
                <iconify-icon icon="line-md:account-small" width="24" height="24"></iconify-icon>
            </div>
        </div>
        <!-- jumlah guru -->
        <div class="w-full p-6 rounded-2xl bg-linear-to-t/hsl from-blue-deep-solid  to-blue-main shadow-lg flex items-center justify-start md:justify-center gap-8">
            <div class="order-2 md:order-1" >
                <h4 class="text-white font-extrabold text-xl">450 <!-- jumlah guru --> </h4>
                <p class="text-gray-300 text-sm font-semibold capitalize">jumlah guru</p>
            </div>
            <div class="order-1 md:order-2 w-12 h-12 p-5 rounded-full bg-blue-main text-white flex items-center justify-center shadow-md">
                <iconify-icon icon="line-md:account" width="24" height="24"></iconify-icon>
            </div>
        </div>
        <!-- jumlah kelas dan jurusan -->
        <div class="w-full p-6 rounded-2xl bg-linear-to-t/hsl from-blue-deep-solid  to-blue-main shadow-lg flex items-center justify-start md:justify-center gap-8">
            <div class="order-2 md:order-1" >
                <h4 class="text-white font-extrabold text-xl">55 / 8 <!-- jumlah kelas dan jurusan --> </h4>
                <p class="text-gray-300 text-sm font-semibold capitalize">jumlah kelas & jurusan</p>
            </div>
            <div class="order-1 md:order-2 w-12 h-12 p-5 rounded-full bg-blue-main text-white flex items-center justify-center shadow-md">
                <iconify-icon icon="line-md:star" width="24" height="24"></iconify-icon>
            </div>
        </div>
        <div class="w-full p-6 rounded-2xl bg-linear-to-t/hsl from-blue-deep-solid  to-blue-main shadow-lg flex items-center justify-start md:justify-center gap-8">
            <div class="order-2 md:order-1" >
                <h4 class="text-white font-extrabold text-xl">2 <!-- jumlah murid --> </h4>
                <p class="text-gray-300 text-sm font-semibold capitalize">jumlah petugas</p>
            </div>
            <div class="order-1 md:order-2 w-12 h-12 p-5 rounded-full bg-blue-main text-white flex items-center justify-center shadow-md">
                <iconify-icon icon="line-md:cog-loop" width="24" height="24"></iconify-icon>
            </div>
        </div>
    </div>
    <!-- container absensi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- absensi murid -->
        <div class="bg-white rounded-2xl shadow-md p-6">
        
            <!-- Header -->
            <div class=" flex flex-col lg:flex-row items-start lg:items-center lg:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Absensi Murid Hari Ini</h2>
                    <p class="text-sm text-gray-500">Pantau kehadiran Murid | {{ $dateNow ?? 'Tanggal sekarang' }}</p>
                </div>

                <div x-data="selectClass()" class="relative w-40">
    
                    <!-- Button -->
                    <div @click="open = !open"
                        class="flex items-center justify-between px-4 py-2 bg-white border border-gray-300 rounded-xl cursor-pointer  hover:border-blue-500 transition">
                        
                        <span x-text="selected ? selected : 'Semua Kelas'" class="text-gray-700 text-sm"></span>

                        <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                    </div>

                    <!-- Dropdown -->
                    <div x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute mt-2 w-full h-52 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto z-50 scroll-thin">

                        <template x-for="item in classes" :key="item">
                            <div @click="select(item)"
                                class="px-4 py-2  cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                                
                                <span x-text="item"></span>

                                <!-- icon check -->                                
                                <iconify-icon x-show="selected === item"  icon="lineicons:check" width="24" height="24"></iconify-icon>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
            <!-- Chart untuk murid -->
            <div id="main-murid" class="h-52"></div>
        </div>
        <!-- absensi guru -->
        <div class="bg-white rounded-2xl shadow-md p-6">
        
            <!-- Header -->
            <div class=" flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Absensi Guru Hari Ini</h2>
                    <p class="text-sm text-gray-500">Pantau kehadiran guru | {{ $dateNow ?? 'Tanggal sekarang' }}</p>
                </div>
            </div>
            <!-- Chart untuk guru -->
            <div id="main-guru" class="h-52"></div>

        </div>
    </div>
    <!-- container tingkat kehadiran -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- tingkat kehadiran murid -->
        <div class="bg-white rounded-2xl shadow-md p-6">
        
            <!-- Header -->
            <div class=" flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Tingkat Kehadiran Murid</h2>
                    <p class="text-sm text-gray-500">Statistik kehadiran 7 hari terakhir | {{ $dateNow ?? 'Tanggal sekarang' }}</p>
                </div>
            </div>
            <!-- Chart untuk guru -->
            <div id="chart-tingkat-kehadiran-murid" class="h-64"></div>
            <a wire:navigate href="{{ route('data-guru') }}" class="relative flex items-end transition-colors duration-200 hover:text-blue-deep-solid" >
                <p class="font-semibold text-sm" >Lihat data</p>
                <iconify-icon class="absolute top-[2px] left-[3.8rem] rotate-180" icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
            </a>            
        </div>
        <!-- tingakt headiran guru -->
        <div class="bg-white rounded-2xl shadow-md p-6">
        
            <!-- Header -->
            <div class=" flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Tingkat Kehadiran Guru</h2>
                    <p class="text-sm text-gray-500">Statistik kehadiran 7 hari terakhir | {{ $dateNow ?? 'Tanggal sekarang' }}</p>
                </div>
            </div>
            <!-- Chart untuk guru -->
            <div id="chart-tingkat-kehadiran-guru" class="h-64"></div>
            <a wire:navigate href="{{ route('data-guru') }}" class="relative flex items-center  transition-colors duration-200 hover:text-blue-deep-solid" >
                <p class="font-semibold text-sm" >Lihat data</p>
                <iconify-icon class="absolute top-[2px] left-[3.8rem] rotate-180" icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
            </a>
        </div>
    </div>
    <script>
        function selectClass() {
            return {
                open: false,
                selected: '',
                classes: [
                    'Semua Kelas',
                    'X PPLG A',
                    'X PPLG B',
                    'XI PPLG A',
                    'XI PPLG B',
                    'XII PPLG A',
                    'XII PPLG B'
                ],
                select(item) {
                    this.selected = item
                    this.open = false
                }
            }
        }
    </script>
</div>
