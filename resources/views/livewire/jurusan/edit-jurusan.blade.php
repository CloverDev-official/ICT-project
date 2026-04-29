<div>
    <!-- btn kembali -->
    <a href="{{ route('data-jurusan') }}" wire:navigate>
        <button
            class="px-4 py-2 rounded-lg bg-blue-deep-solid text-white transition-all duration-200 hover:bg-blue-deep active:scale-95 flex items-center justify-center capitalize mb-5">
            <iconify-icon icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
            kembali
        </button>
    </a>

    <!-- wrap form -->
    <div class="bg-white p-4 rounded-xl shadow-sm">

        <form wire:submit.prevent="store" class="p-6 grid grid-cols-1 gap-4">

            <!-- Nama jurusan -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Jurusan</label>
                <input type="text" wire:model.defer="nama"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Nama jurusan contoh : Animasi">
                @error('nama')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- btn batal & save -->
            <div class=" flex justify-end pt-4 mt-2">
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-blue-main text-white transition-all duration-150 hover:bg-blue-deep-solid active:scale-95 shadow flex items-center justify-center gap-1">
                    <iconify-icon icon="lineicons:save" width="18" height="18"></iconify-icon>
                    Simpan
                </button>

            </div>
        </form>
    </div>
</div>
