<div>
    <!-- GENERATE QR CODE UNTUK MURID -->
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">
        <div>
            <h1 class="font-semibold text-gray-800 text-2xl capitalize">generate QR CODE murid</h1>
            <p class="text-sm text-gray-400" >
                Generate QR CODE murid berdasarkan tingkat, kelas dan jurusan.
            </p>
        </div>
        <!-- kategori -->
        <div class="mt-5 border-t border-gray-200 p-4  grid grid-cols-2 md:grid-cols-3 gap-5">
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
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                    <span x-text="selectedLabel ?? 'Semua tingkat'" class="line-clamp-1 text-gray-700 text-sm"></span>
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

                    @foreach (['X', 'XI', 'XII', 'XIII'] AS $index => $tingkat)
                        <div
                            click.prevent="select({{ $index }}, '{{ $tingkat }}')"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                        >
                            <span>{{ $tingkat }}</span>
                            <iconify-icon x-show="selectedId == {{ $index }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
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
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                    <span x-text="selectedLabel ?? 'Semua jurusan'" class="line-clamp-1 text-gray-700 text-sm"></span>
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
                            @click.prevent="select({{ $index }}, {{ $jurusan }})"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                        >
                            <span>{{ $jurusan }}</span>
                            <iconify-icon x-show="selectedId == {{ $index }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- kelas -->
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

                        $wire.set('filterIndeks', id)
                    }
                }"
                class="relative w-full">
                    <label class="text-sm text-gray-600 capitalize font-semibold">Kelas</label>

                    <div @click="toggle()"
                        class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                        <span x-text="selectedLabel ?? 'Semua Kelas'" class="line-clamp-1 text-gray-700 text-sm"></span>
                        <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                            icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                    </div>

                    <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                        class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                        <div
                            @click.prevent="select(null, 'Semua Kelas')"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                        >
                            <span>Semua Kelas</span>
                            <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                        </div>

                        @foreach ( ['A', 'B', 'C'] as $indeks => $kelas)
                            <div
                                @click.prevent="select( $indeks }}, {{ $kelas }})"
                                class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                            >
                                <span>{{ $kelas }}</span>
                                <iconify-icon x-show="selectedId == {{ $index }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                            </div>
                        @endforeach

                    </div>
            </div>

        </div>
        <div class="p-4 flex justify-end" >
            <button class="bg-blue-main px-4 py-2 rounded-xl text-white flex items-center gap-2 transition-all duration-150 hover: ">
                <iconify-icon icon="lineicons:cloud-download" width="24" height="24"></iconify-icon>
                Download QR CODE
            </button>
        </div>
    </div>

    <!-- GENERATE QR CODE UNTUK GURU -->
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">
        <div>
            <h1 class="font-semibold text-gray-800 text-2xl capitalize">generate QR CODE guru</h1>
            <p class="text-sm text-gray-400" >
                Generate semua QR CODE guru.
            </p>
        </div>
        <!-- kategori -->
        <div class="mt-5 border-t border-gray-200 p-4  grid grid-cols-2 md:grid-cols-3 gap-5">

        </div>
        <div class="p-4 flex justify-end" >
            <button class="bg-blue-main px-4 py-2 rounded-xl text-white flex items-center gap-2 transition-all duration-150 hover: ">
                <iconify-icon icon="lineicons:cloud-download" width="24" height="24"></iconify-icon>
                Download QR CODE
            </button>
        </div>
    </div>
</div>
