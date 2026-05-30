<div class="mb-20 space-y-6">

    @php
    $labelClass = 'mb-2 block text-sm font-semibold text-gray-700';
    $triggerClass = 'flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white';
    $dropdownClass = 'absolute z-50 mt-2 max-h-56 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin';
    $optionClass = 'flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white';
    $errorClass = 'mt-2 text-sm text-rose-500';
    @endphp

    <!-- BACK -->
    <div>
        <a href="{{ route('data-kelas') }}" wire:navigate>
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
                        icon="solar:buildings-2-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Tambah Kelas
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Buat rombel baru berdasarkan tingkat, jurusan, indeks kelas, dan wali kelas.
                    </p>
                </div>

            </div>

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

    <!-- FORM CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- HEADER CARD -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Informasi Kelas
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Lengkapi data berikut untuk menambahkan kelas baru.
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
            class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

            <!-- TINGKAT -->
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

                        $wire.set('tingkat_id', id)
                    }
                }"
                class="relative">

                <label class="{{ $labelClass }}">
                    Tingkat
                </label>

                <div
                    @click="toggle()"
                    class="{{ $triggerClass }}">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-blue-main">

                            <iconify-icon
                                icon="solar:ranking-bold"
                                width="20"
                                height="20">
                            </iconify-icon>

                        </div>

                        <span
                            x-text="selectedLabel ?? 'Pilih tingkat'"
                            class="text-gray-700">
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
                    class="{{ $dropdownClass }}">

                    @foreach ($listTingkat as $tingkat)
                    <div
                        @click.prevent="select({{ (int) $tingkat->id }}, @js($tingkat->nama))"
                        class="{{ $optionClass }}">

                        <span>{{ $tingkat->nama }}</span>

                        <iconify-icon
                            x-show="selectedId == {{ (int) $tingkat->id }}"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>
                    @endforeach

                </div>

                @error('tingkat_id')
                <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror

            </div>

            <!-- JURUSAN -->
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

                        $wire.set('jurusan_id', id)
                    }
                }"
                class="relative">

                <label class="{{ $labelClass }}">
                    Jurusan
                </label>

                <div
                    @click="toggle()"
                    class="{{ $triggerClass }}">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">

                            <iconify-icon
                                icon="solar:book-bold"
                                width="20"
                                height="20">
                            </iconify-icon>

                        </div>

                        <span
                            x-text="selectedLabel ?? 'Pilih jurusan'"
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
                    class="{{ $dropdownClass }}">

                    @foreach ($listJurusan as $jurusan)
                    <div
                        @click.prevent="select({{ (int) $jurusan->id }}, @js($jurusan->nama))"
                        class="{{ $optionClass }}">

                        <span>{{ $jurusan->nama }}</span>

                        <iconify-icon
                            x-show="selectedId == {{ (int) $jurusan->id }}"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>
                    @endforeach

                </div>

                @error('jurusan_id')
                <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror

            </div>

            <!-- KELAS -->
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

                        $wire.set('indeks_id', id)
                    }
                }"
                class="relative">

                <label class="{{ $labelClass }}">
                    Kelas
                </label>

                <div
                    @click="toggle()"
                    class="{{ $triggerClass }}">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-600">

                            <iconify-icon
                                icon="solar:layers-bold"
                                width="20"
                                height="20">
                            </iconify-icon>

                        </div>

                        <span
                            x-text="selectedLabel ?? 'Pilih kelas'"
                            class="text-gray-700">
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
                    class="{{ $dropdownClass }}">

                    @foreach ($listIndeks as $indeks)
                    <div
                        @click.prevent="select({{ (int) $indeks->id }}, @js($indeks->nama))"
                        class="{{ $optionClass }}">

                        <span>{{ $indeks->nama }}</span>

                        <iconify-icon
                            x-show="selectedId == {{ (int) $indeks->id }}"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>
                    @endforeach

                </div>

                @error('indeks_id')
                <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror

            </div>

            <!-- TAHUN MASUK -->
            <div
                x-data="{
                open: false,
                selectedId: null,
                selectedLabel: null,
                currentYear: {{ now()->year }},

                toggle() {
                    this.open = !this.open
                },

                select(year) {
                    this.selectedId = year
                    this.selectedLabel = year
                    this.open = false

                    $wire.set('tahun_masuk', year)
                }
            }"
            class="relative">

                <label class="{{ $labelClass }}">
                    Tahun Masuk
                </label>

                <div
                    @click="toggle()"
                    class="{{ $triggerClass }}">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600">

                            <iconify-icon
                                icon="solar:calendar-bold"
                                width="20"
                                height="20">
                            </iconify-icon>

                        </div>

                        <span
                            x-text="selectedLabel ?? 'Pilih tahun masuk'"
                            class="text-gray-700">
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
                    class="{{ $dropdownClass }}">

                    @foreach (range(now()->year - 10, now()->year + 10) as $tahun)
                    <div
                        @click.prevent="select({{ $tahun }})"
                        class="{{ $optionClass }}">

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
                            x-show="selectedId == {{ $tahun }}"
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

            </div>

            <!-- WALI KELAS -->
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

                        $wire.set('guru_id', id)
                    }
                }"
                class="relative">

                <label class="{{ $labelClass }}">
                    Wali Kelas
                    <span class="font-normal text-gray-400">
                        (Opsional)
                    </span>
                </label>

                <div
                    @click="toggle()"
                    class="{{ $triggerClass }}">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-100 text-rose-600">

                            <iconify-icon
                                icon="solar:user-id-bold"
                                width="20"
                                height="20">
                            </iconify-icon>

                        </div>

                        <span
                            x-text="selectedLabel ?? 'Pilih wali kelas'"
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
                    class="{{ $dropdownClass }}">

                    <div
                        @click.prevent="select(null, 'Pilih wali kelas')"
                        class="{{ $optionClass }}">

                        <span>Pilih wali kelas</span>

                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>

                    @forelse ($listGuru ?? [] as $guru)
                    <div
                        @click.prevent="select({{ (int) $guru->id }}, @js($guru->nama))"
                        class="{{ $optionClass }}">

                        <span>{{ $guru->nama }}</span>

                        <iconify-icon
                            x-show="selectedId == {{ (int) $guru->id }}"
                            icon="lineicons:check"
                            width="18"
                            height="18">
                        </iconify-icon>

                    </div>
                    @empty
                    <div class="px-4 py-3 text-sm text-gray-400">
                        Data guru belum tersedia.
                    </div>
                    @endforelse

                </div>

                @error('guru_id')
                <p class="{{ $errorClass }}">{{ $message }}</p>
                @enderror

            </div>

            <!-- PREVIEW -->
            <div
                class="rounded-3xl border border-blue-100 bg-blue-50 p-5 md:col-span-2">

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
                            Rombel akan terbentuk dari kombinasi tingkat, jurusan, dan kelas. Pastikan data yang dipilih sudah sesuai sebelum disimpan.
                        </p>
                    </div>

                </div>

            </div>

            <!-- ACTION -->
            <div
                class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end md:col-span-2">

                <a href="{{ route('data-kelas') }}" wire:navigate>
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
                        wire:target="store"
                        icon="lineicons:save"
                        width="20"
                        height="20"
                        class="transition group-hover:scale-110">
                    </iconify-icon>

                    <iconify-icon
                        wire:loading
                        wire:target="store"
                        icon="line-md:loading-twotone-loop"
                        width="20"
                        height="20">
                    </iconify-icon>

                    <span wire:loading.remove wire:target="store">
                        Simpan Kelas
                    </span>

                    <span wire:loading wire:target="store">
                        Menyimpan...
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>