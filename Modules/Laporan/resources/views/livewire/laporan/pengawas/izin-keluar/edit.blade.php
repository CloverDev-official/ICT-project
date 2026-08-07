<div class="mb-20 space-y-6">

    @php
        $labelClass = 'mb-2 block text-sm font-semibold text-gray-700';
        $inputClass = 'w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100';
        $errorClass = 'mt-2 text-sm text-rose-500';
    @endphp

    <!-- BACK -->
    <div>
        <a href="{{ route('laporan-izin') }}" wire:navigate>
            <button
                class="group flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-main hover:text-blue-main hover:shadow-md">

                <iconify-icon icon="lineicons:chevron-left" width="20" height="20"
                    class="transition group-hover:-translate-x-1">
                </iconify-icon>

                Kembali

            </button>
        </a>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-main to-blue-deep p-6">

            <div class="flex items-center gap-4">

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 text-white backdrop-blur">

                    <iconify-icon icon="solar:document-add-bold" width="28" height="28">
                    </iconify-icon>

                </div>

                <div>

                    <h1 class="text-2xl font-bold text-white">
                        Edit Surat Izin Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Perbarui data izin yang sudah tersimpan di database.
                    </p>

                </div>

            </div>

        </div>

        <!-- Form -->
        <form wire:submit.prevent="update" class="space-y-6 p-6">

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- nama --}}
                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Nama Lengkap</label>
                    <input type="text" disabled wire:model="nama" class="{{ $inputClass }} capitalize" />
                </div>

                {{-- nipd --}}
                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">NIPD</label>
                    <input type="text" disabled wire:model="nipd" class="{{ $inputClass }}" />
                </div>

                {{-- tanggal --}}
                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Tanggal</label>
                    <input type="date" wire:model.defer="tanggal" class="{{ $inputClass }}" />
                    @error('tanggal')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                {{-- status --}}
                <div>
                    <label class="{{ $labelClass }}">
                        Cari Status
                    </label>

                    <div class="relative">
                        <iconify-icon icon="mdi:clipboard-text-search-outline" width="20" height="20"
                            class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-blue-main">
                        </iconify-icon>

                        <select wire:model.defer="status" class="{{ $inputClass }} pl-12">
                            <option value="">Semua Status</option>
                            <option value="selesai">Selesai</option>
                            <option value="izin">Izin</option>
                        </select>
                    </div>

                    @error('status')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- form waktu container -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- dari waktu -->
                    <div>
                        <label class="mb-3 block text-sm font-semibold text-gray-700">
                            Dari jam
                        </label>

                        <input type="time" wire:model.defer="dariJam"
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                    </div>

                    <!-- sampai waktu -->
                    <div>
                        <form>
                            <div class="flex items-center justify-between">
                                <label class="mb-2 block text-sm font-semibold text-gray-700">
                                    Sampai jam (Optional)
                                </label>

                                <button type="reset" wire:click.prevent="resetJam" class="mb-2 mr-1 flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700
                                        transition hover:bg-gray-200 active:scale-95">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582M20 20v-5h-.581M5 9a7 7 0 0113.418-2M19 15a7 7 0 01-13.418 2" />
                                    </svg>

                                    Reset
                                </button>

                            </div>

                            <input type="time" wire:model.defer="sampaiJam"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                        </form>
                    </div>

                </div>
            </div>
    </div>

    <div>
        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
            Keperluan izin
        </label>

        <textarea wire:model.defer="alasan" rows="5" required maxlength="35"
            placeholder="Contoh: Mengikuti acara keluarga, pemeriksaan kesehatan, atau keperluan lainnya..."
            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-5 py-4 text-sm transition focus:border-blue-main focus:bg-white focus:ring-4 focus:ring-blue-100 focus:outline-none @error('alasan') border-rose-500 @enderror"></textarea>

        @error('alasan')
            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <button wire:click="update"
        class="group flex w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep py-4 text-base font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">

        <iconify-icon icon="solar:pen-bold" width="22" height="22" class="transition group-hover:scale-110">
        </iconify-icon>

        Simpan Perubahan

    </button>

    </form>

</div>
</div>