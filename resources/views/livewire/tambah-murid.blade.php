<div>
    <!-- btn kembali -->
    <a href="{{ route('data-murid') }}" wire:navigate>
        <button
            class="px-4 py-2 rounded-lg bg-blue-deep-solid text-white transition-all duration-200 hover:bg-blue-deep active:scale-95 flex items-center justify-center capitalize mb-5">
            <iconify-icon icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
            kembali
        </button>
    </a>

    <!-- wrap form -->
    <div class="bg-white p-4 rounded-xl shadow-sm">

        <form wire:submit.prevent="store" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Nama -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Nama Lengkap</label>
                <input type="text" wire:model.defer="nama"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Nama">
                @error('nama')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- NISN -->
            <div>
                <label class="text-sm text-gray-600">NISN</label>
                <input type="number" wire:model.defer="nisn"
                    class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Contoh : 123456789">
                @error('nisn')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- NIPD -->
            <div>
                <label class="text-sm text-gray-600">NIPD</label>
                <input type="number" wire:model.defer="nipd"
                    class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Contoh : 1234">
                @error('nipd')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kelas (rombel) -->
            <div x-data="{
                open: false,
                selectedId: null,
                selectedLabel: '',
                init() {
                    this.$nextTick(() => {
                        this.selectedId = $wire.get('rombel_id');
                    });
                },
                select(id, label) {
                    this.selectedId = id;
                    this.selectedLabel = label;
                    $wire.set('rombel_id', id);
                    this.open = false;
                }
            }" 
            @click.outside="open = false"
            class="relative w-full">

                <h1 class="text-sm text-gray-600">Kelas</h1>

                <!-- Button -->
                <div @click="open = !open"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-white border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">

                    <span x-text="selectedLabel ? selectedLabel : 'Pilih Kelas'" class="text-gray-700 text-sm"></span>

                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24">
                    </iconify-icon>
                </div>
                

                <!-- Dropdown -->
                <div x-show="open" x-cloak x-transition
                    class="absolute mt-2 w-full h-52 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto z-50 scroll-thin">

                    @foreach ($rombel as $r)
                        <div @click="select({{ $r->id }}, @js($r->nama_lengkap))"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center"
                            :class="selectedId == {{ $r->id }} ? 'bg-blue-deep-solid text-white' : ''">

                            <span>{{ $r->nama_lengkap }}</span>

                            <iconify-icon x-show="selectedId == {{ $r->id }}" icon="lineicons:check"
                                width="24" height="24"></iconify-icon>
                        </div>
                    @endforeach

                </div>

                @error('rombel_id')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Jenis Kelamin -->
            <div x-data="{
                    open: false,
                    selected: null,

                    init() {
                        this.selected = $wire.get('jk');
                        $wire.watch('jk', (val) => {
                            this.selected = val;
                        });
                    },

                    label() {
                        return this.selected === 'L' ? 'Laki-laki' : (this.selected === 'P' ? 'Perempuan' : 'Pilih Jenis Kelamin');
                    },

                    select(val) {
                        this.selected = val;
                        $wire.set('jk', val);
                        this.open = false;
                    }
                }" 
                class="relative w-full">
                    <h1 class="text-sm text-gray-600">Jenis Kelamin</h1>
                    <!-- Button -->
                    <div @click="open = !open"
                        class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-white border border-gray-300 rounded-xl cursor-pointer  hover:border-blue-500 transition">

                        <span x-text="label() ? label() : 'Pilih Jenis Kelamin'" class="text-gray-700 text-sm"></span>

                        <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                            icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                    </div>

                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
                        class="absolute mt-2 w-full h-20 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
                        <div @click="select('L')"
                            class="px-4 py-2  cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                            <span>Laki-laki</span>

                            <iconify-icon x-show="selected === 'L'" icon="lineicons:check" width="24"
                                height="24"></iconify-icon>
                        </div>

                        <div @click="select('P')"
                            class="px-4 py-2  cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                            <span>Perempuan</span>

                            <iconify-icon x-show="selected === 'P'" icon="lineicons:check" width="24"
                                height="24"></iconify-icon>
                        </div>
                    </div>

                    @error('jk')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
            </div>

            <!-- No HP -->
            <div>
                <label class="text-sm text-gray-600">No HP</label>
                <input type="text" wire:model.defer="hp"
                    class=" capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Contoh : 0812345678">
                @error('hp')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- email -->
            <div>
                <label class="text-sm text-gray-600">Email</label>
                <input type="email" wire:model.defer="email"
                    class="  mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="contoh@gmail.com">
                @error('email')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- tempat lahir -->
            <div>
                <label class="text-sm text-gray-600">Tempat Lahir</label>
                <input type="text" wire:model.defer="tempat_lahir"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Tempat lahir">
                @error('tempat_lahir')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tanggal lahir -->
            <div>
                <label class="text-sm text-gray-600">Tanggal Lahir</label>
                <input type="date" wire:model.defer="tanggal_lahir"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2">
                @error('tanggal_lahir')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- agama -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Agama</label>
                <input type="text" wire:model.defer="agama"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Agama">
                @error('agama')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- alamat -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Alamat</label>
                <input type="text" wire:model.defer="alamat"
                    class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="JL. KOMP. Contoh NO. 126">
                @error('alamat')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- RT -->
            <div>
                <label class="text-sm text-gray-600 ">RT</label>
                <input type="number" wire:model.defer="rt"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Contoh : 6">
                @error('rt')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- RW -->
            <div>
                <label class="text-sm text-gray-600 ">RW</label>
                <input type="number" wire:model.defer="rw"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Contoh : 2">
                @error('rw')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kelurahan -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Kelurahan</label>
                <input type="text" wire:model.defer="kelurahan"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Contoh : Pemurus Luar">
                @error('kelurahan')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- kecamatan -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Kecamatan</label>
                <input type="text" wire:model.defer="kecamatan"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Contoh : Kec. Banjarmasin Timur">
                @error('kecamatan')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- nama ayah -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Nama Ayah</label>
                <input type="text" wire:model.defer="nama_ayah"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Nama Ayah">
                @error('nama_ayah')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- nama ibu -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Nama Ibu</label>
                <input type="text" wire:model.defer="nama_ibu"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Nama Ibu">
                @error('nama_ibu')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- nama wali -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Nama Wali</label>
                <input type="text" wire:model.defer="nama_wali"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="Nama Wali">
                @error('nama_wali')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
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
