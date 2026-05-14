<div x-data="{ openModal: false }">
    <button
        @click="openModal = true"
        class="w-full px-6 py-3 rounded-2xl bg-blue-main text-white font-medium hover:bg-blue-deep-solid active:scale-95 transition-all duration-150 shadow-sm">
        Proses Kenaikan Kelas
    </button>

    <!-- Overlay -->
    <div
        x-show="openModal"
        x-transition.opacity
        style="display: none;"
        class="fixed inset-0 z-40 bg-black/50"></div>

    <!-- MODAL -->
    <div
        x-show="openModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">

        <div
            @click.away="openModal = false"
            class="w-full max-w-md rounded-3xl bg-white shadow-2xl overflow-hidden py-6">

            <!-- HEADER -->
            <h2 class="flex items-center gap-2 text-lg font-semibold mb-4 capitalize px-6">
                <div class="bg-rose-100 text-rose-600 p-2 rounded-lg flex items-center justify-center">
                    <iconify-icon icon="mdi:warning" width="20" height="20"></iconify-icon>
                </div>
                Konfirmasi Kenaikan Kelas
            </h2>

            <hr class="p-0 mb-4 text-gray-400">

            <p class="text-gray-600 mb-6 px-6">
                Proses kenaikan kelas akan mengubah seluruh data murid
                berdasarkan tingkat kelas saat ini.
                Pastikan backup data telah dilakukan sebelum melanjutkan.
            </p>
            <!-- FOOTER -->
            <div class="flex justify-end gap-3 px-6">

                <!-- Batal -->
                <button
                    @click="openModal = false"
                    class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                    Batal
                </button>

                <!-- Hapus -->
                <button @click="openModal = false" class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600">
                    Lanjutkan
                </button>

            </div>

        </div>

    </div>

</div>