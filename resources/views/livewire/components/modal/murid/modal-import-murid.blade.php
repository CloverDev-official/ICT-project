@php
    $triggerClass = 'flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white';
    $dropdownClass = 'absolute z-50 mt-2 max-h-56 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin';
    $optionClass = 'flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white';
    $errorClass = 'mt-2 text-sm text-rose-500';
@endphp

<div
    x-data="{
        isUploading: false,
        progress: 0,
        fileName: '',
        fileSize: '',
        dragActive: false,

        yearOpen: false,
        selectedYear: null,

        get hasFile() {
            return this.fileName && this.fileName.length > 0
        },

        get canImport() {
            return this.hasFile && this.selectedYear && !this.isUploading
        },

        formatSize(bytes) {
            if (!bytes) return ''

            const size = bytes / 1024 / 1024
            return size.toFixed(2) + ' MB'
        },

        setFileFromInput(event) {
            const file = event?.target?.files?.[0]

            this.fileName = file ? file.name : ''
            this.fileSize = file ? this.formatSize(file.size) : ''
        },

        setFileFromDrop(event) {
            const input = this.$refs.fileInput

            if (!input || !event?.dataTransfer?.files?.length) {
                return
            }

            input.files = event.dataTransfer.files
            input.dispatchEvent(new Event('change', { bubbles: true }))
        },

        selectYear(year) {
            this.selectedYear = year
            this.yearOpen = false

            $wire.set('tahun_masuk', year)
        },

        clearFile() {
            this.fileName = ''
            this.fileSize = ''
            this.progress = 0

            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = null
            }
        },

        closeModal() {
            openModalImport = false
            this.clearFile()
        }
    }"
    x-on:livewire-upload-start="isUploading = true"
    x-on:livewire-upload-finish="isUploading = false; progress = 0"
    x-on:livewire-upload-error="isUploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
    x-on:imported.window="closeModal()"
    x-show="openModalImport"
    x-transition.opacity
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-md">

    <!-- MODAL -->
    <div
        @click.outside="closeModal()"
        x-transition.scale
        class="w-full max-w-4xl overflow-hidden rounded-[2rem] border border-white/20 bg-white shadow-2xl">

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
                            Import Data Murid
                        </h1>

                        <p class="mt-1 text-sm text-blue-100">
                            Upload file Excel dan pilih tahun masuk untuk import data murid.
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

            <!-- YEAR + UPLOAD GRID -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[18rem_1fr]">

                <!-- TAHUN MASUK -->
                <div
                    class="relative rounded-3xl border border-gray-200 bg-gray-50 p-5">

                    <div class="mb-4 flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-600">

                            <iconify-icon
                                icon="solar:calendar-bold"
                                width="22"
                                height="22">
                            </iconify-icon>

                        </div>

                        <div>
                            <h3 class="font-bold text-gray-800">
                                Tahun Masuk
                            </h3>

                            <p class="text-xs text-gray-500">
                                Wajib dipilih
                            </p>
                        </div>

                    </div>

                    <div
                        @click="yearOpen = !yearOpen"
                        class="{{ $triggerClass }} bg-white">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600">

                                <iconify-icon
                                    icon="solar:calendar-date-bold"
                                    width="20"
                                    height="20">
                                </iconify-icon>

                            </div>

                            <span
                                x-text="selectedYear ?? 'Pilih tahun'"
                                class="text-gray-700">
                            </span>

                        </div>

                        <iconify-icon
                            icon="lineicons:chevron-up"
                            width="20"
                            height="20"
                            class="text-gray-400 transition-transform"
                            :class="{ 'rotate-180': yearOpen }">
                        </iconify-icon>

                    </div>

                    <div
                        x-show="yearOpen"
                        x-transition
                        @click.outside="yearOpen = false"
                        style="display:none"
                        class="{{ $dropdownClass }} left-5 right-5 w-auto">

                        @foreach (range(now()->year - 10, now()->year + 10) as $tahun)

                            <div
                                @click.prevent="selectYear({{ $tahun }})"
                                class="{{ $optionClass }}"
                                :class="selectedYear == {{ $tahun }} ? 'bg-blue-main text-white' : ''">

                                <div class="flex items-center gap-2">

                                    <span>{{ $tahun }}</span>

                                    @if ($tahun === now()->year)
                                        <span
                                            class="rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-semibold text-cyan-600">
                                            Tahun ini
                                        </span>
                                    @endif

                                </div>

                                <iconify-icon
                                    x-show="selectedYear == {{ $tahun }}"
                                    icon="lineicons:check"
                                    width="18"
                                    height="18">
                                </iconify-icon>

                            </div>

                        @endforeach

                    </div>

                    @error('tahun_masuk')
                        <p class="{{ $errorClass }}">
                            {{ $message }}
                        </p>
                    @enderror

                    <div
                        class="mt-5 rounded-2xl border border-cyan-100 bg-white p-4">

                        <p class="text-xs font-medium text-gray-400">
                            Tahun dipilih
                        </p>

                        <h4
                            x-text="selectedYear ?? '-'"
                            class="mt-1 text-2xl font-bold text-gray-800">
                        </h4>

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
                        x-ref="fileInput"
                        wire:model="file"
                        accept=".xlsx,.xls"
                        class="hidden"
                        @change="setFileFromInput($event)">

                    <!-- EMPTY STATE -->
                    <div
                        x-show="!hasFile"
                        class="flex min-h-80 flex-col items-center justify-center text-center">

                        <div
                            class="mb-5 flex h-24 w-24 items-center justify-center rounded-[2rem] bg-white text-blue-main shadow-sm transition group-hover:-translate-y-1">

                            <iconify-icon
                                icon="solar:cloud-upload-bold"
                                width="48"
                                height="48">
                            </iconify-icon>

                        </div>

                        <h2 class="text-xl font-bold text-gray-800">
                            Upload File Excel
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

                            Pilih File Excel

                        </div>

                        <p class="mt-4 text-xs text-gray-400">
                            Format yang didukung: .xlsx dan .xls
                        </p>

                    </div>

                    <!-- SELECTED FILE -->
                    <div
                        x-show="hasFile"
                        x-transition
                        class="flex min-h-80 flex-col items-center justify-center text-center">

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

            @error('file')
                <p class="mt-3 text-sm text-rose-500">
                    {{ $message }}
                </p>
            @enderror

            <!-- IMPORT STATUS -->
            <div class="mt-6 space-y-4">

                <div
                    wire:loading
                    wire:target="import"
                    class="rounded-3xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-main">

                    <div class="flex items-center gap-3">

                        <iconify-icon
                            icon="line-md:loading-twotone-loop"
                            width="22"
                            height="22">
                        </iconify-icon>

                        <span class="font-semibold">
                            Menyiapkan proses import...
                        </span>

                    </div>

                </div>

                @if ($isImporting)
                    <div
                        wire:poll.300ms="pollProgress"
                        class="rounded-3xl border border-blue-100 bg-blue-50 p-5"
                        x-data="{
                            processed: @entangle('processedRows'),
                            total: @entangle('totalRows'),
                            percent: @entangle('importPercent'),
                            chunkSize: @js($chunkSize),
                            smoothProcessed: 0,
                            smoothPercent: 0,
                            simTimer: null,

                            startSim() {
                                if (this.simTimer) {
                                    return
                                }

                                this.simTimer = setInterval(() => {
                                    if (!this.total || this.total <= 0) {
                                        return
                                    }

                                    const simulatedTarget = Math.min(
                                        this.processed + Math.max(1, Math.floor(this.chunkSize * 0.9)),
                                        this.total
                                    )

                                    if (this.smoothProcessed < this.processed) {
                                        this.smoothProcessed = this.processed
                                    }

                                    if (this.smoothProcessed < simulatedTarget) {
                                        this.smoothProcessed = Math.min(
                                            simulatedTarget,
                                            this.smoothProcessed + Math.max(1, Math.floor(this.chunkSize / 200))
                                        )
                                    }

                                    this.smoothPercent = Math.round(
                                        (this.smoothProcessed / this.total) * 100
                                    )
                                }, 62)
                            }
                        }"
                        x-init="
                            smoothProcessed = processed
                            smoothPercent = percent

                            startSim()

                            $watch('processed', () => {
                                if (smoothProcessed < processed) {
                                    smoothProcessed = processed
                                }

                                smoothPercent = total > 0
                                    ? Math.round((smoothProcessed / total) * 100)
                                    : 0
                            })

                            $watch('percent', (value) => {
                                if (smoothPercent < value) {
                                    smoothPercent = value
                                }
                            })
                        ">

                        <div class="mb-4 flex items-center justify-between gap-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-main text-white">

                                    <iconify-icon
                                        icon="solar:database-bold"
                                        width="24"
                                        height="24">
                                    </iconify-icon>

                                </div>

                                <div>
                                    <h3 class="font-bold text-gray-800">
                                        Sedang Memproses Data Murid
                                    </h3>

                                    <p class="text-sm text-gray-500">
                                        Mohon tunggu sampai proses import selesai.
                                    </p>
                                </div>

                            </div>

                            <div
                                class="rounded-full bg-white px-4 py-2 text-sm font-bold text-blue-main shadow-sm">

                                <span x-text="Math.round(smoothPercent) + '%'"></span>

                            </div>

                        </div>

                        <div class="mb-2 flex justify-between text-xs font-semibold text-gray-500">

                            <span>
                                Progress:
                                <span x-text="Math.floor(smoothProcessed)"></span>
                                /
                                <span x-text="total"></span>
                            </span>

                            <span>
                                Import Murid
                            </span>

                        </div>

                        <div class="h-3 overflow-hidden rounded-full bg-white">

                            <div
                                class="h-full rounded-full bg-gradient-to-r from-blue-main to-blue-deep transition-all duration-300"
                                :style="`width: ${Math.max(0, Math.min(100, smoothPercent))}%`">
                            </div>

                        </div>

                    </div>
                @endif

                @if (!$isImporting && ($importedCount || $skippedCount))
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div
                            class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4">

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
                                        Berhasil
                                    </p>

                                    <h3 class="text-xl font-bold text-emerald-700">
                                        {{ $importedCount }}
                                    </h3>
                                </div>

                            </div>

                        </div>

                        <div
                            class="rounded-3xl border border-amber-100 bg-amber-50 p-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-500 text-white">

                                    <iconify-icon
                                        icon="solar:minus-circle-bold"
                                        width="22"
                                        height="22">
                                    </iconify-icon>

                                </div>

                                <div>
                                    <p class="text-xs text-amber-600">
                                        Dilewati
                                    </p>

                                    <h3 class="text-xl font-bold text-amber-700">
                                        {{ $skippedCount }}
                                    </h3>
                                </div>

                            </div>

                        </div>

                    </div>
                @endif

            </div>

        </div>

        <!-- FOOTER -->
        <div
            class="flex flex-col gap-4 border-t border-gray-200 bg-gray-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <a
                href="{{ route('template-import-murid') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-main transition hover:bg-blue-main hover:text-white">

                <iconify-icon
                    icon="line-md:download"
                    width="20"
                    height="20">
                </iconify-icon>

                Download Template XLSX

            </a>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">

                <button
                    type="button"
                    @click="closeModal()"
                    class="rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">

                    Batal

                </button>

                <button
                    type="button"
                    wire:click="import"
                    wire:loading.attr="disabled"
                    wire:target="import,file"
                    :disabled="!canImport"
                    class="group flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-60">

                    <iconify-icon
                        wire:loading.remove
                        wire:target="import"
                        icon="solar:upload-bold"
                        width="20"
                        height="20"
                        class="transition group-hover:-translate-y-0.5">
                    </iconify-icon>

                    <iconify-icon
                        wire:loading
                        wire:target="import"
                        icon="line-md:loading-twotone-loop"
                        width="20"
                        height="20">
                    </iconify-icon>

                    <span wire:loading.remove wire:target="import">
                        Import Data
                    </span>

                    <span wire:loading wire:target="import">
                        Mengimport...
                    </span>

                </button>

            </div>

        </div>

    </div>

</div>