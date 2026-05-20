<div
    x-data="{
        isUploading: false,
        progress: 0,
        fileName: '',
        dragActive: false,
        get hasFile() {
            return this.fileName && this.fileName.length > 0;
        },
        setFileFromInput(event) {
            const file = event?.target?.files?.[0];
            this.fileName = file ? file.name : '';
        },
        setFileFromDrop(event) {
            const input = this.$refs.fileInput;
            if (!input || !event?.dataTransfer?.files?.length) {
                return;
            }

            input.files = event.dataTransfer.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }"
    x-on:livewire-upload-start="isUploading = true"
    x-on:livewire-upload-finish="isUploading = false; progress = 0"
    x-on:livewire-upload-error="isUploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
    x-on:imported.window="openModalImport = false"
    x-show="openModalImport"
    x-transition.opacity
    style="display: none;"
    class="z-50 fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm p-5"
>
    <div 
        @click.outside="openModalImport = false" 
        x-transition.scale
        class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl "
    >

        <!-- Header -->
        <div class="flex justify-between items-center p-6 pb-4">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 text-blue-600 p-2 rounded-lg flex items-center justify-center">
                    <iconify-icon icon="mdi:file-import" width="22"></iconify-icon> 
                </div>
                <h1 class="text-lg font-semibold text-gray-800">
                    Import Data Guru
                </h1>
            </div>

            <button
                @click="openModalImport = false"
                class="w-9 h-9 flex items-center justify-center rounded-full transition hover:bg-gray-100 active:scale-90"
            >
                <iconify-icon icon="mdi:close" width="20"></iconify-icon>
            </button>
        </div>
        <hr class="text-gray-400 " >
        <!-- Content -->
        <div
            class="p-6 m-6 space-y-4 rounded-xl border-2 border-gray-300 border-dashed transition hover:border-blue-main cursor-pointer"
            :class="dragActive ? 'border-blue-main bg-blue-50/40' : ''"
            @click="if ($refs.fileInput) { $refs.fileInput.click(); }"
            @dragenter.prevent="dragActive = true"
            @dragover.prevent="dragActive = true"
            @dragleave.prevent="dragActive = false"
            @drop.prevent="dragActive = false; setFileFromDrop($event)"
        >
            <div class="p-6 h-56 text-center flex justify-center ">
                <div>
                    <iconify-icon
                        x-show="!hasFile"
                        icon="mdi:file-import"
                        class="text-9xl text-gray-300"
                    ></iconify-icon>
                    <iconify-icon
                        x-show="hasFile"
                        icon="file-icons:microsoft-excel"
                        class="text-9xl text-gray-300"
                    ></iconify-icon>
                    <p x-show="!hasFile" class="text-gray-500 text-sm ">
                        <input
                            type="file"
                            style="display: none;"
                            id="fileInput"
                            x-ref="fileInput"
                            wire:model="file"
                            accept=".xlsx,.xls"
                            @change="setFileFromInput($event)"
                        >
                        Drag & drop file di sini atau <span class="text-blue-500 font-semibold cursor-pointer hover:text-blue-700" onclick="document.getElementById('fileInput').click();" >jelajahi</span> untuk memilih file
                    </p>
                    <p x-show="hasFile" class="text-gray-600 text-sm">
                        <span x-text="fileName"></span>
                    </p>
                </div>
            </div>

            @error('file')
                <p class="text-sm text-rose-600">{{ $message }}</p>
            @enderror

            <div x-show="isUploading" class="space-y-2">
                <div class="h-2 w-full rounded-full bg-gray-200">
                    <div class="h-2 rounded-full bg-blue-main" :style="`width: ${progress}%`"></div>
                </div>
                <p class="text-xs text-gray-500">Upload: <span x-text="progress"></span>%</p>
            </div>

            <div wire:loading wire:target="import" class="text-xs text-blue-600">
                Menyiapkan import...
            </div>

            @if ($isImporting)
                <div
                    wire:poll.300ms="pollProgress"
                    class="space-y-2"
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
                                return;
                            }

                            this.simTimer = setInterval(() => {
                                if (!this.total || this.total <= 0) {
                                    return;
                                }

                                const simulatedTarget = Math.min(
                                    this.processed + Math.max(1, Math.floor(this.chunkSize * 0.9)),
                                    this.total
                                );

                                if (this.smoothProcessed < this.processed) {
                                    this.smoothProcessed = this.processed;
                                }

                                if (this.smoothProcessed < simulatedTarget) {
                                    this.smoothProcessed = Math.min(
                                        simulatedTarget,
                                        this.smoothProcessed + Math.max(1, Math.floor(this.chunkSize / 200))
                                    );
                                }

                                this.smoothPercent = Math.round(
                                    (this.smoothProcessed / this.total) * 100
                                );
                            }, 62);
                        }
                    }"
                    x-init="
                        smoothProcessed = processed;
                        smoothPercent = percent;

                        startSim();

                        $watch('processed', () => {
                            if (smoothProcessed < processed) {
                                smoothProcessed = processed;
                            }
                            smoothPercent = total > 0
                                ? Math.round((smoothProcessed / total) * 100)
                                : 0;
                        });

                        $watch('percent', (value) => {
                            if (smoothPercent < value) {
                                smoothPercent = value;
                            }
                        });
                    "
                >
                    <div class="text-xs text-blue-600">Sedang memproses...</div>
                    <div class="flex justify-between text-xs text-gray-600">
                        <span>
                            Progress:
                            <span x-text="Math.floor(smoothProcessed)"></span>
                            /
                            <span x-text="total"></span>
                        </span>
                        <span x-text="Math.round(smoothPercent) + '%'"
                        ></span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-200">
                        <div
                            class="h-2 rounded-full bg-blue-main"
                            :style="`width: ${Math.max(0, Math.min(100, smoothPercent))}%`"
                        ></div>
                    </div>
                </div>
            @endif

            @if (!$isImporting && ($importedCount || $skippedCount))
                <div class="text-xs text-gray-600">
                    Berhasil: {{ $importedCount }} | Dilewati: {{ $skippedCount }}
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="mt-4 flex justify-between gap-3 p-6">
            <a
                href="{{ route('template-import-guru') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-100"
            >
                <iconify-icon
                    icon="line-md:download"
                    width="18"
                    height="18">
                </iconify-icon>

                Download Contoh XLSX
            </a>
            <div>
                <button
                    @click="openModalImport = false"
                    class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100"
                >
                    Batal
                </button>
                <button
                    wire:click="import"
                    wire:loading.attr="disabled"
                    wire:target="import,file"
                    :disabled="!hasFile"
                    class="px-5 py-2 bg-blue-main text-white rounded-lg shadow hover:bg-blue-deep-solid active:scale-95 transition disabled:opacity-60"
                >
                    Import
                </button>
            </div>
        </div>

    </div>
</div>