<div class="mb-20" >
    <!-- btn kembali -->
    <a href="{{ route('manajemen-user') }}" wire:navigate>
        <button
            class="px-4 py-2 rounded-lg bg-blue-deep-solid text-white transition-all duration-200 hover:bg-blue-deep active:scale-95 flex items-center justify-center capitalize mb-5">
            <iconify-icon icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
            kembali
        </button>
    </a>
        <!-- wrap form -->
    <div class="bg-white p-4 rounded-xl shadow-sm">

        <form wire:submit.prevent="store" class="p-6 flex flex-col gap-2">

            <!-- Nama -->
            <div>
                <label class="text-sm text-gray-600 capitalize">Username</label>
                <input type="text" wire:model.defer="nama"
                    class="capitalize mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="examplename">
                @error('nama')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- email -->
            <div>
                <label class="text-sm text-gray-600">Email</label>
                <input type="email" wire:model.defer="email"
                    class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    placeholder="contoh@gmail.com">
                @error('email')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- password -->
            <div class="relative" x-data="{open : true}" >
                <label class="text-sm text-gray-600">Password</label>
                <input type="password" :type=" open ? 'password' : 'text' " placeholder="Password" class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2" >
                <div class="absolute right-3 top-10 ">
                    <button type="button"  @click="open = !open" >
                        <iconify-icon :icon="open ? 'iconoir:eye-closed' : 'ri:eye-fill' " width="24" height="24" class="transition-transform duration-200 text-gray-400" ></iconify-icon>
                    </button>
                </div>
            </div>

            <!-- repeat password -->
            <div class="relative" x-data="{open : true}" >
                <label class="text-sm text-gray-600">Ulangi Password</label>
                <input type="password" :type=" open ? 'password' : 'text' " placeholder="Masukkan Ulang Password" class="mt-1 w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2" >
                <div class="absolute right-3 top-10 ">
                    <button type="button"  @click="open = !open" >
                        <iconify-icon :icon="open ? 'iconoir:eye-closed' : 'ri:eye-fill' " width="24" height="24" class="transition-transform duration-200 text-gray-400" ></iconify-icon>
                    </button>
                </div>
            </div>


            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <!-- ROLE -->
                <div 
                    x-data="{
                        open: false,
                        selectedId: null,
                        selectedLabel: 'Pilih Role',
    
                        select(id, label){
                            this.selectedId = id
                            this.selectedLabel = label
                            this.open = false
                        },
    
                        toggle(){
                            this.open = !this.open
                        }
                    }"
                    class="relative w-full"
                >   
                    <label class="text-sm text-gray-600">Role</label>
    
                    <!-- trigger -->
                    <div 
                        @click="toggle()"
                        class="text-sm mt-1 flex items-center justify-between px-4 py-2  border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition"
                    >
                        <span x-text="selectedLabel" class="text-gray-700"></span>
    
                        <iconify-icon 
                            class="text-gray-400 transition-transform" 
                            :class="{ 'rotate-180': open }"
                            icon="lineicons:chevron-up" 
                            width="20" 
                            height="20">
                        </iconify-icon>
                    </div>
    
                    <!-- dropdown -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute mt-2 w-full max-h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50"
                    >
                        <!-- pilih -->
                        <div
                            @click.prevent="select(null, 'pilih role')"
                            class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition"
                        >
                            <span>pilih role</span>
                            <iconify-icon 
                                x-show="selectedId === null" 
                                icon="lineicons:check" 
                                width="20" 
                                height="20">
                            </iconify-icon>
                        </div>
    
                        <!-- list -->
                        @foreach (['operator', 'wali kelas', 'pengawas'] as $role)
                            <div
                                @click.prevent="select('{{ $role }}', '{{ $role }}')"
                                class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition"
                            >
                                <span>{{ $role }}</span>
    
                                <iconify-icon 
                                    x-show="selectedId === '{{ $role }}'" 
                                    icon="lineicons:check" 
                                    width="20" 
                                    height="20">
                                </iconify-icon>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- guru -->
                <div 
                    x-data="{
                        open: false,
                        selectedId: null,
                        selectedLabel: 'Pilih Guru',
    
                        select(id, label){
                            this.selectedId = id
                            this.selectedLabel = label
                            this.open = false
                        },
    
                        toggle(){
                            this.open = !this.open
                        }
                    }"
                    class="relative w-full"
                >   
                    <label class="text-sm text-gray-600">Hubungkan ke Guru (Opsional)</label>
    
                    <!-- trigger -->
                    <div 
                        @click="toggle()"
                        class="text-sm mt-1 flex items-center justify-between px-4 py-2  border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition"
                    >
                        <span x-text="selectedLabel" class="text-gray-700"></span>
    
                        <iconify-icon 
                            class="text-gray-400 transition-transform" 
                            :class="{ 'rotate-180': open }"
                            icon="lineicons:chevron-up" 
                            width="20" 
                            height="20">
                        </iconify-icon>
                    </div>
    
                    <!-- dropdown -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute mt-2 w-full max-h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50"
                    >
                        <!-- pilih -->
                        <div
                            @click.prevent="select(null, 'pilih role')"
                            class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition"
                        >
                            <span>pilih role</span>
                            <iconify-icon 
                                x-show="selectedId === null" 
                                icon="lineicons:check" 
                                width="20" 
                                height="20">
                            </iconify-icon>
                        </div>
    
                        <!-- list -->
                        @foreach (['dodi', 'putra', 'kiki'] as $role)
                            <div
                                @click.prevent="select('{{ $role }}', '{{ $role }}')"
                                class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-blue-deep-solid hover:text-white transition"
                            >
                                <span>{{ $role }}</span>
    
                                <iconify-icon 
                                    x-show="selectedId === '{{ $role }}'" 
                                    icon="lineicons:check" 
                                    width="20" 
                                    height="20">
                                </iconify-icon>
                            </div>
                        @endforeach
                    </div>
                </div>
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
