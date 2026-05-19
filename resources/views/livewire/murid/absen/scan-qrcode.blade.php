<div class="fixed inset-0 bg-blue-dark flex flex-col overflow-y-auto scroll-hidden font-sans">
    
    <header class="w-full p-4 bg-slate-900 shadow-md flex justify-between items-center z-10">
        <div class="flex items-center justify-center gap-4">
            <img src="{{ asset('assets/img/logo_smkn_2.png')}}" class="w-10" alt="">
            <div>
                <h1 class="bg-blue- text-start text-white text-shadow-2xs text-sm font-semibold uppercase">
                    Absensi QR
                </h1>
                <p class="text-[10px] text-gray-400 uppercase">smkn 2 banjarmasin</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <h1 class=" md:text-lg font-normal text-white">
                Absen Masuk {{ $dateNow ?? 'Tanggal sekarang' }} <!-- namanya sesuiakan sama yang dipilih di pilih absen -->
            </h1>
        </div>
        <!-- BACK BUTTON -->
        <a href="{{ route('pilih-absen') }}">
            <button 
                class="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 transition px-4 py-2 rounded-xl text-white text-sm shadow"
            >
                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="w-4 h-4" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor">
                    <path stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M15 19l-7-7 7-7" />
                </svg>
    
                Kembali
            </button>
        </a>
    </header>

    <main class="flex-1 relative flex items-center justify-center p-4">
        <!-- TIPS -->
        <div class="right-10 absolute w-xs px-4 pt-4">
            <div class="bg-slate-900/80 border border-slate-700 rounded-2xl p-4 h-80 text-white shadow-lg">
                
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mt-1">
                        <iconify-icon class="text-yellow-400" icon="lets-icons:lamp-fill" width="24" height="24"></iconify-icon>
                    </div>

                    <div>
                        <h2 class="font-semibold text-lg uppercase tracking-wide">
                            Tips Penggunaan
                        </h2>
                        <p class="text-sm text-slate-400">
                            Agar QR berhasil dipindai
                        </p>
                    </div>
                </div>

                <ul class="space-y-2 text-slate-300 mt-4">
                    <li class="flex items-start gap-2">
                        <span>✔</span>
                        <span>Pastikan QR Code terlihat jelas dan tidak buram</span>
                    </li>

                    <li class="flex items-start gap-2">
                        <span>✔</span>
                        <span>Arahkan kamera tepat ke tengah kotak scanner</span>
                    </li>

                    <li class="flex items-start gap-2">
                        <span>✔</span>
                        <span>Gunakan pencahayaan yang cukup</span>
                    </li>

                    <li class="flex items-start gap-2">
                        <span>✔</span>
                        <span>Jangan terlalu dekat atau terlalu jauh dari kamera</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="w-full max-w-lg aspect-square relative">
            <div class="absolute -top-2 -left-2 w-8 h-8 border-t-4 border-l-4 border-blue-500 rounded-tl-lg z-20"></div>
            <div class="absolute -top-2 -right-2 w-8 h-8 border-t-4 border-r-4 border-blue-500 rounded-tr-lg z-20"></div>
            <div class="absolute -bottom-2 -left-2 w-8 h-8 border-b-4 border-l-4 border-blue-500 rounded-bl-lg z-20"></div>
            <div class="absolute -bottom-2 -right-2 w-8 h-8 border-b-4 border-r-4 border-blue-500 rounded-br-lg z-20"></div>
            
            <div wire:ignore id="reader" class="overflow-hidden rounded-xl bg-black min-h-full shadow-2xl text-white"></div>
        </div>

        <div class="absolute text-center">
            <p class="text-slate-400 text-sm animate-pulse">Posisikan QR Code di tengah kotak</p>
        </div>
    </main>

    {{-- @if ($tersimpan)
        <p>murid: {{ $murid->nama }}</p>
        <p>Kelas: {{ $murid->rombel->nama_lengkap }}</p>
    @endif --}}

    @if($tersimpan)
        <div id="success-modal" class="fixed inset-0 z-50">
            <!-- overlay -->
            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>

            <!-- container -->
            <div class="relative flex items-center justify-center min-h-screen p-4">
                
                <!-- CARD -->
                <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">

                    <!-- HEADER -->
                    <div class="bg-linear-to-r from-blue-deep to-blue-deep-solid text-white px-6 py-3 flex items-center justify-between">
                        <div class="flex items-center justify-start gap-2">
                            <img src="{{ asset('assets/img/logo_smkn_2.png') }}" class="w-10 h-10"  alt="">
                            <div>
                                <h2 class="font-bold leading-tight">Absensi Murid</h2>
                                <p class="text-xs opacity-80">SMKN 2 Banjarmasin</p>
                            </div>
                        </div>
                        <div class="text-right text-xs opacity-80">
                            <p>ID: {{ $murid->ulid }}</p>
                        </div>
                    </div>

                    <!-- BODY -->
                    <div class="px-6 pt-5">
                        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            <p class="font-semibold">{{ $scanMessage ?? 'Absensi berhasil disimpan.' }}</p>
                            @if(!empty($jadwalHariIni))
                                <p class="mt-1 text-xs">
                                    Jadwal: {{ $jadwalHariIni['label'] ?? '-' }}
                                    @if(!empty($jadwalHariIni['jam_masuk']) || !empty($jadwalHariIni['jam_pulang']))
                                        · {{ $jadwalHariIni['jam_masuk'] ?? '-' }} - {{ $jadwalHariIni['jam_pulang'] ?? '-' }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 flex gap-6">

                        <!-- FOTO -->
                        <div class="flex-shrink-0">
                            <img src="{{ $murid->image_path }}" 
                                class="w-32 h-40 object-cover rounded-xl border shadow">
                        </div>

                        <!-- DATA -->
                        <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">

                            <div>
                                <p class="text-gray-500 text-xs">Nama</p>
                                <p class="font-semibold text-gray-900">{{ $murid->nama }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">Kelas</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $murid->rombel->nama_lengkap ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">NISN</p>
                                <p class="font-semibold">{{ $murid->nisn }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">NIPD</p>
                                <p class="font-semibold">{{ $murid->nipd }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">Tempat, Tanggal Lahir</p>
                                <p class="font-semibold">
                                    {{ $murid->tempat_lahir }},
                                    {{ \Carbon\Carbon::parse($murid->tanggal_lahir)->format('d M Y') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">Jenis Kelamin</p>
                                <p class="font-semibold">{{ $murid->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">Agama</p>
                                <p class="font-semibold">{{ $murid->agama ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 text-xs">No HP</p>
                                <p class="font-semibold">{{ $murid->hp ?? '-' }}</p>
                            </div>

                            <!-- FULL WIDTH -->
                            <div class="col-span-2">
                                <p class="text-gray-500 text-xs">Alamat</p>
                                <p class="font-semibold leading-snug">
                                    {{ $murid->alamat ?? '-' }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="bg-gray-100 px-6 py-2 flex justify-between text-xs text-gray-600">
                        <p>Dicetak oleh sistem</p>
                        <p>{{ now()->format('d M Y') }}</p>
                    </div>

                </div>
            </div>
        </div>
    @endif

    @if($scanStatus === 'error' && $scanMessage)
        <div id="error-modal" class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
            <div class="relative flex min-h-screen items-center justify-center p-4">
                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <div class="bg-red-600 px-6 py-4 text-white">
                        <h2 class="text-lg font-bold">Absensi Tidak Disimpan</h2>
                        <p class="text-xs opacity-80">Sistem mengecek jadwal kelas dari Manajemen Waktu.</p>
                    </div>
                    <div class="p-6 text-sm text-gray-700">
                        <p class="font-semibold text-gray-900">{{ $scanMessage }}</p>

                        @if($murid)
                            <div class="mt-4 rounded-2xl bg-gray-50 p-4">
                                <p><span class="text-gray-500">Nama:</span> <span class="font-semibold">{{ $murid->nama }}</span></p>
                                <p><span class="text-gray-500">Kelas:</span> <span class="font-semibold">{{ $murid->rombel->nama_lengkap ?? '-' }}</span></p>
                            </div>
                        @endif

                        @if(!empty($jadwalHariIni))
                            <div class="mt-4 rounded-2xl border border-red-100 bg-red-50 p-4 text-red-800">
                                <p class="font-semibold">{{ $jadwalHariIni['label'] ?? '-' }}</p>
                                @if(!empty($jadwalHariIni['nama_acara']))
                                    <p class="text-xs">{{ $jadwalHariIni['nama_acara'] }}</p>
                                @endif
                                @if(!empty($jadwalHariIni['keterangan']))
                                    <p class="text-xs">{{ $jadwalHariIni['keterangan'] }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>


@script
    <script>
        window.initScanner()
        document.addEventListener('scanSuccess', () => {
            window.destroyScanner();

            setTimeout(() => {
                $wire.set('tersimpan', false);
                $wire.set('murid', null);
                window.scanned = false;
                window.initScanner();
            }, 1500);
        });

        document.addEventListener('scanNotFound', () => {
            window.scanned = false;
        });

        document.addEventListener('scanRejected', () => {
            window.destroyScanner();

            setTimeout(() => {
                $wire.set('scanStatus', null);
                $wire.set('scanMessage', null);
                $wire.set('murid', null);
                window.scanned = false;
                window.initScanner();
            }, 2500);
        });
    </script>
@endscript
