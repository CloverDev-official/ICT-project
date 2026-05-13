<div class="space-y-6">

    <!-- BACK BUTTON -->
    <div class="flex items-center justify-between">

        <a href="{{ route('data-murid') }}" wire:navigate>

            <button
                class="group flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-main hover:text-blue-main hover:shadow-md">

                <iconify-icon
                    icon="lineicons:chevron-left"
                    width="20"
                    height="20"
                    class="transition-transform duration-200 group-hover:-translate-x-1">
                </iconify-icon>

                Kembali

            </button>

        </a>

    </div>

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-lg">

        <!-- effect -->
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-white/5"></div>

        <div
            class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

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
                        Lengkapi data siswa untuk ditambahkan ke sistem absensi.
                    </p>

                </div>

            </div>

            <!-- info -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Status
                </p>

                <h2 class="mt-1 text-lg font-bold text-white">
                    Form Input Data
                </h2>

            </div>

        </div>

    </div>

    <!-- FORM -->
    <div
        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- top -->
        <div
            class="border-b border-gray-200 bg-gray-50 px-6 py-5">

            <div
                class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                <div>

                    <h2 class="text-xl font-bold text-gray-800">
                        Informasi Murid
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Pastikan data yang dimasukkan sudah benar dan lengkap.
                    </p>

                </div>

                <!-- badge -->
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

        </div>

        <!-- form -->
        <form
            wire:submit.prevent="store"
            class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

            <!-- nama -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    wire:model.defer="nama"
                    placeholder="Masukkan nama lengkap"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm capitalize transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

                @error('nama')
                <div class="mt-2 text-sm text-rose-500">
                    {{ $message }}
                </div>
                @enderror

            </div>

            <!-- nisn -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    NISN
                </label>

                <input
                    type="number"
                    wire:model.defer="nisn"
                    placeholder="Contoh : 123456789"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

                @error('nisn')
                <div class="mt-2 text-sm text-rose-500">
                    {{ $message }}
                </div>
                @enderror

            </div>

            <!-- nipd -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    NIPD
                </label>

                <input
                    type="number"
                    wire:model.defer="nipd"
                    placeholder="Contoh : 1234"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

                @error('nipd')
                <div class="mt-2 text-sm text-rose-500">
                    {{ $message }}
                </div>
                @enderror

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
                        $wire.set('rombel_id', id)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Kelas
                </label>

                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 transition hover:border-blue-main hover:bg-white">

                    <span
                        x-text="selectedLabel ?? 'Pilih kelas'"
                        class="text-sm text-gray-700">
                    </span>

                    <iconify-icon
                        icon="lineicons:chevron-up"
                        width="20"
                        height="20"
                        class="text-gray-400 transition-transform"
                        :class="{ 'rotate-180': open }">
                    </iconify-icon>

                </div>

                <!-- dropdown -->
                <div
                    x-show="open"
                    @click.outside="toggle()"
                    x-transition
                    style="display:none"
                    class="absolute z-50 mt-2 max-h-52 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl">

                    @foreach ($rombel as $r)

                    <div
                        @click="select({{ $r->id }}, @js($r->nama_lengkap))"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                        <span>{{ $r->nama_lengkap }}</span>

                        <iconify-icon
                            x-show="selectedId == {{ $r->id }}"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>

                    @endforeach

                </div>

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

                    toggle() {
                        this.open = !this.open
                    },

                    select(val) {
                        this.selected = val
                        this.open = false
                        $wire.set('jk', val)
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Jenis Kelamin
                </label>

                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 transition hover:border-blue-main hover:bg-white">

                    <span
                        x-text="label()"
                        class="text-sm text-gray-700">
                    </span>

                    <iconify-icon
                        icon="lineicons:chevron-up"
                        width="20"
                        height="20"
                        class="text-gray-400 transition-transform"
                        :class="{ 'rotate-180': open }">
                    </iconify-icon>

                </div>

                <!-- Dropdown -->
                <div x-show="open" @click.outside="toggle()" x-transition style="display: none;"
                    class="absolute mt-2 w-full h-20 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">

                    @foreach (['L', 'P'] as $jk )

                    <div @click="select('{{ $jk }}')"
                        class="px-4 py-2  cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">

                        <span>{{ $jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>

                        <iconify-icon x-show="selected === '{{ $jk }}'" icon="lineicons:check" width="24"
                            height="24"></iconify-icon>
                    </div>

                    @endforeach
                </div>

                @error('jk')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror

            </div>

            <!-- tempat lahir -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tempat Lahir
                </label>

                <input
                    type="text"
                    wire:model.defer="tempat_lahir"
                    placeholder="Tempat lahir"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm capitalize transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

            </div>

            <!-- tanggal lahir -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tanggal Lahir
                </label>

                <input
                    type="date"
                    wire:model.defer="tanggal_lahir"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

            </div>

            <!-- email -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Email
                </label>

                <input
                    type="email"
                    wire:model.defer="email"
                    placeholder="contoh@gmail.com"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

            </div>

            <!-- hp -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    No HP
                </label>

                <input
                    type="text"
                    wire:model.defer="hp"
                    placeholder="08123456789"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

            </div>

            <!-- agama -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Agama
                </label>

                <input
                    type="text"
                    wire:model.defer="agama"
                    placeholder="Agama"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                @error('agama')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- rt -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    RT
                </label>

                <input
                    type="text"
                    wire:model.defer="rt"
                    placeholder="Contoh: 6"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                @error('rt')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- rw -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    RW
                </label>

                <input
                    type="text"
                    wire:model.defer="rw"
                    placeholder="Contoh: 2"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                @error('rw')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- kelurahan -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Kelurahan
                </label>

                <input
                    type="text"
                    wire:model.defer="kelurahan"
                    placeholder="Contoh: Pemurus Luar"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                @error('kelurahan')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- kecamatan -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Kecamatan
                </label>

                <input
                    type="text"
                    wire:model.defer="kecamatan"
                    placeholder="Contoh : Kec. Banjarmasin Timur"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                @error('kecamatan')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- nama Ibu -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Nama Ayah
                </label>

                <input
                    type="text"
                    wire:model.defer="nama_ayah"
                    placeholder="Nama Ayah"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                @error('nama_ayah')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- nama ibu -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Nama Ibu
                </label>

                <input
                    type="text"
                    wire:model.defer="nama_ibu"
                    placeholder="Nama ibu"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                @error('nama_ibu')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- nama ibu -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Nama Wali
                </label>

                <input
                    type="text"
                    wire:model.defer="nama_wali"
                    placeholder="Nama Wali"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                @error('nama_wali')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- alamat -->
            <div class="md:col-span-2">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Alamat
                </label>

                <textarea
                    wire:model.defer="alamat"
                    rows="4"
                    placeholder="Masukkan alamat lengkap"
                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100"></textarea>

            </div>

            <!-- RFID -->
            <div class="md:col-span-2">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    RFID Code
                </label>

                <div
                    class="flex items-center gap-3 rounded-2xl border border-dashed border-blue-300 bg-blue-50 px-4 py-4">

                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-main text-white">

                        <iconify-icon
                            icon="solar:card-bold"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </div>

                    <input
                        type="text"
                        name="rfid_code"
                        placeholder="Tempelkan kartu RFID"
                        class="w-full bg-transparent text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none" />

                </div>

            </div>

            <!-- button -->
            <div
                class="flex justify-end gap-3 border-t border-gray-200 pt-6 md:col-span-2">

                <!-- cancel -->
                <a href="{{ route('data-murid') }}" wire:navigate>

                    <button
                        type="button"
                        class="rounded-2xl border border-gray-300 bg-white px-6 py-3 font-medium text-gray-700 transition hover:bg-gray-100">

                        Batal

                    </button>

                </a>

                <!-- submit -->
                <button
                    type="submit"
                    class="group flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">

                    <iconify-icon
                        icon="lineicons:save"
                        width="20"
                        height="20"
                        class="transition duration-200 group-hover:scale-110">
                    </iconify-icon>

                    Simpan Data

                </button>

            </div>

        </form>

    </div>

</div>