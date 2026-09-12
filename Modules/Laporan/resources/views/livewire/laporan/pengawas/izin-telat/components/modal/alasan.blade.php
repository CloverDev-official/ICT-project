<div
    x-data="{
        open: $wire.entangle('showAlasan'),
        trigger: null,

        init() {
            this.$watch('open', value => {
                if (value) {
                    this.trigger = document.activeElement;

                    this.$nextTick(() => {
                        this.$refs.alasan.focus();
                    });
                } else {
                    this.trigger?.focus();
                }
            });
        }
    }"

    x-show="open"

    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"

    x-on:click.self="$wire.tutupAlasan()"
    x-on:keydown.escape.prevent.stop="$wire.tutupAlasan()"

    x-on:keydown.tab="
        const controls = [
            ...$refs.dialog.querySelectorAll(
                'button:not([disabled]), textarea:not([disabled])'
            )
        ];

        const first = controls[0];
        const last = controls[controls.length - 1];

        if ($event.shiftKey && document.activeElement === first) {
            $event.preventDefault();
            last.focus();
        } else if (!$event.shiftKey && document.activeElement === last) {
            $event.preventDefault();
            first.focus();
        }
    "

    style="display: none;"
    class="
        fixed inset-0 z-50
        flex items-center justify-center
        bg-slate-950/40
        p-4 sm:p-6
        backdrop-blur-md
    "
>
    <div
        id="modal-alasan-telat"
        x-ref="dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="alasan-telat-title"

        x-show="open"

        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"

        class="
            relative
            w-full max-w-lg
            max-h-[calc(100dvh-2rem)]
            overflow-y-auto
            rounded-[28px]
            border border-white/70
            bg-white/95
            shadow-2xl
            shadow-slate-950/20
        "
    >

        {{-- Header --}}
        <div class="relative overflow-hidden border-b border-slate-100 px-6 py-6 sm:px-7">

            <div class="relative flex items-start gap-4">

                <div
                    class="
                        flex h-12 w-12
                        shrink-0
                        items-center justify-center
                        rounded-2xl
                        bg-blue-50
                        text-blue-600
                        ring-1 ring-blue-100
                    "
                >
                    <iconify-icon
                        icon="solar:printer-bold-duotone"
                        width="25"
                        height="25"
                        aria-hidden="true"
                    ></iconify-icon>
                </div>

                <div class="min-w-0 flex-1">
                    <p
                        class="
                            mb-1
                            text-[11px]
                            font-bold
                            uppercase
                            tracking-[0.16em]
                            text-blue-600
                        "
                    >
                        Surat Keterlambatan
                    </p>

                    <h2
                        id="alasan-telat-title"
                        class="
                            text-xl
                            font-bold
                            tracking-tight
                            text-slate-900
                        "
                    >
                        Isi Alasan Keterlambatan
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Masukkan alasan siswa sebelum surat keterlambatan dicetak.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="tutupAlasan"
                    wire:loading.attr="disabled"
                    wire:target="simpanDanCetak"

                    class="
                        flex h-9 w-9
                        shrink-0
                        items-center justify-center
                        rounded-full
                        border border-slate-200
                        bg-white
                        text-slate-400
                        shadow-sm
                        transition

                        hover:border-slate-300
                        hover:bg-slate-50
                        hover:text-slate-700

                        focus-visible:outline-none
                        focus-visible:ring-4
                        focus-visible:ring-blue-100

                        disabled:pointer-events-none
                        disabled:opacity-50
                    "

                    aria-label="Tutup modal"
                >
                    <iconify-icon
                        icon="ant-design:close-outlined"
                        width="21"
                        height="21"
                    ></iconify-icon>
                </button>

            </div>
        </div>


        <form wire:submit="simpanDanCetak">

            <div class="space-y-6 px-6 py-6 sm:px-7">

                {{-- Student Information --}}
                <div
                    class="
                        flex items-center gap-4
                        rounded-2xl
                        border border-slate-200/80
                        bg-slate-50/80
                        p-4
                    "
                >

                    <div
                        class="
                            flex h-11 w-11
                            shrink-0
                            items-center justify-center
                            rounded-xl
                            bg-white
                            text-slate-500
                            shadow-sm
                            ring-1 ring-slate-200
                        "
                    >
                        <iconify-icon
                            icon="solar:user-rounded-bold-duotone"
                            width="23"
                            height="23"
                        ></iconify-icon>
                    </div>

                    <div class="min-w-0">

                        <p
                            class="
                                truncate
                                text-sm
                                font-bold
                                text-slate-900
                            "
                        >
                            {{ $absenCetak?->murid?->nama ?? '-' }}
                        </p>

                        <div
                            class="
                                mt-1
                                flex items-center
                                gap-1.5
                                text-xs
                                font-medium
                                text-slate-500
                            "
                        >
                            <iconify-icon
                                icon="solar:users-group-rounded-linear"
                                width="15"
                                height="15"
                            ></iconify-icon>

                            <span class="truncate">
                                {{ $absenCetak?->murid?->rombel?->nama_lengkap ?? '-' }}
                            </span>
                        </div>

                    </div>

                </div>


                {{-- Alasan --}}
                <div>

                    <div class="mb-2.5 flex items-center justify-between gap-3">

                        <label
                            for="alasan-terlambat"
                            class="
                                text-sm
                                font-bold
                                text-slate-800
                            "
                        >
                            Alasan Keterlambatan

                            <span class="text-rose-500">*</span>
                        </label>

                        <span
                            class="
                                rounded-full
                                bg-slate-100
                                px-2.5 py-1
                                text-[10px]
                                font-semibold
                                text-slate-500
                            "
                        >
                            Maks. 191 karakter
                        </span>

                    </div>


                    <div class="relative">

                        <textarea
                            id="alasan-terlambat"
                            x-ref="alasan"
                            wire:model="alasan"

                            rows="4"
                            required
                            maxlength="191"

                            aria-describedby="alasan-help alasan-error"
                            aria-invalid="{{ $errors->has('alasan') ? 'true' : 'false' }}"

                            placeholder="Contoh: kendaraan bermasalah atau terjadi kemacetan..."

                            class="
                                min-h-[130px]
                                w-full
                                resize-none
                                rounded-2xl
                                border
                                bg-slate-50/70
                                px-4 py-4
                                text-sm
                                leading-6
                                text-slate-800
                                shadow-inner
                                shadow-slate-100/50
                                transition-all
                                duration-200

                                placeholder:text-slate-400

                                hover:border-blue-300

                                focus:border-blue-500
                                focus:bg-white
                                focus:outline-none
                                focus:ring-4
                                focus:ring-blue-100

                                {{ $errors->has('alasan')
                                    ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100'
                                    : 'border-slate-200'
                                }}
                            "
                        ></textarea>

                    </div>


                    <div
                        id="alasan-help"
                        class="
                            mt-3
                            flex items-start
                            gap-2
                            text-xs
                            leading-5
                            text-slate-500
                        "
                    >

                        <iconify-icon
                            icon="solar:info-circle-linear"
                            width="16"
                            height="16"
                            class="mt-0.5 shrink-0"
                        ></iconify-icon>

                        <p>
                            Tuliskan alasan secara singkat dan jelas agar dapat
                            dicantumkan pada surat keterlambatan.
                        </p>

                    </div>


                    <div id="alasan-error" role="alert">

                        @error('alasan')

                            <div
                                class="
                                    mt-3
                                    flex items-center
                                    gap-2
                                    rounded-xl
                                    bg-rose-50
                                    px-3 py-2.5
                                    text-xs
                                    font-medium
                                    text-rose-600
                                    ring-1 ring-rose-100
                                "
                            >
                                <iconify-icon
                                    icon="solar:danger-circle-bold"
                                    width="17"
                                    height="17"
                                    class="shrink-0"
                                ></iconify-icon>

                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            </div>


            {{-- Footer --}}
            <div
                class="
                    flex
                    flex-col-reverse
                    gap-3
                    border-t border-slate-100
                    bg-slate-50/70
                    px-6 py-5

                    sm:flex-row
                    sm:justify-end
                    sm:px-7
                "
            >

                <button
                    type="button"

                    wire:click="tutupAlasan"
                    wire:loading.attr="disabled"
                    wire:target="simpanDanCetak"

                    class="
                        inline-flex
                        h-11
                        items-center
                        justify-center
                        gap-2
                        rounded-xl
                        border border-slate-200
                        bg-white
                        px-5
                        text-sm
                        font-semibold
                        text-slate-600
                        shadow-sm
                        transition

                        hover:border-slate-300
                        hover:bg-slate-50
                        hover:text-slate-900

                        focus-visible:outline-none
                        focus-visible:ring-4
                        focus-visible:ring-slate-200

                        disabled:pointer-events-none
                        disabled:opacity-50
                    "
                >

                    <iconify-icon
                        icon="solar:close-circle-linear"
                        width="18"
                        height="18"
                    ></iconify-icon>

                    Batal

                </button>


                <button
                    type="submit"

                    wire:loading.attr="disabled"
                    wire:target="simpanDanCetak"

                    class="
                        inline-flex
                        h-11
                        min-w-[155px]
                        items-center
                        justify-center
                        gap-2
                        rounded-xl
                        bg-blue-main
                        px-5
                        text-sm
                        font-semibold
                        text-white
                        shadow-lg
                        shadow-blue-500/20
                        transition-all
                        duration-200

                        hover:-translate-y-0.5
                        hover:bg-blue-deep-solid
                        hover:shadow-xl
                        hover:shadow-blue-500/25

                        active:translate-y-0

                        focus-visible:outline-none
                        focus-visible:ring-4
                        focus-visible:ring-blue-200

                        disabled:pointer-events-none
                        disabled:translate-y-0
                        disabled:cursor-not-allowed
                        disabled:opacity-60
                    "
                >

                    <span
                        wire:loading.remove
                        wire:target="simpanDanCetak"
                        class="flex items-center gap-2"
                    >
                        <iconify-icon
                            icon="solar:printer-bold-duotone"
                            width="18"
                            height="18"
                        ></iconify-icon>

                        Simpan & Cetak
                    </span>


                    <span
                        wire:loading
                        wire:target="simpanDanCetak"
                        class="flex items-center gap-2"
                    >
                        <iconify-icon
                            icon="svg-spinners:ring-resize"
                            width="18"
                            height="18"
                        ></iconify-icon>

                        Menyimpan...
                    </span>

                </button>

            </div>

        </form>

    </div>
</div>