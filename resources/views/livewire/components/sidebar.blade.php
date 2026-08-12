<div>
    @php
        $user = auth()->user();
        $siteSettings = $siteSettings ?? [];
        $siteLogo = \App\Models\Setting::resolveAssetUrl($siteSettings['logo'] ?? null, asset('assets/img/logo_smkn_2.png'));
        $siteName = $siteSettings['nama_website'] ?? 'ICT Absensi';
        $siteCopyright = $siteSettings['copyright'] ?? 'SMKN 2 Banjarmasin © 2026';
    @endphp
    <aside
        class="fixed md:static top-0 right-0 z-50 flex h-screen w-64 md:w-80 flex-col p-1 rounded-tl-4xl md:rounded-tl-none md:rounded-tr-4xl border-r border-white/10 bg-gradient-to-b from-blue-deep to-[#03152d] shadow-[8px_0_30px_rgba(0,0,0,0.15)] transition-transform duration-300"
        :class="openside ? 'translate-x-0' : 'translate-x-full md:translate-x-0'"
    >
        <!-- header -->
        <div class="flex items-center justify-start pl-5 md:pl-10 gap-4 pt-4">
            <img src="{{ $siteLogo }}" class="w-10" alt="">

            <div>
                <h1 class="text-start text-sm font-semibold uppercase text-white text-shadow-2xs">
                    {{ $siteName }}
                </h1>

                <p class="hidden text-[10px] uppercase text-gray-400 md:block">
                    smkn 2 banjarmasin
                </p>
            </div>
        </div>

        <hr class="mt-5 text-white">

        <!-- menu -->
        <ul class="mt-5 md:pb-20 pb-40 flex-1 overflow-y-auto pr-0 md:pr-2 scroll-thin space-y-2">
            @if($user?->canAccess('dashboard'))
            <!-- dashboard -->
            <li>
                <x-nav-link href="{{ route('dashboard') }}" icon="dashboard">
                    Dashboard
                </x-nav-link>
            </li>
            @endif

            @if($user?->canAccess('pilih-absen'))
            <!-- pilih absen -->
            <li>
                <x-nav-link href="{{ route('pilih-absen') }}" icon="pilihAbsen">
                    Pilih Absen
                </x-nav-link>
            </li>
            @endif

            <!-- laporan -->
            @if($user?->canAccess('laporan'))
            <li x-data="{open: {{ request()->routeIs(['rekap-*', 'laporan-*', 'riwayat-absen-*', 'cetak-izin-*']) ? 'true' : 'false' }}}">
                <button
                    @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-white hover:bg-blue-deep-solid"
                >
                    <span class="flex items-center gap-2 capitalize">
                        <iconify-icon
                            :icon="open ? 'mdi:folder-text' : 'mdi:folder-text-outline'"
                            width="24"
                            height="24"
                        ></iconify-icon>

                        laporan
                    </span>

                    <iconify-icon
                        class="transition-transform"
                        :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up"
                        width="25"
                        height="24"
                    ></iconify-icon>
                </button>

                <ul
                x-show="open"
                x-transition
                class="ml-4 mt-2 flex flex-col gap-2"
                style="display: none;"
                >   
                    <!-- laporan pengawas -->
                    @if($user?->canAccess('laporan-pengawas'))
                    <li x-data="{open: {{ request()->routeIs(['laporan-*', 'cetak-izin-*']) ? 'true' : 'false' }}}">
                        
                        <button
                            @click="open = !open"
                            class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-white hover:bg-blue-deep-solid"
                        >
                            <span class="flex items-center gap-2 capitalize">
                                <iconify-icon
                                    :icon="open ? 'mdi:folder-eye' : 'mdi:folder-eye-outline'"
                                    width="24"
                                    height="24"
                                ></iconify-icon>

                                pengawas
                            </span>
                            <iconify-icon
                                class="transition-transform"
                                :class="{ 'rotate-180': open }"
                                icon="lineicons:chevron-up"
                                width="25"
                                height="24"
                            ></iconify-icon>
                        </button>

                        <ul
                            x-show="open"
                            x-transition
                            class="ml-4 mt-2 flex flex-col gap-2"
                            style="display: none;"
                        >   
                            {{-- izin keluar --}}
                            <li x-data="{open: {{ request()->routeIs(['laporan-*', 'cetak-izin-*']) ? 'true' : 'false' }}}" >
                                <button
                                    @click="open = !open"
                                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-white hover:bg-blue-deep-solid"
                                >
                                    <span class="flex items-center gap-2 capitalize">
                                        <iconify-icon
                                            :icon="open ? 'mdi:folder-file' : 'mdi:folder-file-outline'"
                                            width="24"
                                            height="24"
                                        ></iconify-icon>
    
                                        izin keluar
                                    </span>
                                    <iconify-icon
                                        class="transition-transform"
                                        :class="{ 'rotate-180': open }"
                                        icon="lineicons:chevron-up"
                                        width="25"
                                        height="24"
                                    ></iconify-icon>
                                </button>
                                {{-- sub menu izin keluar --}}
                                <ul
                                    x-show="open"
                                    x-transition
                                    class="ml-4 mt-2 flex flex-col gap-2"
                                    style="display: none;"
                                >   
                                    {{-- menu laporam izin keluar --}}
                                    @if($user?->canAccess('laporan-izin-keluar'))
                                        <li>
                                            <x-nav-link href="{{ route('laporan-izin-keluar') }}" icon="rekapAbsenMurid">
                                                laporan izin keluar
                                            </x-nav-link>
                                        </li>
                                    @endif
                                    {{-- menu cetak izin keluar --}}
                                    @if($user?->canAccess('cetak-izin-keluar'))
                                        <li>
                                            <x-nav-link href="{{ route('cetak-izin-keluar') }}" icon="cetak-izin">
                                                cetak izin keluar
                                            </x-nav-link>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                            {{-- izin telat --}}
                            <li x-data="{open: {{ request()->routeIs(['laporan-*', 'cetak-izin-*']) ? 'true' : 'false' }}}" >
                                <button
                                    @click="open = !open"
                                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-white hover:bg-blue-deep-solid"
                                >
                                    <span class="flex items-center gap-2 capitalize">
                                        <iconify-icon
                                            :icon="open ? 'mdi:folder-alert' : 'mdi:folder-alert-outline'"
                                            width="24"
                                            height="24"
                                        ></iconify-icon>
    
                                        izin telat
                                    </span>
                                    <iconify-icon
                                        class="transition-transform"
                                        :class="{ 'rotate-180': open }"
                                        icon="lineicons:chevron-up"
                                        width="25"
                                        height="24"
                                    ></iconify-icon>
                                </button>
                                {{-- sub menu izin telat --}}
                                <ul
                                    x-show="open"
                                    x-transition
                                    class="ml-4 mt-2 flex flex-col gap-2"
                                    style="display: none;"
                                >   
                                    {{-- menu cetak izin tekat --}}
                                    @if($user?->canAccess('cetak-izin-telat'))
                                        <li>
                                            <x-nav-link href="{{ route('cetak-izin-telat') }}" icon="rekapAbsenMurid">
                                                cetak izin telat
                                            </x-nav-link>
                                        </li>
                                    @endif                                    
                                </ul>
                            </li>
                        </ul>
                    </li>
                    @endif

                    <!-- rekap absen -->
                    @if($user?->canAccess('rekap-absen'))
                    <li x-data="{open: {{ request()->routeIs('rekap-absen-*') ? 'true' : 'false' }}}">
                        <button
                            @click="open = !open"
                            class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-white hover:bg-blue-deep-solid"
                        >
                            <span class="flex items-center gap-2 capitalize">
                                <iconify-icon
                                    :icon="open ? 'mdi:folder-check' : 'mdi:folder-check-outline'"
                                    width="24"
                                    height="24"
                                ></iconify-icon>

                                rekap absen
                            </span>

                            <iconify-icon
                                class="transition-transform"
                                :class="{ 'rotate-180': open }"
                                icon="lineicons:chevron-up"
                                width="25"
                                height="24"
                            ></iconify-icon>
                        </button>

                        <ul
                            x-show="open"
                            x-transition
                            class="ml-4 mt-2 flex flex-col gap-2"
                            style="display: none;"
                        >
                            @if($user?->canAccess('rekap-absen-murid'))
                            <li>
                                <x-nav-link href="{{ route('rekap-absen-murid') }}" icon="rekapAbsenMurid">
                                    absen murid
                                </x-nav-link>
                            </li>
                            @endif

                            {{-- @if($user?->canAccess('rekap-absen-guru'))
                            <li>
                                <x-nav-link href="{{ route('rekap-absen-guru') }}" icon="rekapAbsenGuru">
                                    absen guru
                                </x-nav-link>
                            </li>
                            @endif --}}
                        </ul>
                    </li>
                    @endif

                    <!-- riwayat -->
                    @if($user?->canAccess('riwayat'))
                    <li x-data="{open: {{ request()->routeIs('riwayat-absen-*') ? 'true' : 'false' }}}">
                        <button
                            @click="open = !open"
                            class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-white hover:bg-blue-deep-solid"
                        >
                            <span class="flex items-center gap-2 capitalize">
                                <iconify-icon
                                    :icon="open ? 'mdi:folder-clock' : 'mdi:folder-clock-outline'"
                                    width="24"
                                    height="24"
                                ></iconify-icon>

                                riwayat absen
                            </span>

                            <iconify-icon
                                class="transition-transform"
                                :class="{ 'rotate-180': open }"
                                icon="lineicons:chevron-up"
                                width="25"
                                height="24"
                            ></iconify-icon>
                        </button>

                        <ul
                            x-show="open"
                            x-transition
                            class="ml-4 mt-2 flex flex-col gap-2"
                            style="display: none;"
                        >
                            @if($user?->canAccess('riwayat-murid'))
                            <li>
                                <x-nav-link href="{{ route('riwayat-absen-murid') }}" icon="riwayatAbsenMurid">
                                    absen murid
                                </x-nav-link>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            <!-- absensi -->
            @if($user?->canAccess('absensi'))
            <li x-data="{open: {{ request()->routeIs('absensi-*') ? 'true' : 'false' }}}">
                <button
                    @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-white hover:bg-blue-deep-solid"
                >
                    <span class="flex items-center gap-2 capitalize">
                        <iconify-icon
                            :icon="open ? 'mdi:folder-edit' : 'mdi:folder-edit-outline'"
                            width="24"
                            height="24"
                        ></iconify-icon>

                        absensi
                    </span>

                    <iconify-icon
                        class="transition-transform"
                        :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up"
                        width="25"
                        height="24"
                    ></iconify-icon>
                </button>

                <ul
                    x-show="open"
                    x-transition
                    class="ml-4 mt-2 flex flex-col gap-2"
                    style="display: none;"
                >
                    @if($user?->canAccess('absensi-murid'))
                    <li>
                        <x-nav-link href="{{ route('absensi-murid') }}" icon="absenMurid">
                            absensi murid
                        </x-nav-link>
                    </li>
                    @endif

                    {{-- @if($user?->canAccess('absensi-guru'))
                    <li>
                        <x-nav-link href="{{ route('absensi-guru') }}" icon="absenGuru">
                            absensi guru
                        </x-nav-link>
                    </li>
                    @endif --}}
                </ul>
            </li>
            @endif

            <!-- data -->
            @if($user?->canAccess('data-master'))
            <li x-data="{open: {{ request()->routeIs('data-*') ? 'true' : 'false' }}}">
                <button
                    @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-white hover:bg-blue-deep-solid"
                >
                    <span class="flex items-center gap-2 capitalize">
                        <iconify-icon
                            :icon="open ? 'mdi:folder-account' : 'mdi:folder-account-outline'"
                            width="24"
                            height="24"
                        ></iconify-icon>

                        data master
                    </span>

                    <iconify-icon
                        class="transition-transform"
                        :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up"
                        width="25"
                        height="24"
                    ></iconify-icon>
                </button>

                <ul
                    x-show="open"
                    x-transition
                    class="ml-4 mt-2 flex flex-col gap-2"
                    style="display: none;"
                >
                    @if($user?->canAccess('data-murid'))
                    <li>
                        <x-nav-link href="{{ route('data-murid') }}" icon="dataMurid">
                            data murid
                        </x-nav-link>
                    </li>
                    @endif

                    @if($user?->canAccess('data-guru'))
                    <li>
                        <x-nav-link href="{{ route('data-guru') }}" icon="dataGuru">
                            data guru
                        </x-nav-link>
                    </li>
                    @endif

                    @if($user?->canAccess('data-kelas'))
                    <li>
                        <x-nav-link href="{{ route('data-kelas') }}" icon="dataKelas">
                            data kelas
                        </x-nav-link>
                    </li>
                    @endif

                    @if($user?->canAccess('data-jurusan'))
                    <li>
                        <x-nav-link href="{{ route('data-jurusan') }}" icon="dataJurusan">
                            data jurusan
                        </x-nav-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            <!-- manajemen -->
            @if($user?->canAccess('manajemen'))
            <li x-data="{open: {{ request()->routeIs(['manajemen-waktu', 'manajemen-murid', 'generate-QR', 'manajemen-user', 'manajemen-tahun-ajaran', 'manajemen-role']) ? 'true' : 'false' }}}">
                <button
                    @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-white hover:bg-blue-deep-solid"
                >
                    <span class="flex items-center gap-2 capitalize">
                        <iconify-icon
                            :icon="open ? 'mdi:folder-cog' : 'mdi:folder-cog-outline'"
                            width="24"
                            height="24"
                        ></iconify-icon>

                        manajemen
                    </span>

                    <iconify-icon
                        class="transition-transform"
                        :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up"
                        width="25"
                        height="24"
                    ></iconify-icon>
                </button>

                <ul
                    x-show="open"
                    x-transition
                    class="ml-4 mt-2 flex flex-col gap-2"
                    style="display: none;"
                >
                    @if($user?->canAccess('manajemen-waktu'))
                    <li>
                        <x-nav-link href="{{ route('manajemen-waktu') }}" icon="manajemenWaktu">
                            waktu
                        </x-nav-link>
                    </li>
                    @endif

                    @if($user?->canAccess('manajemen-murid'))
                    <li>
                        <x-nav-link href="{{ route('manajemen-foto-murid') }}" icon="manajemenMurid">
                            Foto Murid
                        </x-nav-link>
                    </li>
                    @endif

                    @if($user?->canAccess('generate-qr'))
                    <li>
                        <x-nav-link href="{{ route('generate-QR') }}" icon="manajemenQR">
                            generate QR
                        </x-nav-link>
                    </li>
                    @endif

                    @if($user?->canAccess('manajemen-role'))
                    <li>
                        <x-nav-link href="{{ route('manajemen-role') }}" icon="manajemenRole">
                            role akses
                        </x-nav-link>
                    </li>
                    @endif

                    @if($user?->canAccess('manajemen-lainnya'))
                    <li>
                        <x-nav-link href="{{ route('manajemen-tahun-ajaran') }}" icon="manajemenTahunAjaran">
                            tahun ajaran
                        </x-nav-link>
                    </li>

                    <li>
                        <x-nav-link href="{{ route('manajemen-user') }}" icon="manajemenUser">
                            user
                        </x-nav-link>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if($user?->canAccess('pengaturan'))
                <!-- pengaturan -->
                <li>
                    <x-nav-link href="{{ route('pengaturan') }}" icon="pengaturan">
                        Pengaturan
                    </x-nav-link>
                </li>
            @endif
        </ul>

        <!-- footer -->
        <div class="fixed bottom-0 bg-[#03152E] w-full max-w-75 shrink-0 border-t border-white/10 py-2">
            <a href="{{ route('profil') }}" wire:navigate>
                <div class="flex md:hidden items-center gap-2 p-2 px-5">
                    <div class="cursor-pointer w-8 h-8 rounded-full bg-white shadow-sm overflow-hidden flex items-center justify-center">
                        @if ($user?->profile_photo_path)
                            <img
                                src="{{ $user->profile_photo_url }}"
                                alt="Profile"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <iconify-icon
                                icon="lineicons:user-4"
                                width="25"
                                height="24"
                                class="text-blue-deep-solid"
                            ></iconify-icon>
                        @endif
                    </div>

                    <div class="flex flex-col">
                        <h1 class="text-sm font-semibold capitalize text-white">
                            {{ auth()->user()->name }}
                        </h1>

                        <p class="text-xs capitalize text-gray-200">
                            {{ auth()->user()->role->name }}
                        </p>
                    </div>
                </div>
            </a>

            <p class="mt-4 text-center text-[8px] uppercase text-white">
                {{ $siteCopyright }}
            </p>
        </div>
    </aside>

    <!-- overlay mobile -->
    <div
        x-show="openside"
        x-transition.opacity
        @click="openside = false"
        style="display: none;"
        class="fixed inset-0 z-40 bg-black/40 md:hidden"
    ></div>
</div>