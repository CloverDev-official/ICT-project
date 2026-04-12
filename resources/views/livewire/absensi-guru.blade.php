<div class="flex flex-col w-full gap-5" >
    <!-- kategori -->
    <div class="bg-white p-4 rounded-lg shadow-sm" >
        <div>
            <h1 class="text-2xl font-bold capitalize" >daftar kategori</h1>
            <p class="text-sm text-gray-400 line-clamp-2" >Silahkan pilih kategori agar bisa menentukan kelas yang diinginkan</p>
        </div>


    </div>

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
                        <th class="capitalize text-center px-4 py-3">aksi</th>
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
                        <td class="border-r text-center border-gray-200 px-4 py-3" >
                            <div x-data="{openModal: false}" class="flex items-center justify-center">
                                <!-- button -->
                                <button @click="openModal = !openModal" class="bg-amber-400 p-2 rounded-lg text-white flex items-center justify-center transition-all duration-150 hover:bg-amber-600 active:scale-95" >
                                        <iconify-icon icon="lineicons:pencil-1" width="18" height="18"></iconify-icon>
                                        Edit
                                </button>
                                <livewire:components.modal.modal-edit-absen-murid/>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
