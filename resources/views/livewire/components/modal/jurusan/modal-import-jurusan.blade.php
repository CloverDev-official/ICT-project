<div
    x-show="openModalImport"
    x-transition.opacity
    style="display: none;"
    class="z-50 fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm p-5"
>
    <div 
        @click.outside="openModalImport = false" 
        x-transition.scale
        class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl "
    >

        <!-- Header -->
        <div class="flex justify-between items-center p-6 pb-4">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 text-blue-600 p-2 rounded-lg flex items-center justify-center">
                    <iconify-icon icon="mdi:file-import" width="22"></iconify-icon> 
                </div>
                <h1 class="text-lg font-semibold text-gray-800">
                    Import Data Jurusan
                </h1>
            </div>

            <button
                @click="openModalImport = false"
                class="w-9 h-9 flex items-center justify-center rounded-full transition hover:bg-gray-100 active:scale-90"
            >
                <iconify-icon icon="mdi:close" width="20"></iconify-icon>
            </button>
        </div>
        <hr class="text-gray-400 " >
        <!-- Content -->
        <div class="p-6 m-6 space-y-4 rounded-xl border-2 border-gray-300 border-dashed transition hover:border-blue-main">
            <div class="p-6 h-56 text-center flex justify-center ">
                <div>
                    <iconify-icon icon="mdi:file-import" class="text-9xl text-gray-300" ></iconify-icon>
                    <p class="text-gray-500 text-sm ">
                        <input type="file" style="display: none;" id="fileInput">
                        Drag & drop file di sini atau <span class="text-blue-500 font-semibold cursor-pointer hover:text-blue-700" onclick="document.getElementById('fileInput').click();" >jelajahi</span> untuk memilih file
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-4 flex justify-end gap-3 p-6">
            <button
                @click="openModalImport = false"
                class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100"
            >
                Batal
            </button>
            <div x-data="{ openVerifikasi: false }" >
                <button
                    @click="openVerifikasi = true"
                    class="px-5 py-2 bg-blue-main text-white rounded-lg shadow hover:bg-blue-deep-solid active:scale-95 transition"
                >
                    Import
                </button>
                <!-- modal verifikasi -->
                <livewire:components.modal.modal-verifikasi-import/>
            </div>
        </div>

    </div>
</div>