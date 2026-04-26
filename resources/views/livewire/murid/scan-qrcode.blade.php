<div class="fixed inset-0 bg-slate-900 flex flex-col overflow-hidden font-sans">
    
    <header class="w-full p-4 bg-slate-800 shadow-md flex justify-between items-center z-10">
        <h1 class="text-white font-bold text-xl tracking-tight">QR<span class="text-blue-400">Scan</span></h1>
        <div class="flex items-center space-x-2">
            <span class="flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-gray-300 text-xs font-medium uppercase tracking-widest">Live</span>
        </div>
    </header>

    <main class="flex-1 relative flex items-center justify-center p-4">
        <div class="w-full max-w-lg aspect-square relative">
            <div class="absolute -top-2 -left-2 w-8 h-8 border-t-4 border-l-4 border-blue-500 rounded-tl-lg z-20"></div>
            <div class="absolute -top-2 -right-2 w-8 h-8 border-t-4 border-r-4 border-blue-500 rounded-tr-lg z-20"></div>
            <div class="absolute -bottom-2 -left-2 w-8 h-8 border-b-4 border-l-4 border-blue-500 rounded-bl-lg z-20"></div>
            <div class="absolute -bottom-2 -right-2 w-8 h-8 border-b-4 border-r-4 border-blue-500 rounded-br-lg z-20"></div>

            <div wire:ignore id="reader" class="overflow-hidden rounded-xl bg-black shadow-2xl"></div>
        </div>

        <div class="absolute bottom-10 left-0 right-0 text-center">
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
                    <div class="bg-gradient-to-r from-blue-700 to-blue-500 text-white px-6 py-3 flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-lg leading-tight">KARTU PELAJAR</h2>
                            <p class="text-xs opacity-80">Identitas Siswa</p>
                        </div>
                        <div class="text-right text-xs opacity-80">
                            <p>ID: {{ $murid->ulid }}</p>
                        </div>
                    </div>

                    <!-- BODY -->
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

</div>


@script
    <script>
        window.initScanner()
        document.addEventListener('scanSuccess', () => {
            setTimeout(() => {
                // reset state Livewire
                $wire.set('tersimpan', false);
                $wire.set('murid', null);

                // optional: aktifkan scan lagi
                window.scanned = false;
            }, 2000);
        });
    </script>
@endscript
