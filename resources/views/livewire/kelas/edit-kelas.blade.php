<div>
    <!-- btn kembali -->
    <a href="{{ route('data-kelas') }}" wire:navigate>
        <button
            class="px-4 py-2 rounded-lg bg-blue-deep-solid text-white transition-all duration-200 hover:bg-blue-deep active:scale-95 flex items-center justify-center capitalize mb-5">
            <iconify-icon icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
            kembali
        </button>
    </a>

    <!-- wrap form -->
    <div class="bg-white p-4 rounded-xl shadow-sm">

        <form wire:submit.prevent="store" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- tingkat -->
            <div
            x-data="{
                open: false,
                selectedId: null,
                selectedLabel: null,

                toggle() {
                    this.open = !this.open
                },

                select(id, label) {
                    this.selectedId = id
                    this.selectedLabel = label
                    this.open = false

                    $wire.set('filterTingkat', id)
                }
            }"
            class="relative w-full"
            >
                <label class="text-sm text-gray-600 capitalize font-semibold">tingkat</label>

                <div @click="toggle()"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                    <span x-text="selectedLabel ?? 'Semua tingkat'" class="text-gray-700 text-sm"></span>
                    <iconify-icon 
                        class="text-gray-400 transition-transform" 
                        :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24">
                    </iconify-icon>
                </div>

                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua tingkat')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                    >
                        <span>Semua tingkat</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>
                    @foreach (['X', 'XI', 'XII', 'XIII'] as $index => $tingkat)
                        <div
                            @click.prevent="select({{ $index }}, '{{ $tingkat }}')"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                        >
                            <span>{{ $tingkat }}</span>
                            <iconify-icon 
                                x-show="selectedId == {{ $index }}" 
                                icon="lineicons:check" 
                                width="24" 
                                height="24">
                            </iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- jurusan -->
            <div
            x-data="{
                open: false,
                selectedId: null,
                selectedLabel: null,

                toggle() {
                    this.open = !this.open
                },

                select(id, label) {
                    this.selectedId = id
                    this.selectedLabel = label
                    this.open = false

                    $wire.set('filterJurusan', id)
                }
            }"
            class="relative w-full"
            >
                <label class="text-sm text-gray-600 capitalize font-semibold">jurusan</label>

                <div @click="toggle()"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2  border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                    <span x-text="selectedLabel ?? 'Semua jurusan'" class="text-gray-700 text-sm"></span>
                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </div>

                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div
                        @click.prevent="select(null, 'Semua jurusan')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                    >
                        <span>Semua jurusan</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>

                    @foreach (["Pekerjaan Sosial", "Teknik Furnitur", "Animasi"] as $index => $jurusan)
                        <div
                            @click.prevent="select({{ $index }}, '{{ $jurusan }}')"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                        >
                            <span>{{ $jurusan }}</span>
                            <iconify-icon 
                                x-show="selectedId == {{ $index }}" 
                                icon="lineicons:check" 
                                width="24" 
                                height="24">
                            </iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- Nama Kelas -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Kelas</label>
                <input type="text" wire:model.defer="nama"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Nama Kelas contoh : c">
                @error('nama')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- btn batal & save -->
            <div class="md:col-span-2 flex justify-end gap-3 pt-4 mt-2">
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-blue-main text-white transition-all duration-150 hover:bg-blue-deep-solid active:scale-95 shadow flex items-center justify-center gap-1">
                    <iconify-icon icon="lineicons:save" width="18" height="18"></iconify-icon>
                    Simpan
                </button>

            </div>
        </form>
    </div>
</div>
