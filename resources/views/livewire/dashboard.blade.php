<div class="grid grid-cols-1 gap-10" >
    <!-- jumlah -->
    <div class="grid grid-cols-1 md:grid-cols-2  lg:grid-cols-4 gap-4">
        <!-- jumlah murid -->
        <div class="w-full p-6 rounded-2xl bg-linear-to-t/hsl from-blue-deep-solid  to-blue-main  shadow-lg flex items-center justify-center gap-8">
            <div>
                <h4 class="text-white font-extrabold text-xl">1606 <!-- jumlah murid --> </h4>
                <p class="text-gray-300 text-sm font-semibold capitalize">jumlah murid</p>
            </div>
            <div class="w-12 h-12 p-5 rounded-full bg-blue-main text-white flex items-center justify-center shadow-md">
                <iconify-icon icon="line-md:account-small" width="24" height="24"></iconify-icon>
            </div>
        </div>
        <!-- jumlah guru -->
        <div class="w-full p-6 rounded-2xl bg-linear-to-t/hsl from-blue-deep-solid  to-blue-main shadow-lg flex items-center justify-center gap-8">
            <div>
                <h4 class="text-white font-extrabold text-xl">450 <!-- jumlah murid --> </h4>
                <p class="text-gray-300 text-sm font-semibold capitalize">jumlah murid</p>
            </div>
            <div class="w-12 h-12 p-5 rounded-full bg-blue-main text-white flex items-center justify-center shadow-md">
                <iconify-icon icon="line-md:account" width="24" height="24"></iconify-icon>
            </div>
        </div>
        <!-- jumlah kelas dan jurusan -->
        <div class="w-full p-6 rounded-2xl bg-linear-to-t/hsl from-blue-deep-solid  to-blue-main shadow-lg flex items-center justify-center gap-8">
            <div>
                <h4 class="text-white font-extrabold text-xl">55 / 8 <!-- jumlah kelas dan jurusan --> </h4>
                <p class="text-gray-300 text-sm font-semibold capitalize">jumlah kelas & jurusan</p>
            </div>
            <div class="w-12 h-12 p-5 rounded-full bg-blue-main text-white flex items-center justify-center shadow-md">
                <iconify-icon icon="line-md:star" width="24" height="24"></iconify-icon>
            </div>
        </div>
        <div class="w-full p-6 rounded-2xl bg-linear-to-t/hsl from-blue-deep-solid  to-blue-main shadow-lg flex items-center justify-center gap-8">
            <div>
                <h4 class="text-white font-extrabold text-xl">2 <!-- jumlah murid --> </h4>
                <p class="text-gray-300 text-sm font-semibold capitalize">jumlah petugas</p>
            </div>
            <div class="w-12 h-12 p-5 rounded-full bg-blue-main text-white flex items-center justify-center shadow-md">
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
                <!-- Select Kelas -->
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option>Semua Kelas</option>
                    <option>X PPLG A</option>
                    <option>X PPLG A</option>
                    <option>XI PPLG A</option>
                </select>
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
            <div id="chart-tingkat-kehadiran-murid" class="h-52"></div>
            <a href="{{ route('data-guru') }}" class="flex items-end gap-1 transition-colors duration-200 hover:text-blue-deep-solid" >
                <iconify-icon icon="line-md:check-list-3-filled" width="24" height="24"></iconify-icon>
                <p class="font-semibold text-sm" >Lihat data</p>
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
            <div id="chart-tingkat-kehadiran-guru" class="h-52"></div>
            <a href="{{ route('data-guru') }}" class="flex items-end gap-1 transition-colors duration-200 hover:text-blue-deep-solid" >
                <iconify-icon icon="line-md:check-list-3-filled" width="24" height="24"></iconify-icon>
                <p class="font-semibold text-sm" >Lihat data</p>
            </a>
        </div>
    </div>
</div>
