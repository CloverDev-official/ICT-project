<div class="mb-20 space-y-6">
    
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
                        Surat Izin Murid
                    </h1>
    
                    <p class="mt-1 text-sm text-blue-100">
                        Pilih murid kemudian isi alasan izin untuk mencetak surat.
                    </p>
    
                </div>
    
            </div>
    
        </div>
    
        <!-- Form -->
        <div class="space-y-6 p-6">
    
            <!--  Murid -->
            <div class="relative space-y-6">
                <!-- filter rombel -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <livewire:components.searchable-select
                    wire:model.live="tingkatId"
                    :options="$tingkatList"
                    value-key="id" label-key="nama"
                    label="Tingkat" placeholder="Cari tingkat..."
                    all-label="Semua tingkat"
                    not-found-text="Pilihan tidak ditemukan." />

                    <livewire:components.searchable-select
                    wire:model.live="jurusanId"
                    :options="$jurusanList"
                    value-key="id" label-key="nama"
                    label="Jurusan" placeholder="Cari jurusan..."
                    all-label="Semua jurusan"
                    not-found-text="Pilihan tidak ditemukan." />

                    <livewire:components.searchable-select
                    wire:model.live="indeksId"
                    :options="$indeksList"
                    value-key="id" label-key="nama"
                    label="Indeks" placeholder="Cari indeks..."
                    all-label="Semua indeks"
                    not-found-text="Pilihan tidak ditemukan." />
                </div>

                <!-- cari murid -->
                <div>
                    <label class="{{ $labelClass }}">
                        Cari Nama atau NIPD
                    </label>

                    <div class="relative">
                        <iconify-icon
                            icon="mdi:account-search-outline"
                            width="20"
                            height="20"
                            class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-blue-main">
                        </iconify-icon>

                        <input
                            type="search"
                            wire:model.live.debounce.250ms="muridSearch"
                            placeholder="Ketik nama murid atau NIPD..."
                            autocomplete="on"
                            class="{{ $inputClass }} pl-12" />
                    </div>

                    @if ($muridSearch !== '' && $muridId === null)
                    <div class="absolute z-50 mt-2 max-h-64 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin">
                        @forelse ($muridSuggestions as $murid)
                        <button
                            type="button"
                            wire:click="selectMurid({{ $murid->id }})"
                            class="flex w-full items-center justify-between gap-4 px-4 py-3 text-left transition hover:bg-blue-main hover:text-white">

                            <span class="min-w-0">
                                <span class="block truncate font-semibold capitalize">{{ $murid->nama }}</span>
                                <span class="mt-1 block truncate text-xs opacity-75">{{ $murid->rombel?->nama_lengkap ?: 'Tanpa kelas' }}</span>
                            </span>

                            <span class="shrink-0 text-xs font-semibold">NIPD: {{ $murid->nipd }}</span>
                        </button>
                        @empty
                        <p class="px-4 py-3 text-sm text-gray-500">
                            Murid atau NIPD tidak ditemukan.
                        </p>
                        @endforelse
                    </div>
                    @endif

                    @error('muridId')
                    <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
    
                <!-- form waktu container -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- dari waktu -->
                    <div>
                        <label
                            class="mb-3 block text-sm font-semibold text-gray-700">
                            Dari jam
                        </label>
        
                        <input
                            type="time"
                            wire:model.defer="dariJam"
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                    </div>
    
                    <!-- sampai waktu -->
                    <div>
                        <form>
                            <div class="flex items-center justify-between">
                                <label
                                    class="mb-2 block text-sm font-semibold text-gray-700">
                                    Sampai jam (Optional)
                                </label>

                                <button
                                    type="reset"
                                    wire:click.prevent="resetJam"
                                    class="mb-2 mr-1 flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700
                                        transition hover:bg-gray-200 active:scale-95">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582M20 20v-5h-.581M5 9a7 7 0 0113.418-2M19 15a7 7 0 01-13.418 2" />
                                    </svg>

                                    Reset
                                </button>
                                
                            </div>

                            <input
                                type="time"
                                wire:model.defer="sampaiJam"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                        </form>
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
                    required
                    maxlength="35"
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
    
                Cetak Surat Izin
    
            </button>
    
        </div>
    
    </div>
</div>
