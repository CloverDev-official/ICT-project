<div
    x-show="openModal"
    x-transition
    style="display: none;"
    class="fixed inset-0 flex items-center justify-center bg-black/30 bg-opacity-50">
    <div
        @click.outside="openModal = false"
        class="bg-white rounded-lg shadow-lg w-96 p-6">

        <h2 class="text-lg font-semibold mb-4">
            Hapus Data Murid
        </h2>

        <p class="text-gray-600 mb-6">
            Apakah kamu yakin ingin menghapus data murid ini? (nama muridnya),
            Data yang dihapus tidak dapat dikembalikan.
        </p>

        <div class="flex justify-end gap-3">

            <!-- Batal -->
            <button
                @click="openModal = false"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                Batal
            </button>

            <!-- Hapus -->
            <button
                class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600">
                Hapus
            </button>
        </div>
    </div>
</div>