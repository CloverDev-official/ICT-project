<div
    x-data="{
        openModalImportImage: false,
        dragActive: false,
        fileName: '',
        fileSize: '',
        uploadProgress: 0,
        importProgress: 0,
        isUploading: false,
        isImporting: false,
        hasResult: false,

        updatedImageCount: 0,
        notFoundImageCount: 0,
        failedImageCount: 0,

        get hasFile() {
            return this.fileName.length > 0
        },

        formatSize(bytes) {
            if (!bytes) return '0 MB'

            const size = bytes / 1024 / 1024
            return size.toFixed(2) + ' MB'
        },

        setFile(file) {
            if (!file) return

            const isZip = file.name.toLowerCase().endsWith('.zip')

            if (!isZip) {
                alert('File harus berformat .zip')
                return
            }

            this.fileName = file.name
            this.fileSize = this.formatSize(file.size)
            this.uploadProgress = 0
            this.importProgress = 0
            this.hasResult = false

            this.simulateUpload()
        },

        setFileFromInput(event) {
            this.setFile(event.target.files[0])
        },

        setFileFromDrop(event) {
            const file = event.dataTransfer.files[0]

            if (!file) return

            this.setFile(file)

            if (this.$refs.zipInput) {
                this.$refs.zipInput.files = event.dataTransfer.files
            }
        },

        clearFile() {
            this.fileName = ''
            this.fileSize = ''
            this.uploadProgress = 0
            this.importProgress = 0
            this.isUploading = false
            this.isImporting = false
            this.hasResult = false
            this.updatedImageCount = 0
            this.notFoundImageCount = 0
            this.failedImageCount = 0

            if (this.$refs.zipInput) {
                this.$refs.zipInput.value = null
            }
        },

        closeModal() {
            this.openModalImportImage = false
            this.clearFile()
        },

        simulateUpload() {
            this.isUploading = true
            this.uploadProgress = 0

            const timer = setInterval(() => {
                this.uploadProgress += 10

                if (this.uploadProgress >= 100) {
                    this.uploadProgress = 100
                    this.isUploading = false
                    clearInterval(timer)
                }
            }, 80)
        },

        startImport() {
            if (!this.hasFile || this.isUploading) return

            this.isImporting = true
            this.hasResult = false
            this.importProgress = 0
            this.updatedImageCount = 0
            this.notFoundImageCount = 0
            this.failedImageCount = 0

            const timer = setInterval(() => {
                this.importProgress += 5

                if (this.importProgress >= 100) {
                    this.importProgress = 100
                    this.isImporting = false
                    this.hasResult = true

                    this.updatedImageCount = 42
                    this.notFoundImageCount = 3
                    this.failedImageCount = 1

                    clearInterval(timer)
                }
            }, 120)
        }
    }">

    <!-- TRIGGER BUTTON -->
    <button
        @click="openModalImportImage = true"
        class="w-full flex items-center justify-center gap-2 rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">

        <iconify-icon
            icon="line-md:image"
            width="22"
            height="22">
        </iconify-icon>

        Import Gambar

    </button>


    <!-- MODAL -->
    <div
        x-show="openModalImportImage"
        x-transition.opacity
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-md">

        <div
            @click.outside="closeModal()"
            x-transition.scale
            class="w-full max-w-3xl overflow-hidden rounded-[2rem] border border-white/20 bg-white shadow-2xl">

            <!-- HEADER -->
            <div
                class="relative overflow-hidden bg-gradient-to-r from-blue-main via-blue-deep to-[#07162f] px-6 py-5 text-white">

                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute bottom-0 left-0 h-28 w-28 rounded-full bg-cyan-400/10 blur-2xl"></div>

                <div class="relative flex items-center justify-between gap-4">

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/15 bg-white/10 backdrop-blur">

                            <iconify-icon
                                icon="mdi:image-plus"
                                width="28"
                                height="28">
                            </iconify-icon>

                        </div>

                        <div>
                            <h1 class="text-xl font-bold">
                                Import Foto Murid
                            </h1>

                            <p class="mt-1 text-sm text-blue-100">
                                Upload file ZIP berisi foto murid untuk diproses secara massal.
                            </p>
                        </div>

                    </div>

                    <button
                        type="button"
                        @click="closeModal()"
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-white transition hover:bg-white/20 active:scale-95">

                        <iconify-icon
                            icon="mdi:close"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </button>

                </div>

            </div>

            <!-- BODY -->
            <div class="max-h-[75vh] overflow-y-auto p-6 scroll-thin">

                <!-- INFO -->
                <div class="mb-5 rounded-3xl border border-blue-100 bg-blue-50 p-4">

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
                                Format ZIP
                            </h3>

                            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                Masukkan foto ke dalam file ZIP. Gunakan nama file sesuai NIPD atau NISN murid,
                                contoh <span class="font-semibold text-gray-700">12345.jpg</span>,
                                <span class="font-semibold text-gray-700">12345.png</span>,
                                atau <span class="font-semibold text-gray-700">12345.webp</span>.
                            </p>
                        </div>

                    </div>

                </div>

                <!-- UPLOAD AREA -->
                <div
                    class="group relative overflow-hidden rounded-3xl border-2 border-dashed border-gray-300 bg-gray-50 p-6 transition hover:border-blue-main hover:bg-blue-50/40"
                    :class="dragActive ? 'border-blue-main bg-blue-50' : ''"
                    @click="if ($refs.zipInput) { $refs.zipInput.click() }"
                    @dragenter.prevent="dragActive = true"
                    @dragover.prevent="dragActive = true"
                    @dragleave.prevent="dragActive = false"
                    @drop.prevent="dragActive = false; setFileFromDrop($event)">

                    <input
                        type="file"
                        x-ref="zipInput"
                        accept=".zip,application/zip,application/x-zip-compressed"
                        class="hidden"
                        @change="setFileFromInput($event)">

                    <!-- EMPTY STATE -->
                    <div
                        x-show="!hasFile"
                        class="flex min-h-72 flex-col items-center justify-center text-center">

                        <div
                            class="mb-5 flex h-24 w-24 items-center justify-center rounded-[2rem] bg-white text-blue-main shadow-sm transition group-hover:-translate-y-1">

                            <iconify-icon
                                icon="solar:cloud-upload-bold"
                                width="48"
                                height="48">
                            </iconify-icon>

                        </div>

                        <h2 class="text-xl font-bold text-gray-800">
                            Upload File ZIP
                        </h2>

                        <p class="mt-2 max-w-md text-sm leading-relaxed text-gray-500">
                            Drag & drop file ZIP di area ini atau klik tombol di bawah untuk memilih file.
                        </p>

                        <div
                            class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-blue-main px-5 py-3 text-sm font-semibold text-white shadow-lg transition group-hover:bg-blue-deep-solid">

                            <iconify-icon
                                icon="solar:folder-open-bold"
                                width="20"
                                height="20">
                            </iconify-icon>

                            Pilih File ZIP

                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Format yang didukung: .zip
                        </p>

                    </div>

                    <!-- FILE SELECTED -->
                    <div
                        x-show="hasFile"
                        x-transition
                        class="flex min-h-72 flex-col items-center justify-center text-center">

                        <div
                            class="mb-5 flex h-24 w-24 items-center justify-center rounded-[2rem] bg-amber-50 text-amber-600 shadow-sm">

                            <iconify-icon
                                icon="solar:archive-bold"
                                width="50"
                                height="50">
                            </iconify-icon>

                        </div>

                        <h2 class="text-xl font-bold text-gray-800">
                            ZIP Siap Diimport
                        </h2>

                        <div
                            class="mt-5 w-full max-w-md rounded-3xl border border-gray-200 bg-white p-4 shadow-sm">

                            <div class="flex items-center justify-between gap-4">

                                <div class="flex min-w-0 items-center gap-3">

                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">

                                        <iconify-icon
                                            icon="solar:file-check-bold"
                                            width="24"
                                            height="24">
                                        </iconify-icon>

                                    </div>

                                    <div class="min-w-0 text-left">

                                        <p
                                            x-text="fileName"
                                            class="truncate text-sm font-semibold text-gray-800">
                                        </p>

                                        <p
                                            x-text="fileSize"
                                            class="mt-1 text-xs text-gray-400">
                                        </p>

                                    </div>

                                </div>

                                <button
                                    type="button"
                                    @click.stop="clearFile()"
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 transition hover:bg-rose-500 hover:text-white">

                                    <iconify-icon
                                        icon="lineicons:xmark-circle"
                                        width="22"
                                        height="22">
                                    </iconify-icon>

                                </button>

                            </div>

                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Klik area upload untuk mengganti file.
                        </p>

                    </div>

                    <!-- UPLOAD PROGRESS -->
                    <div
                        x-show="isUploading"
                        x-transition
                        class="absolute inset-x-6 bottom-6 rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">

                        <div class="mb-2 flex items-center justify-between text-xs font-semibold text-gray-600">

                            <span>Mengupload ZIP...</span>

                            <span x-text="uploadProgress + '%'"></span>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-gray-200">

                            <div
                                class="h-full rounded-full bg-gradient-to-r from-blue-main to-blue-deep transition-all duration-300"
                                :style="`width: ${uploadProgress}%`">
                            </div>

                        </div>

                    </div>

                </div>

                <!-- IMPORT PROGRESS -->
                <div
                    x-show="isImporting"
                    x-transition
                    class="mt-5 rounded-3xl border border-blue-100 bg-blue-50 p-5">

                    <div class="mb-4 flex items-center justify-between gap-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-main text-white">

                                <iconify-icon
                                    icon="solar:gallery-bold"
                                    width="24"
                                    height="24">
                                </iconify-icon>

                            </div>

                            <div>
                                <h3 class="font-bold text-gray-800">
                                    Sedang Memproses Foto
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Sistem sedang membaca ZIP dan mencocokkan foto dengan data murid.
                                </p>
                            </div>

                        </div>

                        <div
                            class="rounded-full bg-white px-4 py-2 text-sm font-bold text-blue-main shadow-sm">

                            <span x-text="importProgress + '%'"></span>

                        </div>

                    </div>

                    <div class="mb-2 flex justify-between text-xs font-semibold text-gray-500">

                        <span>
                            Progress import foto
                        </span>

                        <span x-text="importProgress + '%'"></span>

                    </div>

                    <div class="h-3 overflow-hidden rounded-full bg-white">

                        <div
                            class="h-full rounded-full bg-gradient-to-r from-blue-main to-blue-deep transition-all duration-300"
                            :style="`width: ${importProgress}%`">
                        </div>

                    </div>

                </div>

                <!-- RESULT -->
                <div
                    x-show="hasResult"
                    x-transition
                    class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">

                    <!-- berhasil -->
                    <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500 text-white">

                                <iconify-icon
                                    icon="solar:check-circle-bold"
                                    width="22"
                                    height="22">
                                </iconify-icon>

                            </div>

                            <div>
                                <p class="text-xs text-emerald-600">
                                    Berhasil Update
                                </p>

                                <h3
                                    x-text="updatedImageCount"
                                    class="text-xl font-bold text-emerald-700">
                                </h3>
                            </div>

                        </div>

                    </div>

                    <!-- tidak cocok -->
                    <div class="rounded-3xl border border-amber-100 bg-amber-50 p-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-500 text-white">

                                <iconify-icon
                                    icon="solar:user-cross-bold"
                                    width="22"
                                    height="22">
                                </iconify-icon>

                            </div>

                            <div>
                                <p class="text-xs text-amber-600">
                                    Tidak Cocok
                                </p>

                                <h3
                                    x-text="notFoundImageCount"
                                    class="text-xl font-bold text-amber-700">
                                </h3>
                            </div>

                        </div>

                    </div>

                    <!-- gagal -->
                    <div class="rounded-3xl border border-rose-100 bg-rose-50 p-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-500 text-white">

                                <iconify-icon
                                    icon="solar:danger-triangle-bold"
                                    width="22"
                                    height="22">
                                </iconify-icon>

                            </div>

                            <div>
                                <p class="text-xs text-rose-600">
                                    Gagal
                                </p>

                                <h3
                                    x-text="failedImageCount"
                                    class="text-xl font-bold text-rose-700">
                                </h3>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div
                class="flex flex-col gap-4 border-t border-gray-200 bg-gray-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="inline-flex items-center gap-2 text-sm text-gray-500">

                    <iconify-icon
                        icon="solar:archive-bold"
                        width="20"
                        height="20">
                    </iconify-icon>

                    Gunakan ZIP berisi JPG, PNG, atau WEBP.

                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">

                    <button
                        type="button"
                        @click="closeModal()"
                        class="rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">

                        Batal

                    </button>

                    <button
                        type="button"
                        @click="startImport()"
                        :disabled="!hasFile || isUploading || isImporting"
                        class="group flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-60">

                        <iconify-icon
                            x-show="!isImporting"
                            icon="solar:upload-bold"
                            width="20"
                            height="20"
                            class="transition group-hover:-translate-y-0.5">
                        </iconify-icon>

                        <iconify-icon
                            x-show="isImporting"
                            icon="line-md:loading-twotone-loop"
                            width="20"
                            height="20">
                        </iconify-icon>

                        <span x-show="!isImporting">
                            Import Foto
                        </span>

                        <span x-show="isImporting">
                            Mengimport...
                        </span>

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>