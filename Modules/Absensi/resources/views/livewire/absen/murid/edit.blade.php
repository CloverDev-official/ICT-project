<div class="mb-20 space-y-6">
    <!-- BACK -->
    <div>
        <a href="{{ route('absensi-murid') }}" wire:navigate>
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
    <!-- HERO -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main via-blue-deep to-[#07162f] p-6 shadow-lg">

        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-cyan-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-5">

                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="fa7-solid:user-edit"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Edit Absen Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Perbarui informasi kehadiran murid.
                    </p>
                </div>

            </div>

            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Murid
                </p>

                <h2 class="mt-1 text-lg font-bold text-white capitalize">
                    {{ $absen->murid->nama ?? 'nama Murid' }}
                </h2>

            </div>

        </div>
    </div>
    <!-- FORM CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- CARD HEADER -->
        <div
            class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Form Edit Absen Murid
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Pastikan semua data sudah benar sebelum menyimpan perubahan.
                </p>
            </div>

            <div
                class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-600">

                <iconify-icon
                    icon="solar:pen-bold"
                    width="18"
                    height="18">
                </iconify-icon>

                Sedang diedit

            </div>

        </div>

        <!-- FORM -->
        <form
            wire:submit.prevent="update"
            class="space-y-8 p-6">

            <!-- SECTION IDENTITAS -->
            <div>

                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">
                        <iconify-icon icon="solar:user-bold" width="22" height="22"></iconify-icon>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Data Kehadiran Murid
                        </h3>

                        <p class="text-sm text-gray-500">
                            Data Kehadiran murid yang tercatat di sistem.
                        </p>
                    </div>
                </div>
                <!-- KEHADIRAN -->
                <div
                    x-data="{
        open: false,
        selected: @js($status ?? null),
        options: @js(\App\Enums\AttendanceStatus::editFormOptions()),

        selectedOption() {
            return this.options.find((option) => option.value === this.selected)
        },

        statusLabel() {
            return this.selectedOption()?.label ?? 'Pilih kehadiran'
        },

        statusClass() {
            return this.selectedOption()?.class ?? 'bg-gray-100 text-gray-500'
        },

        statusIcon() {
            return this.selectedOption()?.icon ?? 'solar:calendar-mark-bold'
        },

        select(value) {
            this.selected = value
            this.open = false

            $wire.set('status', value)
        }
    }"
                    class="relative">

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Kehadiran
                    </label>

                    <!-- trigger -->
                    <div
                        @click="open = !open"
                        class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl"
                                :class="statusClass()">

                                <iconify-icon
                                    :icon="statusIcon()"
                                    width="22"
                                    height="22">
                                </iconify-icon>

                            </div>

                            <div>
                                <p
                                    x-text="statusLabel()"
                                    class="font-semibold text-gray-800">
                                </p>

                                <p class="text-xs text-gray-400">
                                    Status absensi murid
                                </p>
                            </div>

                        </div>

                        <iconify-icon
                            icon="lineicons:chevron-up"
                            width="20"
                            height="20"
                            class="text-gray-400 transition-transform"
                            :class="{ 'rotate-180': open }">
                        </iconify-icon>

                    </div>

                    <!-- dropdown -->
                    <div
                        x-show="open"
                        x-transition
                        @click.outside="open = false"
                        style="display:none"
                        class="absolute z-50 mt-2 w-full overflow-y-scroll scroll-thin max-h-48 rounded-2xl border border-gray-200 bg-white shadow-xl">

                        @foreach (\App\Enums\AttendanceStatus::editFormOptions() as $item)

                        <div
                            @click.prevent="select('{{ $item['value'] }}')"
                            class="flex cursor-pointer items-center justify-between gap-4 px-4 py-3 transition hover:bg-blue-main hover:text-white"
                            :class="selected === '{{ $item['value'] }}' ? 'bg-blue-main text-white' : ''">

                            <div class="flex items-center gap-3">

                                <div
                                    class="{{ $item['class'] }} flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">

                                    <iconify-icon
                                        icon="{{ $item['icon'] }}"
                                        width="21"
                                        height="21">
                                    </iconify-icon>

                                </div>

                                <div>
                                    <h4 class="font-semibold">
                                        {{ $item['label'] }}
                                    </h4>

                                    <p class="text-xs opacity-70">
                                        {{ $item['desc'] }}
                                    </p>
                                </div>

                            </div>

                            <iconify-icon
                                x-show="selected === '{{ $item['value'] }}'"
                                icon="lineicons:check"
                                width="18"
                                height="18">
                            </iconify-icon>

                        </div>

                        @endforeach

                    </div>

                    @error('status')
                    <p class="mt-2 text-sm text-rose-500">
                        {{ $message }}
                    </p>
                    @enderror

                </div>

                <!-- KETERANGAN -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Keterangan
                    </label>

                    <div class="relative">

                        <textarea
                            wire:model.defer="keterangan"
                            rows="5"
                            placeholder="Contoh: Sakit demam, izin acara keluarga, atau keterangan lainnya..."
                            class="w-full resize-none rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100"></textarea>

                        <div class="absolute right-4 top-4 text-gray-400">

                            <iconify-icon
                                icon="solar:notes-bold"
                                width="22"
                                height="22">
                            </iconify-icon>

                        </div>

                    </div>

                    <p class="mt-2 text-xs text-gray-400">
                        Keterangan boleh diisi untuk sakit, izin, atau alpa.
                    </p>

                    @error('keterangan')
                    <p class="mt-2 text-sm text-rose-500">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 gap-6 mt-4">



                    <!-- ACTION -->
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">

                        <a href="{{ route('absensi-murid') }}" wire:navigate>
                            <button
                                type="button"
                                class="w-full rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-auto">

                                Batal

                            </button>
                        </a>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="update"
                            class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                            <iconify-icon
                                wire:loading.remove
                                wire:target="update"
                                icon="lineicons:save"
                                width="20"
                                height="20"
                                class="transition group-hover:scale-110">
                            </iconify-icon>

                            <iconify-icon
                                wire:loading
                                wire:target="update"
                                icon="line-md:loading-twotone-loop"
                                width="20"
                                height="20">
                            </iconify-icon>

                            <span wire:loading.remove wire:target="update">
                                Simpan Perubahan
                            </span>

                            <span wire:loading wire:target="update">
                                Menyimpan...
                            </span>

                        </button>

                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
