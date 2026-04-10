<div>
    <!-- dekstop -->
    <aside class="hidden md:flex flex-col w-64 bg-blue-deep p-4 pb-0 rounded-tr-4xl h-screen shadow-[6px_0_15px_rgba(0,0,0,0.1)]" >
        <div class="flex items-center justify-center gap-4 pt-4">
            <img src="{{ asset('assets/img/logo_smkn_2.png')}}" class="w-10" alt="">
            <div>
                <h1 class="bg-blue- text-start text-white text-shadow-2xs text-sm font-semibold uppercase">
                    Operator Petugas Absensi
                </h1>
                <p class="text-[10px] text-gray-400 uppercase">smkn 2 banjarmasin</p>
            </div>
        </div>
        <hr class="text-white mt-5" >
        <ul class="mt-5 flex-1 flex flex-col gap-2 overflow-y-auto pr-2 scroll-thin">

            <!-- dashboard -->
            <li>
                <x-nav-link href="{{ route('dashboard') }}" icon="dashboard">
                    Dashboard
                </x-nav-link>
            </li>

            <!-- group laporan -->
            <li x-data="{open: {{ request()->routeIs('rekap-*') ? 'true' : 'false' }}}" >
                <!-- button -->
                <button 
                    @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-2 text-white hover:bg-blue-deep-solid rounded-lg"
                >
                    <span class="flex gap-2 items-center capitalize">
                        <iconify-icon :icon="open ? 'mdi:folder-text' : 'mdi:folder-text-outline'" width="24" height="24"></iconify-icon>
                        laporan
                    </span>

                    <iconify-icon class="transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </button>
            </li>

            <!-- Group Absensi  -->
            <li x-data="{open: {{ request()->routeIs('absensi-*') ? 'true' : 'false' }}}" >

                <!-- button -->
                <button 
                    @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-2 text-white hover:bg-blue-deep-solid rounded-lg"
                >
                    <span class="flex gap-2 items-center capitalize">
                        <iconify-icon :icon="open ? 'mdi:folder-check' : 'mdi:folder-check-outline'" width="24" height="24"></iconify-icon>
                        Absensi
                    </span>

                    <iconify-icon class="transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </button>

                <!-- list -->
                <ul x-show="open" x-transition class="ml-4 mt-2 flex flex-col gap-2" style="display: none;" >
                    <li>
                        <x-nav-link href="{{ route('absensi-murid') }}" icon="absenMurid">
                            absensi murid
                        </x-nav-link>
                    </li>
                
                    <li>
                        <x-nav-link href="{{ route('absensi-guru') }}" icon="absenGuru">
                            absensi guru
                        </x-nav-link>
                    </li>
                </ul>
            </li>

            <!-- GROUP DATA -->
            <li x-data="{ open: {{ request()->routeIs('data-*') ? 'true' : 'false' }} }">

                <button 
                    @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-2 text-white hover:bg-blue-deep-solid rounded-lg"
                >
                    <span class="flex gap-2 items-center capitalize">
                        <iconify-icon :icon="open ? 'mdi:folder-account' : 'mdi:folder-account-outline'" width="24" height="24"></iconify-icon> 
                        Data Master
                    </span>

                    <iconify-icon class="transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </button>

                <!-- list -->
                <ul x-show="open" x-transition class="ml-4 mt-2 flex flex-col gap-2" style="display: none;">

                    <li>
                        <x-nav-link href="{{ route('data-murid') }}" icon="dataMurid">
                            data murid
                        </x-nav-link>
                    </li>

                    <li>
                        <x-nav-link href="{{ route('data-guru') }}" icon="dataGuru">
                            data guru
                        </x-nav-link>
                    </li>

                    <li>
                        <x-nav-link href="{{ route('data-kelas') }}" icon="dataKelas">
                            data kelas 
                        </x-nav-link>
                    </li>

                    <li>
                        <x-nav-link href="{{ route('data-jurusan') }}" icon="dataJurusan">
                            data jurusan
                        </x-nav-link>
                    </li>

                </ul>

            </li>

            <!-- group pengaturan -->
            <li x-data="{open: {{ request()->routeIs('rekap-*') ? 'true' : 'false' }}}" >
                <!-- button -->
                <button 
                    @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-2 text-white hover:bg-blue-deep-solid rounded-lg"
                >
                    <span class="flex gap-2 items-center capitalize">
                        <iconify-icon :icon="open ? 'mdi:folder-cog' : 'mdi:folder-cog-outline'" width="24" height="24"></iconify-icon>
                        pengaturan
                    </span>

                    <iconify-icon class="transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </button>
            </li>
        </ul>
        <div class="p-4" >
            <p class="text-[8px] text-center text-white uppercase">smkn 2 banjarmasin &copy; 2026 - {{ $yearNow }}</p>
        </div>
    </aside>

    <!-- mobile -->
    <aside  class="flex md:hidden">
            <!-- overlay -->
            <div 
                x-show="openside"
                @click="open = false; $dispatch('sidebar-toggle', open)"
                style="display: none;"
                x-transition.opacity
                class="fixed z-40 inset-0 bg-black/40">
            </div>

            <div 
                x-show="openside"
                x-transition:enter="transition transform duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition transform duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                @click.outside="openside = false"
                style="display: none;"
                class="top-0 right-0 absolute w-[15rem]  z-50  bg-blue-deep p-4 rounded-tl-4xl flex flex-col h-screen"
            >
                <div class="flex items-center justify-center gap-4 pt-4">
                    <img src="{{ asset('assets/img/logo_smkn_2.png')}}" class="w-10" alt="">
                    <h1 class="bg-blue- text-start text-white text-shadow-2xs text-sm font-semibold uppercase">
                        Operator Petugas Absensi
                    </h1>
                </div>
                <hr class="text-white mt-5" >
                <ul class="mt-5 flex flex-col gap-2 flex-1 overflow-y-auto scroll-thin" >

                    <!-- dashboard -->
                    <li>
                        <x-nav-link href="{{ route('dashboard') }}" icon="dashboard">
                            Dashboard
                        </x-nav-link>
                    </li>

                    <!-- group laporan -->
                    <li x-data="{open: {{ request()->routeIs('rekap-*') ? 'true' : 'false' }}}" >
                        <!-- button -->
                        <button 
                            @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-2 text-white hover:bg-blue-deep-solid rounded-lg"
                        >
                            <span class="flex gap-2 items-center capitalize">
                                <iconify-icon :icon="open ? 'mdi:folder-text' : 'mdi:folder-text-outline'" width="24" height="24"></iconify-icon>
                                laporan
                            </span>

                            <iconify-icon class="transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                        </button>
                    </li>

                    <!-- Group Absensi  -->
                    <li x-data="{open: {{ request()->routeIs('absensi-*') ? 'true' : 'false' }}}" >

                        <!-- button -->
                        <button 
                            @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-2 text-white hover:bg-blue-deep-solid rounded-lg"
                        >
                            <span class="flex gap-2 items-center capitalize">
                                <iconify-icon :icon="open ? 'mdi:folder-check' : 'mdi:folder-check-outline'" width="24" height="24"></iconify-icon>
                                Absensi
                            </span>

                            <iconify-icon class="transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                        </button>

                        <!-- list -->
                        <ul x-show="open" x-transition class="ml-4 mt-2 flex flex-col gap-2" style="display: none;" >
                            <li>
                                <x-nav-link href="{{ route('absensi-murid') }}" icon="absenMurid">
                                    absensi murid
                                </x-nav-link>
                            </li>
                        
                            <li>
                                <x-nav-link href="{{ route('absensi-guru') }}" icon="absenGuru">
                                    absensi guru
                                </x-nav-link>
                            </li>
                        </ul>
                    </li>

                    <!-- group data -->
                    <li x-data="{open: {{ request()->routeIs('data-*') ? 'true' : 'false' }}}" >

                        <!-- button -->
                        <button 
                        @click="open = !open"
                        class="flex items-center justify-between w-full px-4 py-2 text-white hover:bg-blue-deep-solid rounded-lg"    
                        >
                            <span class="flex gap-2 items-center capitalize">
                                <iconify-icon icon="lineicons:folder" width="20" height="20"></iconify-icon> 
                                Data Master
                            </span>

                            <iconify-icon class="transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                        </button>

                        <!-- list -->
                        <ul  x-show="open" x-transition class="ml-4 mt-2 flex flex-col gap-2" style="display: none;">
                            <li>
                                <x-nav-link href="{{ route('data-murid') }}" icon="dataMurid">
                                    data murid
                                </x-nav-link>
                            </li>
                            <li>
                                <x-nav-link href="{{ route('data-guru') }}" icon="dataGuru">
                                    data guru
                                </x-nav-link>
                            </li>
                            <li>
                                <x-nav-link href="{{ route('data-kelas') }}" icon="dataKelas">
                                    data kelas
                                </x-nav-link>
                            </li>
                            <li>
                                <x-nav-link href="{{ route('data-jurusan') }}" icon="dataJurusan">
                                    data Jurusan
                                </x-nav-link>
                            </li>
                        </ul>
                    </li>

                    <!-- group pengaturan -->
                    <li x-data="{open: {{ request()->routeIs('rekap-*') ? 'true' : 'false' }}}" >
                        <!-- button -->
                        <button 
                            @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-2 text-white hover:bg-blue-deep-solid rounded-lg"
                        >
                            <span class="flex gap-2 items-center capitalize">
                                <iconify-icon :icon="open ? 'mdi:folder-cog' : 'mdi:folder-cog-outline'" width="24" height="24"></iconify-icon>
                                pengaturan
                            </span>

                            <iconify-icon class="transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                        </button>
                    </li>
                </ul>
                <hr class="text-white mb-5" >
                <div class="flex gap-2  justify-start items-center" >
                    <div class="w-8 h-8 rounded-full bg-white shadow-sm text-blue-deep-solid  flex items-center justify-center ">
                        <iconify-icon icon="lineicons:user-4" width="25" height="24"></iconify-icon>
                    </div>
                    <div class="flex flex-col gap-0" >
                        <h1 class="text-sm font-semibold capitalize text-white" >nama user</h1>
                        <p class="text-xs capitalize text-gray-100" >role user</p>
                    </div>
                </div>
            </div>
    </aside>
</div>
