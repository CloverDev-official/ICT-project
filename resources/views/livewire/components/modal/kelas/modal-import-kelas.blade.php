<div
    x-data="{
        dragActive: false,
        fileName: '',
        fileSize: '',
        isUploading: false,
        progress: 0,

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

            this.fileName = file.name
            this.fileSize = this.formatSize(file.size)
            this.progress = 0

            this.simulateUpload()
        },

        setFileFromInput(event) {
            this.setFile(event.target.files[0])
        },

        setFileFromDrop(event) {
            const file = event.dataTransfer.files[0]

            if (!file) return

            this.setFile(file)

            if (this.$refs.fileInput) {
                this.$refs.fileInput.files = event.dataTransfer.files
            }
        },

        clearFile() {
            this.fileName = ''
            this.fileSize = ''
            this.progress = 0
            this.isUploading = false

            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = null
            }
        },

        closeModal() {
            openModalImport = false
            this.clearFile()
        },

        simulateUpload() {
            this.isUploading = true
            this.progress = 0

            const timer = setInterval(() => {
                this.progress += 10

                if (this.progress >= 100) {
                    this.progress = 100
                    this.isUploading = false
                    clearInterval(timer)
                }
            }, 80)
        }
    }"
    x-show="openModalImport"
    x-transition.opacity
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-md">

    <!-- MODAL -->
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
                            icon="solar:file-download-bold"
                            width="28"
                            height="28">
                        </iconify-icon>

                    </div>

                    <div>
                        <h1 class="text-xl font-bold">
                            Import Data Kelas
                        </h1>

                        <p class="mt-1 text-sm text-blue-100">
                            Upload file kelas untuk menambahkan data secara massal.
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
        <div class="p-6">

            <!-- INFO -->
            <div
                class="mb-5 rounded-3xl border border-blue-100 bg-blue-50 p-4">

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
                            Format File
                        </h3>

                        <p class="mt-1 text-sm leading-relaxed text-gray-500">
                            Gunakan file dengan format
                            <span class="font-semibold text-gray-700">.xlsx</span>,
                            <span class="font-semibold text-gray-700">.xls</span>,
                            atau
                            <span class="font-semibold text-gray-700">.csv</span>
                            agar data kelas bisa dibaca dengan benar.
                        </p>
                    </div>

                </div>

            </div>

            <!-- UPLOAD AREA -->
            <div
                class="group relative overflow-hidden rounded-3xl border-2 border-dashed border-gray-300 bg-gray-50 p-6 transition hover:border-blue-main hover:bg-blue-50/40"
                :class="dragActive ? 'border-blue-main bg-blue-50' : ''"
                @click="if ($refs.fileInput) { $refs.fileInput.click() }"
                @dragenter.prevent="dragActive = true"
                @dragover.prevent="dragActive = true"
                @dragleave.prevent="dragActive = false"
                @drop.prevent="dragActive = false; setFileFromDrop($event)">

                <input
                    type="file"
                    id="fileInput"
                    x-ref="fileInput"
                    accept=".xlsx,.xls,.csv"
                    class="hidden"
                    @change="setFileFromInput($event)">

                <!-- EMPTY -->
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
                        Upload File Kelas
                    </h2>

                    <p class="mt-2 max-w-md text-sm leading-relaxed text-gray-500">
                        Drag & drop file di area ini atau klik tombol di bawah untuk memilih file dari perangkat.
                    </p>

                    <div
                        class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-blue-main px-5 py-3 text-sm font-semibold text-white shadow-lg transition group-hover:bg-blue-deep-solid">

                        <iconify-icon
                            icon="solar:folder-open-bold"
                            width="20"
                            height="20">
                        </iconify-icon>

                        Pilih File

                    </div>

                    <p class="mt-4 text-xs text-gray-400">
                        Format didukung: XLSX, XLS, CSV
                    </p>

                </div>

                <!-- SELECTED FILE -->
                <div
                    x-show="hasFile"
                    x-transition
                    class="flex min-h-72 flex-col items-center justify-center text-center">

                    <div
                        class="mb-5 flex h-24 w-24 items-center justify-center rounded-[2rem] bg-emerald-50 text-emerald-600 shadow-sm">

                        <iconify-icon
                            icon="file-icons:microsoft-excel"
                            width="50"
                            height="50">
                        </iconify-icon>

                    </div>

                    <h2 class="text-xl font-bold text-gray-800">
                        File Siap Diimport
                    </h2>

                    <div
                        class="mt-5 w-full max-w-md rounded-3xl border border-gray-200 bg-white p-4 shadow-sm">

                        <div class="flex items-center justify-between gap-4">

                            <div class="flex min-w-0 items-center gap-3">

                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">

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

                        <span>Mengupload file...</span>

                        <span x-text="progress + '%'"></span>

                    </div>

                    <div class="h-2 overflow-hidden rounded-full bg-gray-200">

                        <div
                            class="h-full rounded-full bg-gradient-to-r from-blue-main to-blue-deep transition-all duration-300"
                            :style="`width: ${progress}%`">
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
                    icon="solar:file-text-bold"
                    width="20"
                    height="20">
                </iconify-icon>

                Pastikan struktur file sesuai template.

            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">

                <button
                    type="button"
                    @click="closeModal()"
                    class="rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">

                    Batal

                </button>

                <div x-data="{ openVerifikasi: false }">

                    <button
                        type="button"
                        @click="openVerifikasi = true"
                        :disabled="!hasFile || isUploading"
                        class="group flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-60">

                        <iconify-icon
                            icon="solar:upload-bold"
                            width="20"
                            height="20"
                            class="transition group-hover:-translate-y-0.5">
                        </iconify-icon>

                        Import Data

                    </button>

                    <!-- modal verifikasi -->
                    <livewire:components.modal.modal-verifikasi-import />

                </div>

            </div>

        </div>

    </div>

</div>