<div
    x-show="openModalColon"
    @click.outside="openModalColon = false"
    x-transition
    style="display: none;"
    class="absolute top-10 mt-2 w-52 p-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50"
    >
        <button
            @click="deleteSelected"
            class="flex items-center text-sm justify-start gap-1 w-full text-left px-4 py-2 transition-all duration-200 hover:bg-rose-400 rounded-lg hover:text-white "
            >
            <iconify-icon icon="lineicons:trash-3" width="18" height="18"></iconify-icon>
            Hapus yang dipilih
        </button>

</div>
