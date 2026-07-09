<div class="fixed inset-0 bg-blue-dark flex flex-col overflow-y-auto scroll-hidden font-sans">

    <header
        class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/80 px-4 py-4 shadow-2xl backdrop-blur-xl">

        <div class="mx-auto flex items-center max-w-7xl justify-between gap-4">

            <!-- brand -->
            <div class="flex items-center gap-4">

                <img
                    src="{{ asset('assets/img/logo_smkn_2.png') }}"
                    class="w-10"
                    alt="Logo SMKN 2 Banjarmasin">

                <div>
                    <h1 class="text-sm font-bold uppercase tracking-wide text-white">
                        Absensi QR
                    </h1>

                    <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400">
                        SMKN 2 Banjarmasin
                    </p>
                </div>

            </div>

            <!-- title -->
            <div class="hidden text-center md:block">

                <div
                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-5 py-2 text-sm font-semibold text-blue-100 backdrop-blur">

                    <div class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></div>
                    <span class="text-white">
                        {{ $dateNow ?? 'Tanggal sekarang' }},
                    </span>
                    <span class="text-white" id="clock">
                        {{ now()->format('H:i:s') ?? 'Waktu sekarang' }}
                    </span>

                </div>

            </div>

            <!-- back -->
            <a href="{{ route('pilih-absen') }}">

                <button
                    class="group flex items-center gap-2 rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm font-semibold text-white shadow-lg backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/20 active:scale-95">

                    <iconify-icon
                        icon="lineicons:chevron-left"
                        width="18"
                        height="18"
                        class="transition group-hover:-translate-x-1">
                    </iconify-icon>

                    Kembali

                </button>

            </a>

        </div>

    </header>

    <main class="flex items-center justify-center p-4">

        <!-- SCANNER AREA -->
        <div class="w-full max-w-2xl">

            <!-- mobile label -->
            <div class="mb-5 block text-center md:hidden">

                <div
                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-5 py-2 text-sm font-semibold text-blue-100 backdrop-blur">

                    <div class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></div>

                    Absen Masuk

                </div>

                <p class="mt-2 text-xs text-slate-400">
                    {{ $dateNow ?? 'Tanggal sekarang' }}
                </p>

            </div>

            <!-- scanner card -->
            <div
                class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 p-4 shadow-2xl backdrop-blur-xl">
                <!-- reader wrapper -->
                <div class="relative aspect-square w-full overflow-hidden rounded-[1.6rem] bg-black shadow-2xl">

                    <!-- corner frame -->
                    <div class="pointer-events-none absolute inset-0 z-20">
                        <div class="absolute left-4 top-4 h-12 w-12 rounded-tl-2xl border-l-4 border-t-4 border-blue-400"></div>
                        <div class="absolute right-4 top-4 h-12 w-12 rounded-tr-2xl border-r-4 border-t-4 border-blue-400"></div>
                        <div class="absolute bottom-4 left-4 h-12 w-12 rounded-bl-2xl border-b-4 border-l-4 border-blue-400"></div>
                        <div class="absolute bottom-4 right-4 h-12 w-12 rounded-br-2xl border-b-4 border-r-4 border-blue-400"></div>
                    </div>

                    <!-- center helper -->
                    <div
                        class="pointer-events-none absolute left-1/2 top-1/2 z-20 h-[58%] w-[58%] -translate-x-1/2 -translate-y-1/2 rounded-3xl border border-blue-400/40 bg-blue-400/5">
                    </div>

                    <!-- scan line -->
                    <div
                        class="pointer-events-none absolute left-[22%] top-1/2 z-20 h-0.5 w-[56%] -translate-y-1/2 animate-pulse rounded-full bg-blue-400 shadow-[0_0_25px_rgba(96,165,250,0.9)]">
                    </div>

                    <!-- camera -->
                    <div
                        wire:ignore
                        id="reader"
                        class="min-h-full overflow-hidden rounded-[1.6rem] bg-black text-white">
                    </div>

                </div>

                <!-- helper text -->
                <div class="mt-4 text-center">

                    <p class="text-sm font-medium text-slate-200">
                        Posisikan QR Code di tengah kotak scanner
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Sistem akan memproses otomatis setelah QR terbaca.
                    </p>

                </div>

            </div>

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
                        <img src="{{ asset('assets/img/logo_smkn_2.png') }}" class="w-10 h-10" alt="">
                        <div>
                            <h2 class="font-bold leading-tight">Absensi Murid</h2>
                            <p class="text-xs opacity-80">SMKN 2 Banjarmasin</p>
                        </div>
                    </div>
                    <div class="text-right text-xs opacity-80">
                        <p>ID: {{ $murid->uuid }}</p>
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

    @if($scanStatus === 'message' && $scanMessage)
    <div id="error-modal" class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
        <div class="relative flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="bg-green-600 px-6 py-4 text-white">
                    <h2 class="text-lg font-bold">Izin Berhasil Diperbarui</h2>
                    <p class="text-xs opacity-80">Sistem mengecek jadwal kelas dari Manajemen Waktu.</p>
                </div>
                <div class="p-6 text-sm text-gray-700">
                    <p class="font-semibold text-gray-900">{{ $scanMessage }}</p>
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
    function updateClock() {
        const now = new Date();

        const formatted = now.toLocaleString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });

        document.getElementById('clock').textContent = formatted;
    }

    updateClock();
    setInterval(updateClock, 1000);


    import('{{ Vite::asset('resources/js/scanner.js') }}')
    .then(() => {
        window.initScanner()
        document.addEventListener('scanSuccess', () => {
            window.destroyScanner();

            setTimeout(() => {
                $wire.set('tersimpan', false);
                $wire.set('murid', null);
                window.scanned = false;
                window.initScanner();
            }, 5500);
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
            }, 5500);
        });

        document.addEventListener('scanMessage', () => {
            window.destroyScanner();

            setTimeout(() => {
                $wire.set('scanStatus', null);
                $wire.set('scanMessage', null);
                $wire.set('murid', null);
                window.scanned = false;
                window.initScanner();
            }, 5500);
        });
    });
</script>
@endscript