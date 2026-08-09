<div class="mb-20 space-y-6">

    @php
    $labelClass = 'mb-2 block text-sm font-semibold text-gray-700';
    $inputClass = 'w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100';
    $errorClass = 'mt-2 text-sm text-rose-500';
    @endphp

    <!-- BACK -->
    <div>
        <a href="{{ route('data-murid') }}" wire:navigate>
            <button
                class="group flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-main hover:text-blue-main hover:shadow-md">

                <iconify-icon
                    icon="lineicons:chevron-left"
                    width="20"
                    height="20"
                    class="transition group-hover:-translate-x-1">
                </iconify-icon>

                Kembali

            </button>
        </a>
    </div>

    <!-- HERO -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main via-blue-deep to-[#07162f] p-6 shadow-lg">

        <!-- ornament -->
        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-cyan-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-5">

                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:user-plus-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Tambah Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Lengkapi identitas, kelas, alamat, dan data wali murid.
                    </p>
                </div>

            </div>

            <!-- status -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Status Form
                </p>

                <h2 class="mt-1 text-lg font-bold text-white">
                    Input Data Baru
                </h2>

            </div>

        </div>
    </div>

    <!-- FORM CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- CARD HEADER -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Form Data Murid
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Pastikan semua data sudah benar sebelum disimpan.
                </p>
            </div>

            <div
                class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                <iconify-icon
                    icon="solar:shield-check-bold"
                    width="18"
                    height="18">
                </iconify-icon>

                Data Aman

            </div>

        </div>

        <!-- FORM -->
        <form
            wire:submit.prevent="store"
            class="space-y-8 p-6">

            <!-- SECTION IDENTITAS -->
            <div>

                <div class="mb-5 flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">

                        <iconify-icon
                            icon="solar:user-bold"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Identitas Murid
                        </h3>

                        <p class="text-sm text-gray-500">
                            Data utama murid yang akan digunakan pada sistem.
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <!-- nama -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            wire:model.defer="nama"
                            placeholder="Masukkan nama lengkap"
                            class="{{ $inputClass }} capitalize" />

                        @error('nama')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- nisn -->
                    <div>
                        <label class="{{ $labelClass }}">
                            NISN
                        </label>

                        <input
                            type="number"
                            wire:model.defer="nisn"
                            placeholder="Contoh : 123456789"
                            class="{{ $inputClass }}" />

                        @error('nisn')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- nipd -->
                    <div>
                        <label class="{{ $labelClass }}">
                            NIPD
                        </label>

                        <input
                            type="number"
                            wire:model.defer="nipd"
                            placeholder="Contoh : 1234"
                            class="{{ $inputClass }}" />

                        @error('nipd')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- kelas -->
                    <div
                        x-data="{
                            open: false,
                            selectedId: null,
                            query: '',
                            kelas: @js($rombel->map(fn ($r) => ['id' => (string) $r->id, 'label' => $r->nama_lengkap])->values()),

                            get filteredKelas() {
                                const keyword = this.query.trim().toLowerCase()

                                return this.kelas.filter((item) => item.label.toLowerCase().includes(keyword))
                            },

                            search() {
                                this.selectedId = null
                                $wire.set('rombel_id', null)
                            },

                            select(id, label) {
                                this.selectedId = String(id)
                                this.query = label
                                this.open = false

                                $wire.set('rombel_id', id)
                            }
                        }"
                        class="relative">

                        <label class="{{ $labelClass }}">
                            Kelas
                        </label>

                        <div class="relative">
                            <iconify-icon
                                icon="solar:buildings-2-bold"
                                width="20"
                                height="20"
                                class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-blue-main">
                            </iconify-icon>

                            <input
                                type="search"
                                x-ref="search"
                                x-model="query"
                                @focus="open = true"
                                @input="open = true; search()"
                                @keydown.escape="open = false"
                                placeholder="Cari kelas..."
                                autocomplete="off"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 pl-12 pr-4 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100" />
                        </div>

                        <div
                            x-show="open"
                            x-transition
                            @click.outside="open = false"
                            style="display:none"
                            class="absolute z-50 mt-2 max-h-56 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin">

                            <template x-for="item in filteredKelas" :key="item.id">
                            <button
                                type="button"
                                @mousedown.prevent="select(item.id, item.label)"
                                class="flex w-full cursor-pointer items-center justify-between px-4 py-3 text-left transition hover:bg-blue-main hover:text-white"
                                :class="selectedId === item.id ? 'bg-blue-main text-white' : ''">

                                <span x-text="item.label"></span>

                                <iconify-icon
                                    x-show="selectedId === item.id"
                                    icon="lineicons:check"
                                    width="18"
                                    height="18">
                                </iconify-icon>

                            </button>
                            </template>

                            <p x-show="filteredKelas.length === 0" class="px-4 py-3 text-sm text-gray-500">
                                Kelas tidak ditemukan.
                            </p>

                        </div>

                        @error('rombel_id')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror

                    </div>

                    <!-- jenis kelamin -->
                    <div
                        x-data="{
                            open: false,
                            selected: null,

                            label() {
                                return this.selected === 'L'
                                    ? 'Laki-laki'
                                    : this.selected === 'P'
                                    ? 'Perempuan'
                                    : 'Pilih jenis kelamin'
                            },

                            select(value) {
                                this.selected = value
                                this.open = false

                                $wire.set('jk', value)
                            }
                        }"
                        class="relative">

                        <label class="{{ $labelClass }}">
                            Jenis Kelamin
                        </label>

                        <div
                            @click="open = !open"
                            class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                            <span
                                x-text="label()"
                                class="text-gray-700">
                            </span>

                            <iconify-icon
                                icon="lineicons:chevron-up"
                                width="20"
                                height="20"
                                class="text-gray-400 transition-transform"
                                :class="{ 'rotate-180': open }">
                            </iconify-icon>

                        </div>

                        <div
                            x-show="open"
                            x-transition
                            @click.outside="open = false"
                            style="display:none"
                            class="absolute z-50 mt-2 w-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">

                            @foreach (['L', 'P'] as $jk)
                            <div
                                @click="select('{{ $jk }}')"
                                class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                                <span>{{ $jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>

                                <iconify-icon
                                    x-show="selected === '{{ $jk }}'"
                                    icon="lineicons:check"
                                    width="18"
                                    height="18">
                                </iconify-icon>

                            </div>
                            @endforeach

                        </div>

                        @error('jk')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror

                    </div>

                    <!-- tempat lahir -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Tempat Lahir
                        </label>

                        <input
                            type="text"
                            wire:model.defer="tempat_lahir"
                            placeholder="Tempat lahir"
                            class="{{ $inputClass }} capitalize" />

                        @error('tempat_lahir')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- tanggal lahir -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Tanggal Lahir
                        </label>

                        <input
                            type="date"
                            wire:model.defer="tanggal_lahir"
                            class="{{ $inputClass }}" />

                        @error('tanggal_lahir')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- agama -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Agama
                        </label>

                        <input
                            type="text"
                            wire:model.defer="agama"
                            placeholder="Agama"
                            class="{{ $inputClass }} capitalize" />

                        @error('agama')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            <!-- SECTION KONTAK -->
            <div class="border-t border-gray-200 pt-8">

                <div class="mb-5 flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">

                        <iconify-icon
                            icon="solar:phone-bold"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Kontak Murid
                        </h3>

                        <p class="text-sm text-gray-500">
                            Nomor HP dan email yang bisa dihubungi.
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <!-- email -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Email
                        </label>

                        <input
                            type="email"
                            wire:model.defer="email"
                            placeholder="contoh@gmail.com"
                            class="{{ $inputClass }}" />

                        @error('email')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- hp -->
                    <div>
                        <label class="{{ $labelClass }}">
                            No HP
                        </label>

                        <input
                            type="text"
                            wire:model.defer="hp"
                            placeholder="08123456789"
                            class="{{ $inputClass }}" />

                        @error('hp')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            <!-- SECTION ALAMAT -->
            <div class="border-t border-gray-200 pt-8">

                <div class="mb-5 flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">

                        <iconify-icon
                            icon="solar:map-point-bold"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Alamat
                        </h3>

                        <p class="text-sm text-gray-500">
                            Detail alamat tempat tinggal murid.
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <!-- alamat -->
                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">
                            Alamat Lengkap
                        </label>

                        <textarea
                            wire:model.defer="alamat"
                            rows="4"
                            placeholder="Masukkan alamat lengkap"
                            class="{{ $inputClass }}"></textarea>

                        @error('alamat')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- rt -->
                    <div>
                        <label class="{{ $labelClass }}">
                            RT
                        </label>

                        <input
                            type="text"
                            wire:model.defer="rt"
                            placeholder="Contoh : 6"
                            class="{{ $inputClass }}" />

                        @error('rt')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- rw -->
                    <div>
                        <label class="{{ $labelClass }}">
                            RW
                        </label>

                        <input
                            type="text"
                            wire:model.defer="rw"
                            placeholder="Contoh : 2"
                            class="{{ $inputClass }}" />

                        @error('rw')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- kelurahan -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Kelurahan
                        </label>

                        <input
                            type="text"
                            wire:model.defer="kelurahan"
                            placeholder="Contoh : Pemurus Luar"
                            class="{{ $inputClass }} capitalize" />

                        @error('kelurahan')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- kecamatan -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Kecamatan
                        </label>

                        <input
                            type="text"
                            wire:model.defer="kecamatan"
                            placeholder="Contoh : Banjarmasin Timur"
                            class="{{ $inputClass }} capitalize" />

                        @error('kecamatan')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            <!-- SECTION ORANG TUA -->
            <div class="border-t border-gray-200 pt-8">

                <div class="mb-5 flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">

                        <iconify-icon
                            icon="solar:users-group-rounded-bold"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Orang Tua / Wali
                        </h3>

                        <p class="text-sm text-gray-500">
                            Informasi keluarga atau wali murid.
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                    <!-- nama ayah -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Nama Ayah
                        </label>

                        <input
                            type="text"
                            wire:model.defer="nama_ayah"
                            placeholder="Nama ayah"
                            class="{{ $inputClass }} capitalize" />

                        @error('nama_ayah')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- nama ibu -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Nama Ibu
                        </label>

                        <input
                            type="text"
                            wire:model.defer="nama_ibu"
                            placeholder="Nama ibu"
                            class="{{ $inputClass }} capitalize" />

                        @error('nama_ibu')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- nama wali -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Nama Wali
                        </label>

                        <input
                            type="text"
                            wire:model.defer="nama_wali"
                            placeholder="Nama wali"
                            class="{{ $inputClass }} capitalize" />

                        @error('nama_wali')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            <!-- ACTION -->
            <div
                class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">

                <a href="{{ route('data-murid') }}" wire:navigate>
                    <button
                        type="button"
                        class="w-full rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-auto">

                        Batal

                    </button>
                </a>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="store"
                    class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                    <iconify-icon
                        wire:loading.remove
                        wire:target="store,image"
                        icon="lineicons:save"
                        width="20"
                        height="20"
                        class="transition group-hover:scale-110">
                    </iconify-icon>

                    <iconify-icon
                        wire:loading
                        wire:target="store,image"
                        icon="line-md:loading-twotone-loop"
                        width="20"
                        height="20">
                    </iconify-icon>

                    <span wire:loading.remove wire:target="store,image">
                        Simpan Data
                    </span>

                    <span wire:loading wire:target="store,image">
                        Menyimpan...
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>
