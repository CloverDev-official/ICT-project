<div
    x-data="{ openModalDelete: false }"
    x-show="openModalDelete"
    x-on:open-delete-guru.window="
        openModalDelete = true;
        $wire.loadGuru($event.detail.ulid);
    "
    x-on:close-modal.window="openModalDelete = false"
    x-transition
    style="display: none;"
    class="fixed inset-0 flex items-center justify-center bg-black/30"
>
    <div @click.outside="openModalDelete = false" class="bg-white rounded-lg shadow-lg w-96 p-6">

        <h2 class="flex items-center text-lg font-semibold mb-4 capitalize">
            <iconify-icon icon="lineicons:trash-3" width="20" height="20"></iconify-icon>
            Hapus Data guru
        </h2>

        <p class="text-gray-600 mb-6">
            Apakah kamu yakin ingin menghapus data guru ini? "{{ $guru->nama ?? '-' }}",
            Data yang dihapus tidak dapat dikembalikan.
        </p>

        <div class="flex justify-end gap-3">

            <!-- Batal -->
            <button
                @click="toggleDelete()"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                Batal
            </button>

            <!-- Hapus -->
            <button wire:click="destroy" class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600">
                Hapus
            </button>
        </div>
    </div>
</div>