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
                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Nama Lengkap</label>
                    <input type="text" disabled wire:model="nama" class="{{ $inputClass }} capitalize" />
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">NIPD</label>
                    <input type="text" disabled wire:model="nipd" class="{{ $inputClass }}" />
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">Tanggal</label>
                    <input type="date" wire:model.defer="tanggal" class="{{ $inputClass }}" />
                    @error('tanggal')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 md:col-span-2">
                    <div>
                        <label class="{{ $labelClass }}">Dari jam</label>
                        <input type="time" wire:model.defer="dariJam" class="{{ $inputClass }}" />
                        @error('dariJam')
                            <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label class="{{ $labelClass }}">Sampai jam (Optional)</label>

                            <button
                                type="button"
                                wire:click="$set('sampaiJam', null)"
                                class="mb-2 mr-1 flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-200 active:scale-95">

                                Reset

                            </button>
                        </div>

                        <input type="time" wire:model.defer="sampaiJam" class="{{ $inputClass }}" />
                        @error('sampaiJam')
                            <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                    Keperluan izin
                </label>

                <textarea
                    wire:model.defer="alasan"
                    rows="5"
                    required
                    maxlength="35"
                    placeholder="Contoh: Mengikuti acara keluarga, pemeriksaan kesehatan, atau keperluan lainnya..."
                    class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-5 py-4 text-sm transition focus:border-blue-main focus:bg-white focus:ring-4 focus:ring-blue-100 focus:outline-none @error('alasan') border-rose-500 @enderror"></textarea>

                @error('alasan')
                    <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="group flex w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep py-4 text-base font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">

                <iconify-icon
                    icon="solar:pen-bold"
                    width="22"
                    height="22"
                    class="transition group-hover:scale-110">
                </iconify-icon>

                Simpan Perubahan

            </button>

        </form>

    </div>
</div>
