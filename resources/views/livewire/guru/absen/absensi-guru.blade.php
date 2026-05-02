<div class="flex flex-col w-full gap-5" >
    <!-- kategori -->
    <div class="bg-white p-4 rounded-xl shadow-sm grid grid-cols-2 md:grid-cols-3 gap-5">
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

                $wire.set('filterStatus', id)
            }
        }"
        class="relative w-full"
        >
            <label class="text-sm text-gray-600 capitalize font-semibold">Status Kepegawaian</label>

            <div @click="toggle()"
                class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                <span x-text="selectedLabel ?? 'Semua status'" class="text-gray-700 text-sm"></span>
                <iconify-icon 
                    class="text-gray-400 transition-transform" 
                    :class="{ 'rotate-180': open }"
                    icon="lineicons:chevron-up" width="25" height="24">
                </iconify-icon>
            </div>

            <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                <div
                    @click.prevent="select(null, 'Semua status')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                >
                    <span>Semua status</span>
                    <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                </div>

                @foreach (['PNS', 'Honorer'] AS $index => $status)
                    <div
                        click.prevent="select({{ $index }}, '{{ $status }}')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                    >
                        <span>{{ $status }}</span>
                        <iconify-icon x-show="selectedId == {{ $index }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>
                @endforeach

            </div>
        </div>

        <!-- jenis ptk -->
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

                $wire.set('filterjenis', id)
            }
        }"
        class="relative w-full"
        >
            <label class="text-sm text-gray-600 capitalize font-semibold">Jenis PTK</label>

            <div @click="toggle()"
                class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                <span x-text="selectedLabel ?? 'Semua jenis'" class="text-gray-700 text-sm"></span>
                <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                    icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
            </div>

            <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                <div
                    @click.prevent="select(null, 'Semua jenis')"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                >
                    <span>Semua jenis</span>
                    <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24" height="24"></iconify-icon>
                </div>

                @foreach (["Guru Mapel", "Guru BK"] as $index => $jenis)
                    <div
                        @click.prevent="select({{ $index }}, {{ $jenis }})"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                    >
                        <span>{{ $jenis }}</span>
                        <iconify-icon x-show="selectedId == {{ $index }}" icon="lineicons:check" width="24" height="24"></iconify-icon>
                    </div>
                @endforeach

            </div>
        </div>

        <!-- tanggal absen -->
        <div>
            <label class="text-sm text-gray-600 capitalize">Tanggal  Absen</label>
            <input type="date" wire:model.live="filterTanggal"
                class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2">
        </div>
    </div>

    <!-- wrapper table guru -->
    <div class="bg-white p-4 rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row md:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold capitalize" >daftar Absen Guru</h1>
                <p class="text-sm text-gray-400 line-clamp-2" >daftar absen guru pada tanggal ( nanti tanggal dari yang dipilih)</p>
            </div>

            <!-- input search -->
            <div class="relative w-full md:w-sm">
                <input 
                    type="search"
                    name="search"
                    placeholder="Cari Nama Murid..."
                    class="w-full text-sm mt-1 px-4 pr-10 py-2 bg-gray-100 border border-gray-300 rounded-xl
                    hover:border-blue-500
                    focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none
                    transition"
                />

                <div class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none">
                    <iconify-icon icon="mdi:account-search-outline" width="20" height="20"></iconify-icon>
                </div>
            </div> 
        </div>
        
        <!-- container table guru -->
        <div class="overflow-x-auto table-auto md:table-fixed rounded-t-lg">
            <table class="min-w-full text-sm text-left text-gray-600" >
                <thead class="bg-blue-main border border-gray-200 text-white uppercase text-xs" >
                    <tr>
                        <th class="capitalize text-center px-4 py-3">no.</th>
                        <th class="capitalize text-center px-4 py-3">NUPTK</th>
                        <th class="capitalize text-center px-4 py-3">nama guru</th>
                        <th class="capitalize text-center px-4 py-3">kehadiran</th>
                        <th class="capitalize text-center px-4 py-3">jam masuk</th>
                        <th class="capitalize text-center px-4 py-3">jam pulang</th>
                        <th class="capitalize text-center px-4 py-3">keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class=" hover:bg-gray-100 border-lr border-gray-200" > 
                        <td class="border-r text-center border-gray-200 px-4 py-3" >1</td>
                        <td class="border-r text-center border-gray-200 px-4 py-3" >123456789</td>
                        <td class="border-r text-center border-gray-200 px-4 py-3" >Ghaizan</td>
                        <td class="border-r text-center border-gray-200 px-4 py-3" >hadir</td>
                        <td class="border-r text-center border-gray-200 px-4 py-3" >07.00</td>
                        <td class="border-r text-center border-gray-200 px-4 py-3" >16.30</td>
                        <td class="border-r text-center border-gray-200 px-4 py-3" >-</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
