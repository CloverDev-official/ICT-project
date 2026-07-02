<div
    x-show="openModalDelete"
    x-transition
    style="display: none;"
    class="fixed inset-0 flex items-center justify-center bg-black/30 p-5 "
>
    <div @click.outside="openModalDelete = false" class="bg-white rounded-lg shadow-lg w-96 py-6">

        <h2 class="flex items-center gap-2 text-lg font-semibold mb-4 capitalize px-6">
            <div class="bg-rose-100 text-rose-600 p-2 rounded-lg flex items-center justify-center" >
                <iconify-icon icon="lineicons:trash-3" width="20" height="20"></iconify-icon>
            </div>
            Hapus Data Izin Murid
        </h2>

        <hr class="p-0 mb-4 text-gray-400" >

        <p class="text-gray-600 mb-6 px-6">
            Apakah kamu yakin ingin menghapus data izin murid ini? "{{ $murid->nama ?? '-' }}",
            Data yang dihapus tidak dapat dikembalikan.
        </p>

        <div class="flex justify-end gap-3 px-6">

            <!-- Batal -->
            <button
                @click="openModalDelete = false"
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