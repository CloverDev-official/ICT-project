    <div
        x-transition.opacity
        style="display: none;"
        class="z-50 fixed inset-0 flex items-center justify-center p-5 bg-black/20"
    >
        <div 
            x-transition.scale
            class="bg-white rounded-2xl shadow-2xl w-96 py-6"
        >
                <h2 class="flex items-center gap-2 text-lg font-semibold mb-4 capitalize px-6">
                <div class="bg-rose-100 text-rose-600 p-2 rounded-lg flex items-center justify-center">
                    <iconify-icon icon="mdi:file-warning" width="22"></iconify-icon> 
                </div>
                Import Data Murid gagal
            </h2>

            <hr class="p-0 mb-4 text-gray-400" >

            <p class="text-gray-600 mb-6 px-6">
                Import gagal, tidak bisa mengimport data murid 
            </p>
            

            <!-- Footer -->
            <div class="mt-4 flex justify-end gap-3 px-6">
                <button
                    @click="openImportGagal = false"
                    class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100"
                >
                    tutup
                </button>

                <button
                    class="px-5 py-2 bg-blue-main text-white rounded-lg shadow hover:bg-blue-deep-solid active:scale-95 transition"
                >
                    coba lagi
                </button>
            </div>

        </div>
    </div>