<div>
    <div class="flex items-center justify-start gap-5">
        <!-- btn tambah data kelas -->
        <a href="{{ route('tambah-kelas') }}" wire:navigate>
            <button
                class="px-4 py-2 rounded-lg bg-emerald-600 text-white transition-all duration-200 hover:bg-emerald-700 active:scale-95 flex items-center justify-center gap-1 capitalize ">
                <iconify-icon icon="line-md:plus" width="20" height="20"></iconify-icon>
                tambah data kelas
            </button>
        </a>

        <!-- btn import CSV -->
        <div x-data="{ openModalImport: false }">
            <button
                @click="openModalImport = true"
                class="px-4 py-2 rounded-lg bg-blue-main text-white transition-all duration-200 hover:bg-blue-deep-solid active:scale-95 flex items-center justify-center gap-1 capitalize ">
                <iconify-icon icon="line-md:file-import" width="20" height="20"></iconify-icon>
                import CSV
            </button>
            <!-- modal import murid  -->
            <livewire:components.modal.kelas.modal-import-kelas />
        </div>
    </div>
    <!-- kategori -->
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm grid grid-cols-2 md:grid-cols-3 gap-5">
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
                    <span x-text="selectedLabel ?? 'Semua Kelas'" class="text-gray-700 text-sm"></span>
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
    <div class="mt-2 bg-white p-4 rounded-xl shadow-sm">
        <div class="mb-5">
            <!-- tombol hapus -->
            <div class="mb-3 flex items-center gap-3 relative" x-data="{ openModalColon: false }">
    
                <button @click="openModalColon = !openModalColon"
                    class="p-2 rounded-lg text-gray-500 duration-200  transition-all hover:bg-blue-deep-solid hover:text-white active:scale-95 flex items-center justify-center ">
                    <iconify-icon icon="lineicons:menu-meatballs-1" width="25" height="24"></iconify-icon>
                </button>
    
                <livewire:components.modal.modal-colon />
    
                <span class="text-sm text-gray-500">
                    <span x-text="selected.length"></span> dipilih
                </span>
    
            </div>
        </div>
        <!-- table -->
        <div
        
        class="overflow-x-auto table-auto md:table-fixed rounded-t-lg"
        >
            <table class="w-full text-sm text-left text-gray-600" >
                <thead class="bg-blue-main border border-gray-200 text-white uppercase text-xs" >
                    <tr>
                        <th class="px-4 py-3" >
                            <input type="checkbox" @click="toggleAll">
                        </th>
                        <th class="text-center px-4 py-3">No.</th>
                        <th class="text-center px-4 py-3">Tingkat</th>
                        <th class="text-center px-4 py-3">Jurusan</th>
                        <th class="text-center px-4 py-3">Kelas</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $kelasList = [
                            ['tingkat' => 'X', 'jurusan' => 'Pengembangan Perangkat Lunak dan Gim', 'kelas' => 'A'],
                            ['tingkat' => 'XI', 'jurusan' => 'Pengembangan Perangkat Lunak dan Gim', 'kelas' => 'A'],
                            ['tingkat' => 'XII', 'jurusan' => 'Pengembangan Perangkat Lunak dan Gim', 'kelas' => 'A'],
                            ['tingkat' => 'X', 'jurusan' => 'Pengembangan Perangkat Lunak dan Gim', 'kelas' => 'B'],
                            ['tingkat' => 'XI', 'jurusan' => 'Pengembangan Perangkat Lunak dan Gim', 'kelas' => 'B'],
                            ['tingkat' => 'XII', 'jurusan' => 'Pengembangan Perangkat Lunak dan Gim', 'kelas' => 'B'],
                        ];
                    @endphp

                    @foreach ($kelasList as $index => $kelas)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 border-lr border-gray-200">
                            
                            <!-- checkbox -->
                            <td class="px-4 py-3 w-10">
                                <input type="checkbox">
                            </td>

                            <!-- nomor -->
                            <td class="border-r border-gray-200 px-4 py-3 text-center w-20">
                                {{ $index + 1 }}
                            </td>

                            <!-- tingkat -->
                            <td class="border-r border-gray-200 px-4 py-3 text-center ">
                                {{ $kelas['tingkat'] }}
                            </td>

                            <!-- jurusan -->
                            <td class="border-r border-gray-200 px-4 py-3 text-center">
                                {{ $kelas['jurusan'] }}
                            </td>

                            <!-- kelas -->
                            <td class="border-r border-gray-200 px-4 py-3 text-center uppercase">
                                {{ $kelas['kelas'] }}
                            </td>

                            <!-- crud -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- edit -->
                                    <a href="{{ route('edit-kelas') }}" wire:navigate>
                                        <button class="bg-amber-400 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-amber-600 active:scale-95" >
                                            <iconify-icon icon="lineicons:pencil-1" width="20" height="20"></iconify-icon>
                                        </button>
                                    </a>

                                    <!-- hapus -->
                                    <div x-data="{ openModalDelete: false }" >
                                        <button
                                            @click="openModalDelete = true"
                                            type="button"
                                            class="bg-rose-500 w-8 h-8 rounded-lg text-white flex items-center justify-center gap-1 transition-all duration-150 hover:bg-rose-700 active:scale-95"
                                        >
                                            <iconify-icon icon="lineicons:trash-3" width="20" height="20"></iconify-icon>
                                        </button>
                                        <livewire:components.modal.kelas.modal-hapus-kelas/>
                                    </div>
                                </div>
                                
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>