<div>
    <!-- btn kembali -->
    <a 
        href="{{ route('data-guru') }}"
        wire:navigate >
        <button class="px-4 py-2 rounded-lg bg-blue-deep-solid text-white transition-all duration-200 hover:bg-blue-deep active:scale-95 flex items-center justify-center capitalize mb-5" >
            <iconify-icon icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
            kembali
        </button>
    </a>

    <!-- wrap form -->
    <div class="bg-white p-4 rounded-xl shadow-sm" >
    
        <form wire:submit.prevent="update" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    
                <!-- Nama -->
                <div>
                    <label class="text-sm text-gray-600 capitalize">Nama Lengkap</label>
                    <input type="text" wire:model.defer="nama"
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Nama">
                </div>
    
                <!-- NUPTK -->
                <div>
                    <label class="text-sm text-gray-600">NUPTK</label>
                    <input type="number" wire:model.defer="nuptk" 
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 123456789">
                </div>
    
                <!-- NIP -->
                <div>
                    <label class="text-sm text-gray-600">NIP</label>
                    <input type="number" wire:model.defer="nip" 
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 1234566789">
                </div>

                <!-- Status kepegawaian -->
                <div>
                    <label class="text-sm text-gray-600 capitalize">status kepegawaian</label>
                    <input type="text" wire:model.defer="status_kepegawaian" 
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : PPPK">
                </div>

                <!-- jenis PTK -->
                <div>
                    <label class="text-sm text-gray-600 capitalize">jenis PTK</label>
                    <input type="text" wire:model.defer="jenis_ptk" 
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : Guru">
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
                        
                        <span x-text="selected ? selected : 'Pilih Jenis Kelamin'" class="text-gray-700 text-sm"></span>
    
                        <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }" icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                    </div>
    
                    <!-- Dropdown -->
                    <div x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute mt-2 w-full h-20 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
                            @foreach ([['Laki-laki', 'L'], ['Perempuan', 'P']] as $jk )
                                <div @click="select('{{ $jk[0] }}'); $wire.set('jk', '{{ $jk[1] }}')"
                                    class="px-4 py-2  cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                                    
                                    <span>{{ $jk[0] }}</span>
    
                                    <!-- icon check -->                                
                                    <iconify-icon x-show="selected === '{{ $jk[0] }}'"  icon="lineicons:check" width="24" height="24"></iconify-icon>
                                </div>                        
                            @endforeach
    
                    </div>
                </div>
    
                <!-- No HP -->
                <div>
                    <label class="text-sm text-gray-600">No HP</label>
                    <input type="number" wire:model.defer="hp" 
                        class=" capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 0812345678">
                </div>

                <!-- telepon -->
                <div>
                    <label class="text-sm text-gray-600">telepon</label>
                    <input type="number" wire:model.defer="telepon" 
                        class=" capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 0812345678">
                </div>
    
                <!-- email -->
                <div>
                    <label class="text-sm text-gray-600">Email</label>
                    <input type="email" wire:model.defer="email" 
                        class="  mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="contoh@gmail.com">
                </div>
    
                <!-- tempat lahir -->
                <div >
                    <label class="text-sm text-gray-600">Tempat Lahir</label>
                    <input type="text" wire:model.defer="tempat_lahir"
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Tempat lahir">
                </div>
    
                <!-- Tanggal lahir -->
                <div >
                    <label class="text-sm text-gray-600">Tanggal Lahir</label>
                    <input type="date" wire:model.defer="tanggal_lahir"
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2">
                </div>
    
                <!-- agama -->
                <div>
                    <label class="text-sm text-gray-600 capitalize">Agama</label>
                    <input type="text" wire:model.defer="agama" 
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Agama">
                </div>
    
                <!-- alamat -->
                <div>
                    <label class="text-sm text-gray-600 capitalize">Alamat Jalan</label>
                    <input type="text" wire:model.defer="alamat_jalan" 
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="JL. KOMP. Contoh NO. 126">
                </div>
                
                <!-- RT -->
                <div >
                    <label class="text-sm text-gray-600 ">RT</label>
                    <input type="number" wire:model.defer="rt"  
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 6">
                </div>
    
                <!-- RW -->
                <div >
                    <label class="text-sm text-gray-600 ">RW</label>
                    <input type="number" wire:model.defer="rw" 
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 2">
                </div>
    
                <!-- Kelurahan -->
                <div >
                    <label class="text-sm text-gray-600 capitalize">desa Kelurahan</label>
                    <input type="text" wire:model.defer="desa_kelurahan" 
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : Pemurus Luar">
                </div>
    
                <!-- kecamatan -->
                <div >
                    <label class="text-sm text-gray-600 capitalize">Kecamatan</label>
                    <input type="text" wire:model.defer="kecamatan" 
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : Kec. Banjarmasin Timur">
                </div>

                <!-- kode pos -->
                <div >
                    <label class="text-sm text-gray-600 capitalize">kode pos</label>
                    <input type="text" wire:model.defer="kode_pos" 
                        class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        placeholder="Contoh : 70116">
                </div>
    
                <!-- rombel -->
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600 capitalize">Kelas</label>
                    <select wire:model.defer="rombel_id"
                        class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main focus:outline-hidden px-4 py-2">
                        <option value="">Pilih kelas</option>
                        @foreach ($rombel as $r)
                            <option value="{{ $r->id }}">{{ $r->nama_lengkap }}</option>
                        @endforeach
                    </select>
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