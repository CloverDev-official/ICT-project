<div class="mb-20 space-y-6">
    
    <!-- BACK -->
    <div>
        <a href="{{ route('laporan-izin') }}" wire:navigate>
            <button
                class="group flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-main hover:text-blue-main hover:shadow-md">

                <iconify-icon
                    icon="lineicons:chevron-left"
                    width="20"
                    height="20"
                    class="transition group-hover:-translate-x-1">
                </iconify-icon>

                Kembali

            </button>
        </a>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        @php
            $labelClass = 'mb-2 block text-sm font-semibold text-gray-700';
            $inputClass = 'w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100';
            $errorClass = 'mt-2 text-sm text-rose-500';
        @endphp
    
    
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-main to-blue-deep p-6">
    
            <div class="flex items-center gap-4">
    
                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 text-white backdrop-blur">
    
                    <iconify-icon
                        icon="solar:document-add-bold"
                        width="28"
                        height="28">
                    </iconify-icon>
    
                </div>
    
                <div>
    
                    <h1 class="text-2xl font-bold text-white">
                        edit Surat Izin Murid
                    </h1>
    
                    <p class="mt-1 text-sm text-blue-100">
                        Pilih murid kemudian isi alasan izin untuk mengedit surat izin.
                    </p>
    
                </div>
    
            </div>
    
        </div>
    
        <!-- Form -->
        <div class="space-y-6 p-6">
    
            <!--  Murid -->
            <div class="relative space-y-6">
                <!-- nama -->
                <div>
                    <label class="{{ $labelClass }}">
                        Nama Lengkap
                    </label>
    
                    <input
                        type="text"
                        wire:model.defer="nama"
                        placeholder="Masukkan nama lengkap"
                        class="{{ $inputClass }} capitalize" />
    
                    @error('nama')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
    
                <!-- nipd -->
                <div>
                    <label class="{{ $labelClass }}">
                        NIPD
                    </label>
    
                    <input
                        type="number"
                        wire:model.defer="nipd"
                        placeholder="Contoh : 1234"
                        class="{{ $inputClass }}" />
    
                    @error('nipd')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
    
                <!-- tanggal -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-gray-700">
                        Tanggal
                    </label>
    
                    <input
                        type="date"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                </div>
    
                <!-- form waktu container -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- dari waktu -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Dari jam
                        </label>
        
                        <input
                            type="time"
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                    </div>
    
                    <!-- sampai waktu -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Sampai jam
                        </label>
        
                        <input
                            type="time"
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                    </div>
    
                </div>
            </div>
    
            <!-- Textarea -->
            <div>
    
                <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                    Keperluan izin
                </label>
    
                <textarea
                    wire:model.defer="alasan"
                    rows="5"k
                    placeholder="Contoh: Mengikuti acara keluarga, pemeriksaan kesehatan, atau keperluan lainnya..."
                    class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-5 py-4 text-sm transition focus:border-blue-main focus:bg-white focus:ring-4 focus:ring-blue-100 focus:outline-none @error('alasan') border-rose-500 @enderror"></textarea>
    
                @error('alasan')
                    <p class="mt-2 text-sm text-rose-500">
                        {{ $message }}
                    </p>
                @enderror
    
            </div>
    
            <!-- Button -->
            <button
                wire:click="cetakIzin"
                class="group flex w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep py-4 text-base font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">
    
                <iconify-icon
                    icon="solar:printer-bold"
                    width="22"
                    height="22"
                    class="transition group-hover:scale-110">
                </iconify-icon>
    
                edit Surat Izin
    
            </button>
    
        </div>
    
    </div>
</div>