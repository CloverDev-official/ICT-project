<div class="mb-20 space-y-6">

    @php
        $labelClass = 'mb-2 block text-sm font-semibold text-gray-700';
        $errorClass = 'mt-2 text-sm text-rose-500';
    @endphp

    <!-- BACK -->
    <div>
        <a href="{{ route('manajemen-murid') }}" wire:navigate>
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
                        icon="solar:gallery-add-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Tambah Foto Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Pilih nama murid lalu upload foto untuk data profil murid.
                    </p>
                </div>

            </div>

            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Form
                </p>

                <h2 class="mt-1 text-lg font-bold text-white">
                    Upload Foto
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
                    Form Foto Murid
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Pilih murid dan tambahkan gambar yang jelas.
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

            <!-- SELECT MURID -->
            <div
                x-data="{
                    open: false,
                    selectedId: @entangle('murid_id'),
                    selectedLabel: @entangle('selectedLabel'),

                    toggle() {
                        this.open = !this.open
                    },

                    select(id, label) {
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false
                    }
                }"
                class="relative">

                <label class="{{ $labelClass }}">
                    Nama Murid
                </label>

                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-main">

                            <iconify-icon
                                icon="solar:user-bold"
                                width="22"
                                height="22">
                            </iconify-icon>

                        </div>

                        <span
                            x-text="selectedLabel ?? 'Pilih nama murid'"
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

                <!-- DROPDOWN -->
                <div
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    style="display:none"
                    class="absolute z-50 mt-2 max-h-64 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin">

                    <div
                        @click.prevent="select(null, 'Pilih nama murid')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                        <span>Pilih nama murid</span>

                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>

                    @foreach ($listMurid ?? [] as $murid)

                        <div
                            @click.prevent="select({{ (int) $murid->id }}, @js($murid->nama . ' - ' . $murid->nipd))"
                            class="flex cursor-pointer items-center justify-between gap-4 px-4 py-3 transition hover:bg-blue-main hover:text-white">

                            <div>
                                <h4 class="font-semibold capitalize">
                                    {{ $murid->nama }}
                                </h4>

                                <p class="text-xs opacity-70">
                                    NIPD : {{ $murid->nipd }}
                                </p>
                            </div>

                            <iconify-icon
                                x-show="selectedId == {{ (int) $murid->id }}"
                                icon="lineicons:check"
                                width="18"
                                height="18">
                            </iconify-icon>

                        </div>

                    @endforeach

                </div>

                @error('murid_id')
                    <p class="{{ $errorClass }}">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <!-- UPLOAD FOTO -->
            <div
                x-data="{
                    preview: null,
                    fileName: null,
                    fileSize: null,
                    existingImage: @entangle('selectedImagePath'),

                    formatSize(bytes) {
                        if (!bytes) return '0 MB'

                        const size = bytes / 1024 / 1024
                        return size.toFixed(2) + ' MB'
                    },

                    setPreview(event) {
                        const file = event.target.files[0]

                        if (!file) {
                            return
                        }

                        this.preview = URL.createObjectURL(file)
                        this.fileName = file.name
                        this.fileSize = this.formatSize(file.size)
                    },

                    clearPreview() {
                        this.preview = null
                        this.fileName = null
                        this.fileSize = null

                        if (this.$refs.imageInput) {
                            this.$refs.imageInput.value = null
                        }
                    }
                }">

                <label class="{{ $labelClass }}">
                    Gambar Murid
                </label>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[18rem_1fr]">

                    <!-- PREVIEW -->
                    <div
                        class="flex items-center justify-center rounded-3xl border border-dashed border-blue-300 bg-blue-50 p-5">

                        <div
                            class="relative h-72 w-56 overflow-hidden rounded-[2rem] border border-white bg-white shadow-sm">

                            <!-- preview -->
                            <img
                                x-show="preview"
                                :src="preview"
                                class="h-full w-full object-cover"
                                alt="Preview Foto Murid">

                            <img
                                x-show="!preview && existingImage"
                                :src="existingImage"
                                class="h-full w-full object-cover"
                                alt="Foto Murid Saat Ini">

                            <!-- empty -->
                            <div
                                x-show="!preview && !existingImage"
                                class="flex h-full w-full flex-col items-center justify-center gap-3 bg-gray-50 text-center">

                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-blue-main">

                                    <iconify-icon
                                        icon="solar:user-bold"
                                        width="34"
                                        height="34">
                                    </iconify-icon>

                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-gray-700">
                                        Belum ada foto
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Preview akan tampil di sini
                                    </p>
                                </div>

                            </div>

                            <!-- loading -->
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

                    <!-- INPUT -->
                    <div
                        class="flex flex-col justify-center rounded-3xl border border-gray-200 bg-gray-50 p-5">

                        <div class="mb-5">

                            <h4 class="font-bold text-gray-800">
                                Upload Foto Murid
                            </h4>

                            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                Gunakan foto yang jelas dengan format JPG, PNG, atau WEBP.
                                Disarankan foto portrait agar tampilan lebih rapi.
                            </p>

                        </div>

                        <label
                            class="group flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-dashed border-blue-300 bg-blue-50 px-5 py-5 text-sm font-semibold text-blue-main transition hover:bg-blue-main hover:text-white">

                            <iconify-icon
                                icon="solar:upload-bold"
                                width="20"
                                height="20"
                                class="transition group-hover:-translate-y-0.5">
                            </iconify-icon>

                            Pilih Gambar Murid

                            <input
                                x-ref="imageInput"
                                type="file"
                                wire:model="image"
                                accept="image/*"
                                class="hidden"
                                @change="setPreview($event)">

                        </label>

                        <!-- FILE INFO -->
                        <div
                            x-show="fileName"
                            x-transition
                            class="mt-4 rounded-2xl border border-gray-200 bg-white p-4">

                            <div class="flex items-center justify-between gap-3">

                                <div class="flex min-w-0 items-center gap-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">

                                        <iconify-icon
                                            icon="solar:gallery-check-bold"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-400">
                                            File dipilih
                                        </p>

                                        <p
                                            x-text="fileName"
                                            class="truncate text-sm font-semibold text-gray-700">
                                        </p>

                                        <p
                                            x-text="fileSize"
                                            class="mt-0.5 text-xs text-gray-400">
                                        </p>
                                    </div>

                                </div>

                                <button
                                    type="button"
                                    @click="clearPreview()"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-600 transition hover:bg-rose-500 hover:text-white">

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

            <!-- INFO -->
            <div
                class="rounded-3xl border border-blue-100 bg-blue-50 p-5">

                <div class="flex gap-4">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-main text-white">

                        <iconify-icon
                            icon="solar:info-circle-bold"
                            width="24"
                            height="24">
                        </iconify-icon>

                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            Catatan
                        </h3>

                        <p class="mt-1 text-sm leading-relaxed text-gray-500">
                            Foto akan disimpan berdasarkan murid yang dipilih. Pastikan nama murid dan gambar sudah benar sebelum menyimpan data.
                        </p>
                    </div>

                </div>

            </div>

            <!-- ACTION -->
            <div
                class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">

                <a href="{{ route('manajemen-murid') }}" wire:navigate>
                    <button
                        type="button"
                        class="w-full rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-auto">

                        Batal

                    </button>
                </a>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="store,image"
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
                        Simpan Foto
                    </span>

                    <span wire:loading wire:target="store,image">
                        Menyimpan...
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>