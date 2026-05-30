<div>
    @php
        $user = auth()->user();
    @endphp
    <aside
        class="fixed md:static top-0 right-0 z-50 flex h-screen w-64 md:w-72 flex-col rounded-tl-4xl md:rounded-tl-none md:rounded-tr-4xl border-r border-white/10 bg-gradient-to-b from-blue-deep to-[#03152d] p-4 pb-2 shadow-[8px_0_30px_rgba(0,0,0,0.15)] transition-transform duration-300"
        :class="openside ? 'translate-x-0' : 'translate-x-full md:translate-x-0'"
    >
        <!-- header -->
        <div class="flex items-center justify-center gap-4 pt-4">
            <img src="{{ asset('assets/img/logo_smkn_2.png')}}" class="w-10" alt="">

            <div>
                <h1 class="text-start text-sm font-semibold uppercase text-white text-shadow-2xs">
                    Operator Petugas Absensi
                </h1>

                <p class="hidden text-[10px] uppercase text-gray-400 md:block">
                    smkn 2 banjarmasin
                </p>
            </div>
        </div>

        <hr class="mt-5 text-white">

        <!-- menu -->
        <ul class="my-5 flex flex-1 flex-col gap-2 overflow-y-auto pr-0 md:pr-2 scroll-thin">

            <!-- dashboard -->
            <li>
                <x-nav-link href="{{ route('dashboard') }}" icon="dashboard">
                    Dashboard
                </x-nav-link>
            </li>

            <!-- pilih absen -->
            <li>
                <x-nav-link href="{{ route('pilih-absen') }}" icon="pilihAbsen">
                    Pilih Absen
                </x-nav-link>
            </li>

            <!-- laporan -->
            @if($user?->canAccess('laporan'))
            <li x-data="{open: {{ request()->routeIs(['rekap-*', 'laporan-*', 'riwayat-absen-*']) ? 'true' : 'false' }}}">
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

                @if($user?->canAccess('rekap-absen'))
                <ul
                    x-show="open"
                    x-transition
                    class="ml-4 mt-2 flex flex-col gap-2"
                    style="display: none;"
                >
                    <!-- rekap absen -->
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

                            @if($user?->canAccess('rekap-absen-guru'))
                            <li>
                                <x-nav-link href="{{ route('rekap-absen-guru') }}" icon="rekapAbsenGuru">
                                    absen guru
                                </x-nav-link>
                            </li>
                            @endif
                        </ul>
                    </li>

                    <!-- riwayat -->
                    @if($user?->canAccess('riwayat'))
                    <li x-data="{open: {{ request()->routeIs('riwayat-absen-*') ? 'true' : 'false' }}}">
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
                @endif
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
                            :icon="open ? 'mdi:folder-check' : 'mdi:folder-check-outline'"
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

                    @if($user?->canAccess('absensi-guru'))
                    <li>
                        <x-nav-link href="{{ route('absensi-guru') }}" icon="absenGuru">
                            absensi guru
                        </x-nav-link>
                    </li>
                    @endif
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
            <li x-data="{open: {{ request()->routeIs(['manajemen-waktu', 'manajemen-murid', 'generate-QR', 'manajemen-user', 'manajemen-tahun-ajaran']) ? 'true' : 'false' }}}">
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
                        <x-nav-link href="{{ route('manajemen-murid') }}" icon="manajemenMurid">
                            murid
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

            <!-- pengaturan -->
            <li>
                <x-nav-link href="{{ route('pengaturan') }}" icon="pengaturan">
                    Pengaturan
                </x-nav-link>
            </li>
        </ul>

        <!-- footer -->
        <div class="border-t border-white/10 py-4">
            <div class="flex md:hidden items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-blue-deep-solid shadow-sm">
                    <iconify-icon icon="lineicons:user-4" width="20" height="20"></iconify-icon>
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

            <p class="mt-4 text-center text-[8px] uppercase text-white">
                smkn 2 banjarmasin &copy; 2026
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