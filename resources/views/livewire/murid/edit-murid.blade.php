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

        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-cyan-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-5">

                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="fa7-solid:user-edit"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Edit Data Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Perbarui informasi identitas, kelas, alamat, dan data wali murid.
                    </p>
                </div>

            </div>

            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Murid
                </p>

                <h2 class="mt-1 text-lg font-bold text-white capitalize">
                    {{ $murid->nama ?? 'Data Murid' }}
                </h2>

            </div>

        </div>
    </div>

    <!-- FORM CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- CARD HEADER -->
        <div
            class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Form Edit Murid
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Pastikan semua data sudah benar sebelum menyimpan perubahan.
                </p>
            </div>

            <div
                class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-600">

                <iconify-icon
                    icon="solar:pen-bold"
                    width="18"
                    height="18">
                </iconify-icon>

                Sedang diedit

            </div>

        </div>

        <!-- FORM -->
        <form
            wire:submit.prevent="update"
            class="space-y-8 p-6">

            <!-- SECTION IDENTITAS -->
            <div>

                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">
                        <iconify-icon icon="solar:user-bold" width="22" height="22"></iconify-icon>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Identitas Murid
                        </h3>

                        <p class="text-sm text-gray-500">
                            Data utama murid yang tercatat di sistem.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <!-- Nama -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            wire:model.defer="nama"
                            required
                            placeholder="Masukkan nama lengkap"
                            class="{{ $inputClass }} capitalize">

                        @error('nama')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NISN -->
                    <div>
                        <label class="{{ $labelClass }}">
                            NISN
                        </label>

                        <input
                            type="number"
                            wire:model.defer="nisn"
                            required
                            placeholder="Contoh : 123456789"
                            class="{{ $inputClass }}">

                        @error('nisn')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIPD -->
                    <div>
                        <label class="{{ $labelClass }}">
                            NIPD
                        </label>

                        <input
                            type="number"
                            wire:model.defer="nipd"
                            required
                            placeholder="Contoh : 1234"
                            class="{{ $inputClass }}">

                        @error('nipd')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kelas -->
                    <div
                        x-data="{
                            open: false,
                            selectedId: @js((string) $murid->rombel_id),
                            selectedLabel: @js($murid->rombel?->nama_lengkap),

                            toggle() {
                                this.open = !this.open
                            },

                            select(id, label) {
                                this.selectedId = String(id)
                                this.selectedLabel = label
                                this.open = false

                                $wire.set('rombel_id', id)
                            }
                        }"
                        class="relative">

                        <label class="{{ $labelClass }}">
                            Kelas
                        </label>

                        <div
                            @click="toggle()"
                            class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-blue-main">

                                    <iconify-icon
                                        icon="solar:buildings-2-bold"
                                        width="20"
                                        height="20">
                                    </iconify-icon>

                                </div>

                                <span
                                    x-text="selectedLabel ?? 'Pilih kelas'"
                                    class="line-clamp-1 text-gray-700">
                                </span>

                            </div>

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
                            class="absolute z-50 mt-2 max-h-56 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin">

                            @foreach ($rombel as $r)
                            <div
                                @click="select('{{ $r->id }}', @js($r->nama_lengkap))"
                                class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white"
                                :class="selectedId === '{{ $r->id }}' ? 'bg-blue-main text-white' : ''">

                                <span>{{ $r->nama_lengkap }}</span>

                                <iconify-icon
                                    x-show="selectedId === '{{ $r->id }}'"
                                    icon="lineicons:check"
                                    width="18"
                                    height="18">
                                </iconify-icon>

                            </div>
                            @endforeach

                        </div>

                        @error('rombel_id')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror

                    </div>

                    <!-- Jenis Kelamin -->
                    <div
                        x-data="{
                            open: false,
                            selected: @js($murid->jk),

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

                            <span x-text="label()" class="text-gray-700"></span>

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

                    <!-- Tempat Lahir -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Tempat Lahir
                        </label>

                        <input
                            type="text"
                            wire:model.defer="tempat_lahir"
                            required
                            placeholder="Tempat lahir"
                            class="{{ $inputClass }} capitalize">

                        @error('tempat_lahir')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Tanggal Lahir
                        </label>

                        <input
                            type="date"
                            wire:model.defer="tanggal_lahir"
                            required
                            class="{{ $inputClass }}">

                        @error('tanggal_lahir')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agama -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Agama
                        </label>

                        <input
                            type="text"
                            wire:model.defer="agama"
                            required
                            placeholder="Agama"
                            class="{{ $inputClass }} capitalize">

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
                        <iconify-icon icon="solar:phone-bold" width="22" height="22"></iconify-icon>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Kontak
                        </h3>

                        <p class="text-sm text-gray-500">
                            Nomor HP dan email aktif murid.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <!-- No HP -->
                    <div>
                        <label class="{{ $labelClass }}">
                            No HP
                        </label>

                        <input
                            type="text"
                            wire:model.defer="hp"
                            required
                            placeholder="Contoh : 0812345678"
                            class="{{ $inputClass }}">

                        @error('hp')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Email
                        </label>

                        <input
                            type="email"
                            wire:model.defer="email"
                            required
                            placeholder="contoh@gmail.com"
                            class="{{ $inputClass }}">

                        @error('email')
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
                        <iconify-icon icon="solar:map-point-bold" width="22" height="22"></iconify-icon>
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

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">
                            Alamat Lengkap
                        </label>

                        <textarea
                            wire:model.defer="alamat"
                            required
                            rows="4"
                            placeholder="JL. KOMP. Contoh NO. 126"
                            class="{{ $inputClass }}"></textarea>

                        @error('alamat')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RT -->
                    <div>
                        <label class="{{ $labelClass }}">
                            RT
                        </label>

                        <input
                            type="number"
                            wire:model.defer="rt"
                            required
                            placeholder="Contoh : 6"
                            class="{{ $inputClass }}">

                        @error('rt')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RW -->
                    <div>
                        <label class="{{ $labelClass }}">
                            RW
                        </label>

                        <input
                            type="number"
                            wire:model.defer="rw"
                            required
                            placeholder="Contoh : 2"
                            class="{{ $inputClass }}">

                        @error('rw')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kelurahan -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Kelurahan
                        </label>

                        <input
                            type="text"
                            wire:model.defer="kelurahan"
                            required
                            placeholder="Contoh : Pemurus Luar"
                            class="{{ $inputClass }} capitalize">

                        @error('kelurahan')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Kecamatan
                        </label>

                        <input
                            type="text"
                            wire:model.defer="kecamatan"
                            required
                            placeholder="Contoh : Banjarmasin Timur"
                            class="{{ $inputClass }} capitalize">

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
                        <iconify-icon icon="solar:users-group-rounded-bold" width="22" height="22"></iconify-icon>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Data Orang Tua / Wali
                        </h3>

                        <p class="text-sm text-gray-500">
                            Informasi keluarga atau wali murid.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                    <!-- Nama Ayah -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Nama Ayah
                        </label>

                        <input
                            type="text"
                            wire:model.defer="nama_ayah"
                            required
                            placeholder="Nama ayah"
                            class="{{ $inputClass }} capitalize">

                        @error('nama_ayah')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Ibu -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Nama Ibu
                        </label>

                        <input
                            type="text"
                            wire:model.defer="nama_ibu"
                            required
                            placeholder="Nama ibu"
                            class="{{ $inputClass }} capitalize">

                        @error('nama_ibu')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Wali -->
                    <div>
                        <label class="{{ $labelClass }}">
                            Nama Wali
                        </label>

                        <input
                            type="text"
                            wire:model.defer="nama_wali"
                            required
                            placeholder="Nama wali"
                            class="{{ $inputClass }} capitalize">

                        @error('nama_wali')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            <!-- SECTION FOTO MURID -->
            <div
            x-data="{
                preview: null,
                fileName: null,

                setPreview(event) {
                    const file = event.target.files[0]

                    if (!file) {
                        return
                    }

                    this.fileName = file.name
                    this.preview = URL.createObjectURL(file)
                },

                clearPreview() {
                    this.preview = null
                    this.fileName = null
                    this.$refs.imageInput.value = null
                }
            }"
                class="border-t border-gray-200 pt-8">

                <div class="mb-5 flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-600">

                        <iconify-icon
                            icon="solar:camera-bold"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Foto Murid
                        </h3>

                        <p class="text-sm text-gray-500">
                            Perbarui foto murid dan lihat preview sebelum disimpan.
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[18rem_1fr]">

                    <!-- PREVIEW -->
                    <div
                        class="flex items-center justify-center rounded-3xl border border-dashed border-blue-300 bg-blue-50 p-5">

                        <div
                            class="relative h-72 w-56 overflow-hidden rounded-[2rem] border border-white bg-white shadow-sm">

                            <!-- preview baru -->
                            <img
                                x-show="preview"
                                :src="preview"
                                class="h-full w-full object-cover"
                                alt="Preview Foto Murid">

                            <!-- foto lama -->
                            <img
                                x-show="!preview"
                                src="{{ $murid->image_path ?? asset('assets/img/default-avatar.png') }}"
                                class="h-full w-full object-cover"
                                alt="Foto Murid Saat Ini">

                            <!-- upload loading -->
                            <div
                                wire:loading
                                wire:target="image"
                                class="absolute inset-0 flex items-center justify-center bg-white/80 backdrop-blur-sm">

                                <div class="flex flex-col items-center gap-2 text-blue-main">

                                    <iconify-icon
                                        icon="line-md:loading-twotone-loop"
                                        width="32"
                                        height="32">
                                    </iconify-icon>

                                    <p class="text-xs font-semibold">
                                        Mengupload...
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- UPLOAD CONTROL -->
                    <div
                        class="flex flex-col justify-center rounded-3xl border border-gray-200 bg-gray-50 p-5">

                        <div class="mb-5">

                            <h4 class="font-bold text-gray-800">
                                Upload Foto Baru
                            </h4>

                            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                Gunakan foto yang jelas dengan format JPG, PNG, atau WEBP.
                                Jika tidak memilih foto baru, foto lama tetap digunakan.
                            </p>

                        </div>

                        <!-- upload button -->
                        <label
                            class="group flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-dashed border-blue-300 bg-blue-50 px-5 py-5 text-sm font-semibold text-blue-main transition hover:bg-blue-main hover:text-white">

                            <iconify-icon
                                icon="solar:upload-bold"
                                width="20"
                                height="20"
                                class="transition group-hover:-translate-y-0.5">
                            </iconify-icon>

                            Pilih Foto Baru

                            <input
                                x-ref="imageInput"
                                type="file"
                                wire:model="image"
                                accept="image/*"
                                class="hidden"
                                @change="setPreview($event)">

                        </label>

                        <!-- file name -->
                        <div
                            x-show="fileName"
                            x-transition
                            class="mt-4 rounded-2xl border border-gray-200 bg-white p-4">

                            <div class="flex items-center justify-between gap-3">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">

                                        <iconify-icon
                                            icon="solar:gallery-check-bold"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-400">
                                            File dipilih
                                        </p>

                                        <p
                                            x-text="fileName"
                                            class="line-clamp-1 text-sm font-semibold text-gray-700">
                                        </p>
                                    </div>

                                </div>

                                <button
                                    type="button"
                                    @click="clearPreview()"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-100 text-rose-600 transition hover:bg-rose-500 hover:text-white">

                                    <iconify-icon
                                        icon="lineicons:xmark-circle"
                                        width="20"
                                        height="20">
                                    </iconify-icon>

                                </button>

                            </div>

                        </div>

                        @error('image')
                        <p class="{{ $errorClass }}">
                            {{ $message }}
                        </p>
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
                    wire:target="update,image"
                    class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                    <iconify-icon
                        wire:loading.remove
                        wire:target="update,image"
                        icon="lineicons:save"
                        width="20"
                        height="20"
                        class="transition group-hover:scale-110">
                    </iconify-icon>

                    <iconify-icon
                        wire:loading
                        wire:target="update,image"
                        icon="line-md:loading-twotone-loop"
                        width="20"
                        height="20">
                    </iconify-icon>

                    <span wire:loading.remove wire:target="update,image">
                        Simpan Perubahan
                    </span>

                    <span wire:loading wire:target="update,image">
                        Menyimpan...
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>