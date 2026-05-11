<div>
    <!-- GENERATE QR CODE UNTUK MURID -->
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">
        <div>
            <h1 class="font-semibold text-gray-800 text-2xl capitalize">generate QR CODE murid</h1>
            <p class="text-sm text-gray-400">
                Generate QR CODE murid berdasarkan tingkat, kelas dan jurusan.
            </p>
        </div>
        <!-- kategori -->
        <div class="mt-5 border-t border-gray-200 p-4 grid grid-cols-2 md:grid-cols-3 gap-5">
            <!-- tingkat -->
            <div x-data="{
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
            
                    $wire.set('filterTingkat', id)
                }
            }" class="relative w-full {{ $isGenerating ? 'pointer-events-none opacity-60' : '' }}">
                <label class="text-sm text-gray-600 capitalize font-semibold">tingkat</label>

                <div @click="toggle()"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                    <span x-text="selectedLabel ?? 'Semua tingkat'" class="line-clamp-1 text-gray-700 text-sm"></span>
                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24">
                    </iconify-icon>
                </div>

                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div @click.prevent="select(null, 'Semua tingkat')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>Semua tingkat</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24"
                            height="24"></iconify-icon>
                    </div>

                    @foreach ($listRombel->pluck('tingkat')->filter()->unique('id') as $tingkat)
                        <div @click.prevent="select({{ (int) $tingkat->id }}, @js($tingkat->nama))"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                            <span>{{ $tingkat->nama }}</span>
                            <iconify-icon x-show="selectedId == {{ (int) $tingkat->id }}" icon="lineicons:check"
                                width="24" height="24"></iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- jurusan -->
            <div x-data="{
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
            
                    $wire.set('filterJurusan', id)
                }
            }" class="relative w-full {{ $isGenerating ? 'pointer-events-none opacity-60' : '' }}">
                <label class="text-sm text-gray-600 capitalize font-semibold">jurusan</label>

                <div @click="toggle()"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                    <span x-text="selectedLabel ?? 'Semua jurusan'" class="line-clamp-1 text-gray-700 text-sm"></span>
                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </div>

                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div @click.prevent="select(null, 'Semua jurusan')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>Semua jurusan</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24"
                            height="24"></iconify-icon>
                    </div>

                    @foreach ($filteredJurusan as $jurusan)
                        <div @click.prevent="select({{ (int) $jurusan->id }}, @js($jurusan->nama))"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                            <span>{{ $jurusan->nama }}</span>
                            <iconify-icon x-show="selectedId == {{ (int) $jurusan->id }}" icon="lineicons:check"
                                width="24" height="24"></iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- kelas -->
            <div x-data="{
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
            
                    $wire.set('filterIndeks', id)
                }
            }" class="relative w-full {{ $isGenerating ? 'pointer-events-none opacity-60' : '' }}">
                <label class="text-sm text-gray-600 capitalize font-semibold">Kelas</label>

                <div @click="toggle()"
                    class="text-sm mt-1 flex items-center justify-between px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 transition">
                    <span x-text="selectedLabel ?? 'Semua Kelas'" class="line-clamp-1 text-gray-700 text-sm"></span>
                    <iconify-icon class="text-gray-400 transition-transform" :class="{ 'rotate-180': open }"
                        icon="lineicons:chevron-up" width="25" height="24"></iconify-icon>
                </div>

                <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                    class="absolute mt-2 w-full h-40 bg-white border border-gray-200 rounded-xl shadow-lg overflow-y-auto scroll-thin z-50">

                    <div @click.prevent="select(null, 'Semua Kelas')"
                        class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                        <span>Semua Kelas</span>
                        <iconify-icon x-show="selectedId === null" icon="lineicons:check" width="24"
                            height="24"></iconify-icon>
                    </div>

                    @foreach ($filteredIndeks as $kelas)
                        <div @click.prevent="select({{ (int) $kelas->id }}, @js($kelas->nama))"
                            class="px-4 py-2 cursor-pointer hover:bg-blue-deep-solid hover:text-white transition flex justify-between items-center">
                            <span>{{ $kelas->nama }}</span>
                            <iconify-icon x-show="selectedId == {{ (int) $kelas->id }}" icon="lineicons:check"
                                width="24" height="24"></iconify-icon>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>
        
        <div class="grid grid-cols-3 pt-4">
            <!-- PROGRESS -->
            <div class="col-span-2 md:col-span-2 mt-4 {{ $totalData <= 0 ? 'invisible' : '' }}" >
                <!-- info -->
                <div wire:ignore>
                    <div class="flex justify-between text-sm mb-1">
                        <span id="progressTextMurid" class="text-gray-600">1/1</span>
                        <span id="progressPercentMurid" class="text-gray-600">0%</span>
                    </div>
        
                    <!-- bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div id="progressBarMurid"
                            class="bg-blue-main h-3 rounded-full transition-all duration-300"
                            style="width: 0%">
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 flex justify-end">
                <button
                    wire:click="startGenerate"
                    class="bg-blue-main px-4 py-2 rounded-xl text-white flex items-center gap-2 transition-all duration-150 hover:bg-blue-deep-solid active:scale-95 {{ $isGenerating ? 'opacity-60 cursor-not-allowed' : '' }}"
                    @disabled($isGenerating)>
                    <iconify-icon icon="lineicons:cloud-download" width="24" height="24"></iconify-icon>
                    Download QR CODE
                </button>
            </div>
        </div>
    </div>

    <!-- GENERATE QR CODE UNTUK GURU -->
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">
        <div>
            <h1 class="font-semibold text-gray-800 text-2xl capitalize">generate QR CODE guru</h1>
            <p class="text-sm text-gray-400">
                Generate semua QR CODE guru.
            </p>
        </div>

        <div class="grid grid-cols-3 pt-4 mt-5 border-t border-gray-200">
            <!-- PROGRESS -->
            <div class="col-span-2 md:col-span-2 mt-4">
                <!-- info -->
                <div class="flex justify-between text-sm mb-1">
                    <span id="progressTextGuru" class="text-gray-600">1/1</span>
                    <span id="progressPercentGuru" class="text-gray-600">0%</span>
                </div>
    
                <!-- bar -->
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div id="progressBarGuru"
                        class="bg-blue-main h-3 rounded-full transition-all duration-300"
                        style="width: 0%">
                    </div>
                </div>
            </div>
    
    
    
            <div class="p-4 flex justify-end">
                <button
                    onclick="startGenerateGuru()"
                    class="bg-blue-main px-4 py-2 rounded-xl text-white flex items-center gap-2 transition-all duration-150 hover:bg-blue-deep-solid active:scale-95 ">
                    <iconify-icon icon="lineicons:cloud-download" width="24" height="24"></iconify-icon>
                    Download QR CODE
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>

const progressTextMurid = document.getElementById('progressTextMurid');
const progressPercentMurid = document.getElementById('progressPercentMurid');
const progressBarMurid = document.getElementById('progressBarMurid');

let processing = false;
let processed = 0;
let totalData = 0;
let currentRunId = null;

let zip = null;
let zipChunks = [];

function resetProgress() {
    processed = 0;
    totalData = 0;

    zipChunks = [];

    progressTextMurid.textContent = '0/0';
    progressPercentMurid.textContent = '0%';
    progressBarMurid.style.width = '0%';
}

function createZip() {

    zipChunks = [];

    zip = new Zip((err, chunk, final) => {

        if (err) {
            console.error(err);
            return;
        }

        zipChunks.push(chunk);

        if (final) {

            const blob = new Blob(zipChunks, {
                type: 'application/zip'
            });

            const a = document.createElement('a');

            a.href = URL.createObjectURL(blob);
            a.download = 'murid-qr.zip';

            a.click();

            URL.revokeObjectURL(a.href);
        }
    });
}

$wire.$on('generate-qr', async (event) => {
    if (processing) {
        return;
    }

    console.log('Received generate-qr event with data:', event.totalData);

    processing = true;

    if (currentRunId !== event.runId) {
        currentRunId = event.runId;
        resetProgress();
        createZip();
    }

    if (typeof event.totalData === 'number' && event.totalData >= 0) {
        totalData = event.totalData;
    }

    const { dataMurid } = event;

    for (const murid of dataMurid) {
        console.log(`Processing murid: ${murid.rombel.nama_lengkap} (${murid.ulid})`);
        const pngBytes = await generateQRPNG(murid.ulid);
        const folderName = murid.rombel.nama_lengkap
            .replace(/[\\?%*:|"<>]/g, '-')
            .replace(/\s+/g, '-');

        const fileName = `${murid.nama} ${murid.nipd}.png`
            .replace(/[/\\?%*:|"<>]/g, '_')
            .replace(/\s+/g, '_');

        const file = new ZipPassThrough(
            `${folderName}/${fileName}`
        );

        zip.add(file);

        file.push(pngBytes, true);

        processed++;
        if (processed % 15 === 0) {
            if (scheduler?.yield) {
                await scheduler.yield();
            } else {
                await new Promise(resolve => setTimeout(resolve, 0));
            }
        }
        const percent = totalData > 0 ? Math.round((processed / totalData) * 100) : 0;

        progressTextMurid.textContent = `${processed}/${totalData}`;
        progressPercentMurid.textContent = `${percent}%`;
        progressBarMurid.style.width = `${percent}%`;
    }

    if (processed >= totalData) {
        console.log('Finalizing ZIP...');
        zip.end();
    }

    processing = false;

    $wire.nextChunk();
});
</script>
@endscript