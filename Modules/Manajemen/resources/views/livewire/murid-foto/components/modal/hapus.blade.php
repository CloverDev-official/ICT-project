<div
    x-data="{ openModalDelete: false }"
    x-show="openModalDelete"
    x-on:open-delete-foto.window="
        openModalDelete = true;
        $wire.loadMuridFoto($event.detail.id);
    "
    @close-delete-modal.window="openModalDelete = false"
    x-transition.opacity
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">

    <!-- MODAL -->
    <div
        @click.outside="openModalDelete = false"
        x-transition.scale
        class="w-full max-w-md overflow-hidden rounded-[2rem] border border-white/20 bg-white shadow-2xl">

        <!-- HEADER -->
        <div
            class="relative overflow-hidden bg-gradient-to-r from-rose-600 to-red-500 px-6 py-5 text-white">

            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/15 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 h-24 w-24 rounded-full bg-white/10 blur-2xl"></div>

            <div class="relative flex items-center justify-between gap-4">

                <div class="flex items-center gap-4">

                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-white/15 backdrop-blur">

                        <iconify-icon
                            icon="lineicons:trash-3"
                            width="28"
                            height="28">
                        </iconify-icon>

                    </div>

                    <div>
                        <h2 class="text-xl font-bold capitalize">
                            Hapus Foto Murid "{{ $murid->nama ?? '-' }}"
                        </h2>

                        <p class="mt-1 text-sm text-rose-100">
                            Tindakan ini tidak bisa dibatalkan.
                        </p>
                    </div>

                </div>

                <button
                    type="button"
                    @click="openModalDelete = false"
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-white transition hover:bg-white/20 active:scale-95">

                    <iconify-icon
                        icon="mdi:close"
                        width="22"
                        height="22">
                    </iconify-icon>

                </button>

            </div>

        </div>

        <!-- BODY -->
        <div class="px-6 py-6">

            <div
                class="mb-5 flex items-start gap-4 rounded-3xl border border-rose-100 bg-rose-50 p-4">

                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-500 text-white">

                    <iconify-icon
                        icon="solar:danger-triangle-bold"
                        width="24"
                        height="24">
                    </iconify-icon>

                </div>

                <div>
                    <h3 class="font-semibold text-gray-800">
                        Konfirmasi Penghapusan
                    </h3>

                    <p class="mt-1 text-sm leading-relaxed text-gray-500">
                        Apakah kamu yakin ingin menghapus data foto murid ini?
                        Foto yang sudah dihapus tidak dapat dikembalikan.
                    </p>
                </div>

            </div>

            <!-- OPTIONAL INFO -->
            <div
                class="rounded-3xl border border-gray-200 bg-gray-50 p-4">

                <div class="flex items-center justify-center gap-3 text-center">
                    <div>
                        <p class="text-xs text-gray-400">
                            Data yang akan dihapus
                        </p>

                        <h4 class="mt-1 font-semibold text-gray-800">
                            <img
                                src="{{ $murid->image_path ?? '-' }}"
                                alt="Foto Murid"
                                class="mx-auto h-40 w-40 rounded-lg object-cover object-center">
                        </h4>
                    </div>
                </div>

            </div>

        </div>

        <!-- FOOTER -->
        <div
            class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-6 py-5 sm:flex-row sm:justify-end">

            <button
                type="button"
                @click="openModalDelete = false"
                class="w-full rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 active:scale-95 sm:w-auto">

                Batal

            </button>

            <button
                type="button"
                wire:click="destroy"
                wire:loading.attr="disabled"
                wire:target="destroy"
                class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-rose-700 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                <iconify-icon
                    wire:loading.remove
                    wire:target="destroy"
                    icon="lineicons:trash-3"
                    width="20"
                    height="20"
                    class="transition group-hover:scale-110">
                </iconify-icon>

                <iconify-icon
                    wire:loading
                    wire:target="destroy"
                    icon="line-md:loading-twotone-loop"
                    width="20"
                    height="20">
                </iconify-icon>

                <span wire:loading.remove wire:target="destroy">
                    Hapus Foto
                </span>

                <span wire:loading wire:target="destroy">
                    Menghapus...
                </span>

            </button>

        </div>

    </div>

</div>