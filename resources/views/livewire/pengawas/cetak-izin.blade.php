<div>
    <div
        x-data="{
            open: false,
            selectedId: @entangle('murid_id'),
            selectedLabel: @entangle('selectedLabel'),

            toggle() {
                this.open = !this.open
            },

            select(id, label) {
                this.selectedId = id
                this.selectedLabel = label
                this.open = false
            }
        }"
        class="relative">

        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Nama Murid
        </label>

        <div
            @click="toggle()"
            class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

            <div class="flex items-center gap-3">

                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-main">

                    <iconify-icon
                        icon="solar:user-bold"
                        width="22"
                        height="22">
                    </iconify-icon>

                </div>

                <span
                    x-text="selectedLabel ?? 'Pilih nama murid'"
                    class="line-clamp-1 text-gray-700">
                </span>

            </div>

            <iconify-icon
                icon="lineicons:chevron-up"
                width="20"
                height="20"
                class="text-gray-400 transition-transform"
                :class="{ 'rotate-180': open }">
            </iconify-icon>

        </div>

        <!-- DROPDOWN -->
        <div
            x-show="open"
            x-transition
            @click.outside="open = false"
            style="display:none"
            class="absolute z-50 mt-2 max-h-64 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin">

            <div
                @click.prevent="select(null, 'Pilih nama murid')"
                class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                <span>Pilih nama murid</span>

                <iconify-icon
                    x-show="selectedId === null"
                    icon="lineicons:check"
                    width="18"
                    height="18">
                </iconify-icon>

            </div>

            @foreach ($listMurid ?? [] as $murid)

                <div
                    @click.prevent="select({{ (int) $murid->id }}, @js($murid->nama . ' - ' . $murid->nipd))"
                    class="flex cursor-pointer items-center justify-between gap-4 px-4 py-3 transition hover:bg-blue-main hover:text-white">

                    <div>
                        <h4 class="font-semibold capitalize">
                            {{ $murid->nama }}
                        </h4>

                        <p class="text-xs opacity-70">
                            NIPD : {{ $murid->nipd }}
                        </p>
                    </div>

                    <iconify-icon
                        x-show="selectedId == {{ (int) $murid->id }}"
                        icon="lineicons:check"
                        width="18"
                        height="18">
                    </iconify-icon>

                </div>

            @endforeach

        </div>

        @error('murid_id')
            <p class="mt-2 text-sm text-rose-500">
                {{ $message }}
            </p>
        @enderror

    </div>

    <div>
        <textarea
            wire:model.defer="alasan"
            placeholder="Masukkan alasan izin"
            class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition focus:border-blue-main focus:bg-white focus:outline-none @error('alasan') border-rose-500 @enderror">
        
        </textarea>
    </div>

    <button
        wire:click="cetakIzin"
        class="mt-4 w-full rounded-xl bg-blue-main px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-600">
        Cetak Izin
    </button>
</div>
