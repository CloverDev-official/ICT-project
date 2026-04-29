<div>
    <div class="flex items-center justify-start gap-5">
        <!-- btn tambah data kelas -->
        <a href="{{ route('tambah-jurusan') }}" wire:navigate>
            <button
                class="px-4 py-2 rounded-lg bg-emerald-600 text-white transition-all duration-200 hover:bg-emerald-700 active:scale-95 flex items-center justify-center gap-1 capitalize ">
                <iconify-icon icon="line-md:plus" width="20" height="20"></iconify-icon>
                tambah data Jurusan
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
            <livewire:components.modal.jurusan.modal-import-jurusan />
        </div>
    </div>
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">
        <div class="flex justify-between items-center gap-4 mb-5">
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
            <!-- input search -->
            <div class="relative w-40 md:w-sm">
                <input 
                    type="search"
                    name="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari Nama Jurusan..."
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
                        <th class="text-center px-4 py-3">Jurusan</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $kelasList = [
                            [ 'jurusan' => 'Pengembangan Perangkat Lunak dan Gim'],
                            [ 'jurusan' => 'Pekerjaan Sosial'],
                            [ 'jurusan' => 'Animasi'],
                            [ 'jurusan' => 'Teknik Jaringan Komunikasi Telekomunikasi'],
                            [ 'jurusan' => 'Teknik Furnitur'],
                            [ 'jurusan' => 'Teknik Kimia Industri'],
                            [ 'jurusan' => 'Broadcasting dan Film'],
                            [ 'jurusan' => 'Desain Komunikasi Visual '],
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
                            <!-- jurusan -->
                            <td class="border-r border-gray-200 px-4 py-3 text-center">
                                {{ $kelas['jurusan'] }}
                            </td>

                            <!-- crud -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- edit -->
                                    <a href="{{ route('edit-jurusan') }}" wire:navigate>
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
                                        <livewire:components.modal.jurusan.modal-hapus-jurusan/>
                                    </div>
                                </div>
                                
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
</div>
