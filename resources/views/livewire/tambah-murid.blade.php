<div>
    <!-- btn kembali -->
    <a 
        href="{{ route('data-murid') }}"
        wire:navigate >
        <button class="px-4 py-2 rounded-lg bg-blue-deep-solid text-white transition-all duration-200 hover:bg-blue-deep active:scale-95 flex items-center justify-center capitalize mb-5" >
            <iconify-icon icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
            kembali
        </button>
    </a>

    <!-- wrap form -->
    <div class="bg-white p-4 rounded-xl shadow-sm" >
    
        <form class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    
                <!-- Nama -->
                <div>
                    <label class="text-sm text-gray-600 capitalize">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap"
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Nama">
                </div>
    
                <!-- NIS -->
                <div>
                    <label class="text-sm text-gray-600">NIS</label>
                    <input type="number" name="nis" required
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 123456789">
                </div>
    
                <!-- NIPD -->
                <div>
                    <label class="text-sm text-gray-600">NIPD</label>
                    <input type="number" name="nipd" require
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 1234">
                </div>
    
                <!-- Kelas -->
                <div x-data="{
                        open: false,
                        selected: '',
    
                        select(item) {
                            this.selected = item
                            this.open = false
                        }
                    }" 
                    class="relative w-full">
    
                    <label class="text-sm text-gray-600">Kelas</label>
    
                    <!-- Button -->
                    <div @click="open = !open"
                        class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-white border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                        
                        <span x-text="selected ? selected : 'Pilih Kelas'" class="text-gray-700 text-sm"></span>
    
                        <iconify-icon 
                            class="text-gray-400 transition-transform" 
                            :class="{ 'rotate-180': open }" 
                            icon="lineicons:chevron-up" 
                            width="25" 
                            height="24">
                        </iconify-icon>
                    </div>
    
                    <!-- Dropdown -->
                    <div x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute mt-2 w-full h-52 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto z-50 scroll-thin">
    
                        @foreach (['XI PPLG A', 'XI PPLG B', 'X PPLG A', 'X PPLG B', 'XII PPLG A', 'XII PPLG B'] as $kelas)
                            <div 
                                @click="select('{{ $kelas }}')"
                                class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                                :class="selected === '{{ $kelas }}' ? 'bg-blue-deep-solid text-white' : ''">
    
                                <span>{{ $kelas }}</span>
    
                                <iconify-icon 
                                    x-show="selected === '{{ $kelas }}'"  
                                    icon="lineicons:check" 
                                    width="24" 
                                    height="24">
                                </iconify-icon>
                            </div>
                        @endforeach
    
                    </div>
                </div>
                
                <!-- Jenis Kelamin -->
                <div x-data="{
                    open:false,
    
                    selected: '',
    
                    select(item) {
                        this.selected = item
                        this.open = false
                    
                    }
                
                }" class="relative w-full">
                    <label class="text-sm text-gray-600">Jenis Kelamin</label>
                    <!-- Button -->
                    <div @click="open = !open"
                        class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-white border border-gray-300 rounded-xl cursor-pointer  hover:border-blue-500 transition">
                        
                        <span x-text="selected ? selected : 'Pilih Kelas'" class="text-gray-700 text-sm"></span>
    
                        <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                    </div>
    
                    <!-- Dropdown -->
                    <div x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute mt-2 w-full h-20 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
                            @foreach (['Laki-laki', 'Perempuan'] as $jk )
                                <div @click="select('{{ $jk}}')"
                                    class="px-4 py-2  cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                                    
                                    <span>{{ $jk }}</span>
    
                                    <!-- icon check -->                                
                                    <iconify-icon x-show="selected === '{{ $jk }}'"  icon="lineicons:check" width="24" height="24"></iconify-icon>
                                </div>                        
                            @endforeach
    
                    </div>
                </div>
    
                <!-- No HP -->
                <div>
                    <label class="text-sm text-gray-600">No HP</label>
                    <input type="number" name="no_hp" require
                        class=" capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 0812345678">
                </div>
    
                <!-- email -->
                <div>
                    <label class="text-sm text-gray-600">Email</label>
                    <input type="email" name="email" require
                        class="  mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="contoh@gmail.com">
                </div>
    
                <!-- tempat lahir -->
                <div >
                    <label class="text-sm text-gray-600">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" require
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Tempat lahir">
                </div>
    
                <!-- Tanggal lahir -->
                <div >
                    <label class="text-sm text-gray-600">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" require
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2">
                </div>
    
                <!-- agama -->
                <div>
                    <label class="text-sm text-gray-600 capitalize">Agama</label>
                    <input type="text" name="agama" require
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Agama">
                </div>
    
                <!-- alamat -->
                <div>
                    <label class="text-sm text-gray-600 capitalize">Alamat</label>
                    <input type="text" name="alamat" require
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="JL. KOMP. Contoh NO. 126">
                </div>
                
                <!-- RT -->
                <div >
                    <label class="text-sm text-gray-600 ">RT</label>
                    <input type="number" name="RT" require 
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 6">
                </div>
    
                <!-- RW -->
                <div >
                    <label class="text-sm text-gray-600 ">RT</label>
                    <input type="number" name="RW" require
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 2">
                </div>
    
                <!-- Kelurahan -->
                <div >
                    <label class="text-sm text-gray-600 capitalize">Kelurahan</label>
                    <input type="text" name="kelurahan" require
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : Pemurus Luar">
                </div>
    
                <!-- kecamatan -->
                <div >
                    <label class="text-sm text-gray-600 capitalize">Kecamatan</label>
                    <input type="text" name="kecamatan" require
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : Kec. Banjarmasin Timur">
                </div>
    
                <!-- nama ayah -->
                <div >
                    <label class="text-sm text-gray-600 capitalize">Nama Ayah</label>
                    <input type="text" name="kecamatan" require
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Nama Ayah">
                </div>
    
                <!-- nama ibu -->
                <div >
                    <label class="text-sm text-gray-600 capitalize">nama ibu</label>
                    <input type="text" name="kecamatan" require
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Nama Ibu">
                </div>
    
                <!-- nama wali -->
                <div >
                    <label class="text-sm text-gray-600 capitalize">nama wali</label>
                    <input type="text" name="nama wali" 
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Nama Wali">
                </div>
    
                <!-- RFID -->
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">RFID Code</label>
                    <input type="text" name="rfid_code"
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Tempelkan kartu RFID">
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