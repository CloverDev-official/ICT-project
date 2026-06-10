<div
    x-data="manajemenWaktu(@js($state), $wire)"
    x-init="init()"
    x-cloak
    wire:ignore
    class="space-y-6"
>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-5 shadow-lg sm:p-6">
        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-36 w-36 rounded-full bg-white/5"></div>

        <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur md:flex">
                    <iconify-icon icon="solar:calendar-bold" width="32" height="32" class="text-white"></iconify-icon>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Manajemen Kalender Absensi</h1>
                    <p class="mt-1 max-w-2xl text-sm text-blue-100">
                        Atur jadwal per tanggal dan per kelas: pulang cepat, PJJ, libur, atau hari spesial. Klik tanggal terasa instan karena form diproses di browser dulu.
                    </p>
                </div>
            </div>

            <button
                type="button"
                @click="goToday()"
                :disabled="loading"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-blue-main shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl disabled:cursor-wait disabled:opacity-60"
            >
                <iconify-icon icon="solar:calendar-mark-bold" width="20" height="20"></iconify-icon>
                Hari Ini
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <section class="space-y-6 xl:col-span-8">
            <div class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800" x-text="state.monthLabel"></h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Jumat memakai default khusus. Sabtu dan Minggu libur default, tapi tetap bisa dioverride.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="previousMonth()"
                            :disabled="loading"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-gray-200 text-gray-600 transition hover:border-blue-main hover:text-blue-main disabled:cursor-wait disabled:opacity-50"
                            aria-label="Bulan sebelumnya"
                        >
                            <iconify-icon icon="lineicons:chevron-left" width="19" height="19"></iconify-icon>
                        </button>
                        <button
                            type="button"
                            @click="nextMonth()"
                            :disabled="loading"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-gray-200 text-gray-600 transition hover:border-blue-main hover:text-blue-main disabled:cursor-wait disabled:opacity-50"
                            aria-label="Bulan berikutnya"
                        >
                            <iconify-icon icon="lineicons:chevron-right" width="19" height="19"></iconify-icon>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-1 text-center text-[11px] font-bold uppercase tracking-wide text-gray-400 sm:gap-2 sm:text-xs">
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div>Sab</div>
                    <div>Min</div>
                </div>

                <div class="mt-2 grid grid-cols-7 gap-1 sm:gap-2">
                    <template x-for="(day, index) in state.days" :key="day ? day.date : 'empty-' + index">
                        <div>
                            <template x-if="!day">
                                <div class="min-h-16 rounded-2xl border border-dashed border-gray-100 bg-gray-50/60 sm:min-h-24"></div>
                            </template>

                            <template x-if="day">
                                <button
                                    type="button"
                                    @click="selectDate(day.date)"
                                    class="group flex min-h-16 w-full flex-col rounded-2xl border p-2 text-left transition sm:min-h-24 sm:p-3"
                                    :class="calendarClass(day)"
                                >
                                    <div class="flex items-start justify-between gap-1">
                                        <span
                                            class="flex h-7 w-7 items-center justify-center rounded-full text-sm font-bold sm:h-8 sm:w-8"
                                            :class="day.date === selectedDate ? 'bg-blue-main text-white' : (day.isToday ? 'bg-blue-50 text-blue-main' : 'text-gray-800')"
                                            x-text="day.day"
                                        ></span>
                                        <span
                                            class="hidden rounded-full px-2 py-0.5 text-[10px] font-semibold sm:inline-flex"
                                            :class="day.hasEvent ? 'bg-orange-50 text-orange-600' : typePillClass(day.default.tipe)"
                                            x-text="day.hasEvent ? day.affectedCount + ' kelas' : day.default.label"
                                        ></span>
                                    </div>

                                    <div class="mt-auto hidden pt-2 text-xs sm:block">
                                        <p class="line-clamp-1 font-semibold" :class="day.hasEvent ? 'text-orange-700' : 'text-gray-600'" x-text="day.title"></p>
                                        <p class="line-clamp-1 text-gray-400" x-text="day.summary"></p>
                                    </div>

                                    <div class="mt-auto flex gap-1 pt-2 sm:hidden">
                                        <span class="h-2 w-2 rounded-full" :class="day.hasEvent ? 'bg-orange-500' : tinyDotClass(day.default.tipe)"></span>
                                        <span class="text-[10px] text-gray-400" x-show="day.hasEvent" x-text="day.affectedCount"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Event Bulan Ini</h3>
                        <p class="text-sm text-gray-500">Ringkasan tanggal yang punya jadwal khusus per kelas.</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-600">
                        <span x-text="monthEvents().length"></span> event
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[720px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-xs uppercase text-gray-400">
                                <th class="px-3 py-3">Tanggal</th>
                                <th class="px-3 py-3">Event</th>
                                <th class="px-3 py-3">Kelas Terdampak</th>
                                <th class="px-3 py-3">Ringkasan</th>
                                <th class="px-3 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="monthEvents().length === 0">
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-400">
                                        Belum ada event khusus pada bulan ini.
                                    </td>
                                </tr>
                            </template>

                            <template x-for="event in monthEvents()" :key="event.tanggal">
                                <tr class="border-b border-gray-100 last:border-0">
                                    <td class="px-3 py-3 font-semibold text-gray-700" x-text="formatDate(event.tanggal)"></td>
                                    <td class="px-3 py-3">
                                        <p class="font-semibold text-gray-800" x-text="event.nama_acara || '-'"></p>
                                        <p class="text-xs text-gray-400" x-text="event.keterangan || ''"></p>
                                    </td>
                                    <td class="px-3 py-3 text-gray-700">
                                        <span x-text="event.affected_count"></span> kelas
                                    </td>
                                    <td class="px-3 py-3 text-gray-600" x-text="event.summary"></td>
                                    <td class="px-3 py-3 text-right">
                                        <button
                                            type="button"
                                            @click="selectDate(event.tanggal); scrollToForm()"
                                            class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-main transition hover:bg-blue-100"
                                        >
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <aside class="space-y-6 xl:col-span-4 xl:sticky xl:top-6 self-start">
            <div id="form-event-waktu" class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-blue-main">Tanggal Dipilih</p>
                        <h3 class="text-xl font-bold text-gray-800" x-text="formatDate(selectedDate)"></h3>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-bold" :class="selectedEvent() ? 'bg-orange-50 text-orange-600' : typePillClass(selectedDefault().tipe)" x-text="selectedEvent() ? 'Ada Event' : selectedDefault().label"></span>
                        <button
                            type="button"
                            x-show="selectedEvent()"
                            @click="resetToDefault"
                            class="rounded-full border border-gray-200 px-3 py-1 text-xs font-bold text-gray-600 transition hover:border-blue-main hover:text-blue-main"
                        >
                            Reset ke Default
                        </button>
                    </div>
                </div>

                <div class="mb-5 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 text-sm text-gray-600">
                        <template x-if="selectedEvent()">
                            <div>
                                <p class="font-bold text-gray-800" x-text="selectedEvent().nama_acara"></p>
                                <p class="mt-1" x-text="selectedEvent().summary"></p>
                                <p class="mt-2 text-xs text-gray-400">Detail jadwal per kelas ada di bawah.</p>
                            </div>
                        </template>
                        <template x-if="!selectedEvent()">
                            <div>
                                <p class="font-bold text-gray-800" x-text="selectedDefault().label"></p>
                                <p class="mt-1" x-text="selectedDefault().keterangan"></p>
                                <p class="mt-2 text-xs text-gray-400" x-show="selectedDefault().jam_masuk">
                                    <span x-text="selectedDefault().jam_masuk"></span> - <span x-text="selectedDefault().jam_pulang"></span>
                                </p>
                            </div>
                        </template>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 text-sm text-gray-600">
                        <p class="text-xs font-bold uppercase text-gray-400">Jendela Scan</p>
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span>Masuk</span>
                                <span class="font-semibold text-gray-800" x-text="scanMasukLabel()"></span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span>Pulang</span>
                                <span class="font-semibold text-gray-800" x-text="scanKeluarLabel()"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="saveEvent" class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Dari Tanggal</label>
                            <input type="date" x-model="eventForm.tanggal_mulai" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Sampai Tanggal</label>
                            <input type="date" x-model="eventForm.tanggal_selesai" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Nama Event</label>
                        <input type="text" x-model="eventForm.nama_acara" placeholder="Contoh: PTS, Rapat Guru, Classmeeting" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Keterangan Umum</label>
                        <textarea x-model="eventForm.keterangan" rows="2" placeholder="Opsional" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none"></textarea>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-bold text-gray-800">Preset untuk Kelas Baru</p>
                                <p class="text-xs text-gray-500">Preset ini diterapkan ke kelas yang dipilih setelahnya, termasuk window scan jika diaktifkan.</p>
                            </div>
                            <button type="button" @click="applyPresetToAll" class="rounded-xl bg-white px-3 py-2 text-xs font-bold text-blue-main shadow-sm transition hover:bg-blue-50">
                                Terapkan ke Terpilih
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <select x-model="preset.tipe" @change="syncPresetTime" class="rounded-2xl border border-gray-200 px-3 py-3 text-sm focus:border-blue-main focus:outline-none sm:col-span-3">
                                    <template x-for="(label, key) in state.tipeOptions" :key="key">
                                        <option :value="key" x-text="label"></option>
                                    </template>
                                </select>
                                <input type="time" x-model="preset.jam_masuk" :disabled="preset.tipe === 'libur'" class="rounded-2xl border border-gray-200 px-3 py-3 text-sm focus:border-blue-main focus:outline-none disabled:bg-gray-100">
                                <input type="time" x-model="preset.jam_pulang" :disabled="preset.tipe === 'libur'" class="rounded-2xl border border-gray-200 px-3 py-3 text-sm focus:border-blue-main focus:outline-none disabled:bg-gray-100">
                                <input type="text" x-model="preset.keterangan" placeholder="Keterangan kelas" class="rounded-2xl border border-gray-200 px-3 py-3 text-sm focus:border-blue-main focus:outline-none">
                            </div>

                            <div class="rounded-2xl border border-blue-100 bg-white p-3 shadow-sm">
                                <label class="flex cursor-pointer items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Atur jam bisa scan masuk dan scan pulang</p>
                                        <p class="mt-1 text-xs text-gray-500">Aktifkan jika kelas baru perlu window scan khusus. Jika nonaktif, kelas memakai window default.</p>
                                    </div>
                                    <input type="checkbox" x-model="preset.gunakan_window_scan" :disabled="preset.tipe === 'libur'" class="mt-1 h-5 w-5 rounded border-gray-300 text-blue-main focus:ring-blue-main disabled:cursor-not-allowed disabled:opacity-50">
                                </label>

                                <div
                                    x-show="preset.gunakan_window_scan && preset.tipe !== 'libur'"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2"
                                >
                                    <div>
                                        <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Scan Masuk Mulai</label>
                                        <input type="time" x-model="preset.scan_masuk_mulai" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                        <p class="mt-1 text-[11px] text-gray-400">Awal siswa boleh scan masuk.</p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Scan Masuk Sampai</label>
                                        <input type="time" x-model="preset.scan_masuk_sampai" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                        <p class="mt-1 text-[11px] text-gray-400">Batas akhir scan masuk.</p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Scan Pulang Mulai</label>
                                        <input type="time" x-model="preset.scan_keluar_mulai" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                        <p class="mt-1 text-[11px] text-gray-400">Awal siswa boleh scan pulang.</p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Scan Pulang Sampai</label>
                                        <input type="time" x-model="preset.scan_keluar_sampai" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                        <p class="mt-1 text-[11px] text-gray-400">Batas akhir scan pulang.</p>
                                    </div>
                                </div>

                                <p class="mt-3 text-xs text-blue-700" x-show="preset.gunakan_window_scan && preset.tipe !== 'libur'">
                                    Window scan preset akan disalin ke kelas yang dipilih berikutnya.
                                </p>
                                <p class="mt-3 text-xs text-gray-500" x-show="!preset.gunakan_window_scan || preset.tipe === 'libur'">
                                    Preset kelas baru memakai window scan default/global.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 p-4">
                        <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-bold text-gray-800">Atur Event Kelas</p>
                                <p class="text-xs text-gray-500">
                                    <span x-text="eventForm.selected_rombel_ids.length"></span> kelas dipilih.
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="selectFilteredRombel" class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-bold text-blue-main hover:bg-blue-100">Pilih tampil</button>
                                <button type="button" @click="clearRombel" class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100">Kosongkan</button>
                            </div>
                        </div>

                        <input type="search" x-model.debounce.150ms="rombelSearch" placeholder="Cari kelas..." class="mb-3 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">

                        <div class="max-h-52 space-y-2 overflow-y-auto pr-1">
                            <template x-for="rombel in filteredRombel()" :key="rombel.id">
                                <button
                                    type="button"
                                    @click="toggleRombel(rombel.id)"
                                    class="flex w-full items-center justify-between gap-3 rounded-2xl border px-3 py-3 text-left transition"
                                    :class="isRombelSelected(rombel.id) ? 'border-blue-main bg-blue-50' : 'border-gray-100 hover:border-blue-main/40 hover:bg-gray-50'"
                                >
                                    <div>
                                        <p class="text-sm font-bold text-gray-800" x-text="rombel.nama"></p>
                                        <p class="text-xs text-gray-400" x-text="[rombel.tingkat, rombel.jurusan, rombel.indeks].filter(Boolean).join(' • ')"></p>
                                    </div>
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border text-xs font-bold" :class="isRombelSelected(rombel.id) ? 'border-blue-main bg-blue-main text-white' : 'border-gray-200 text-gray-300'">
                                        ✓
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-3" x-show="eventForm.selected_rombel_ids.length > 0">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-bold text-gray-800">Detail Per Kelas</p>
                            <p class="text-xs text-gray-400">Bisa beda antar kelas.</p>
                        </div>

                        <div class="max-h-[520px] space-y-3 overflow-y-auto pr-1">
                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                                <template x-for="rombel in selectedRombel()" :key="rombel.id">
                                    <div class="rounded-2xl border border-gray-200 p-3">
                                    <div class="mb-3 flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-800" x-text="rombel.nama"></p>
                                            <p class="text-xs text-gray-400" x-text="detailLabel(rombel.id)"></p>
                                        </div>
                                        <button type="button" @click="toggleRombel(rombel.id)" class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100">Hapus</button>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            <select x-model="eventForm.detail_kelas[String(rombel.id)].tipe" @change="applyTypeDefault(rombel.id)" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none sm:col-span-2">
                                                <template x-for="(label, key) in state.tipeOptions" :key="key">
                                                    <option :value="key" x-text="label"></option>
                                                </template>
                                            </select>
                                            <input type="time" x-model="eventForm.detail_kelas[String(rombel.id)].jam_masuk" :disabled="eventForm.detail_kelas[String(rombel.id)].tipe === 'libur'" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none disabled:bg-gray-100">
                                            <input type="time" x-model="eventForm.detail_kelas[String(rombel.id)].jam_pulang" :disabled="eventForm.detail_kelas[String(rombel.id)].tipe === 'libur'" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none disabled:bg-gray-100">
                                            <textarea x-model="eventForm.detail_kelas[String(rombel.id)].keterangan" rows="2" placeholder="Keterangan khusus kelas ini" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none sm:col-span-2"></textarea>
                                        </div>

                                        <div class="rounded-2xl border border-blue-100 bg-blue-50/40 p-3">
                                            <label class="flex cursor-pointer items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-bold text-gray-800">Window scan khusus kelas</p>
                                                    <p class="mt-1 text-xs text-gray-500">Aktifkan jika kelas ini punya jam scan masuk/pulang sendiri.</p>
                                                </div>
                                                <input type="checkbox" x-model="eventForm.detail_kelas[String(rombel.id)].gunakan_window_scan" @change="fillDetailScanFromDefault(eventForm.detail_kelas[String(rombel.id)])" :disabled="eventForm.detail_kelas[String(rombel.id)].tipe === 'libur'" class="mt-1 h-5 w-5 rounded border-gray-300 text-blue-main focus:ring-blue-main disabled:cursor-not-allowed disabled:opacity-50">
                                            </label>

                                            <div
                                                x-show="eventForm.detail_kelas[String(rombel.id)].gunakan_window_scan && eventForm.detail_kelas[String(rombel.id)].tipe !== 'libur'"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 -translate-y-1"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2"
                                            >
                                                <div>
                                                    <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Scan Masuk Mulai</label>
                                                    <input type="time" x-model="eventForm.detail_kelas[String(rombel.id)].scan_masuk_mulai" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                                    <p class="mt-1 text-[11px] text-gray-400">Awal scan masuk.</p>
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Scan Masuk Sampai</label>
                                                    <input type="time" x-model="eventForm.detail_kelas[String(rombel.id)].scan_masuk_sampai" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                                    <p class="mt-1 text-[11px] text-gray-400">Batas scan masuk.</p>
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Scan Pulang Mulai</label>
                                                    <input type="time" x-model="eventForm.detail_kelas[String(rombel.id)].scan_keluar_mulai" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                                    <p class="mt-1 text-[11px] text-gray-400">Awal scan pulang.</p>
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Scan Pulang Sampai</label>
                                                    <input type="time" x-model="eventForm.detail_kelas[String(rombel.id)].scan_keluar_sampai" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                                    <p class="mt-1 text-[11px] text-gray-400">Batas scan pulang.</p>
                                                </div>
                                            </div>

                                            <p class="mt-3 text-xs text-blue-700" x-show="eventForm.detail_kelas[String(rombel.id)].gunakan_window_scan && eventForm.detail_kelas[String(rombel.id)].tipe !== 'libur'">
                                                Scanner akan mendahulukan window scan kelas ini.
                                            </p>
                                            <p class="mt-3 text-xs text-gray-500" x-show="!eventForm.detail_kelas[String(rombel.id)].gunakan_window_scan || eventForm.detail_kelas[String(rombel.id)].tipe === 'libur'">
                                                Kelas ini memakai window scan default/global.
                                            </p>
                                        </div>
                                    </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <template x-if="formError">
                        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600" x-text="formError"></div>
                    </template>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <button
                            type="submit"
                            :disabled="saving"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-main px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-deep disabled:cursor-wait disabled:opacity-60"
                        >
                            <span x-show="!saving">Simpan Event</span>
                            <span x-show="saving">Menyimpan...</span>
                        </button>
                        <button
                            type="button"
                            @click="resetFormFromDate(selectedDate)"
                            class="rounded-2xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 transition hover:border-blue-main hover:text-blue-main"
                        >
                            Reset Form
                        </button>
                    </div>

                    <button
                        type="button"
                        x-show="selectedEvent()"
                        @click="deleteSelectedDate"
                        :disabled="saving"
                        class="w-full rounded-2xl bg-red-50 px-5 py-3 text-sm font-bold text-red-600 transition hover:bg-red-100 disabled:cursor-wait disabled:opacity-60"
                    >
                        Hapus Semua Event di Tanggal Ini
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Default Jam Absensi</h3>
                    <p class="text-sm text-gray-500">Dipakai untuk hari tanpa event kelas.</p>
                </div>

                <form @submit.prevent="saveDefault" class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Jam Masuk</label>
                            <input type="time" x-model="defaultForm.jam_masuk" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Jam Pulang Normal</label>
                            <input type="time" x-model="defaultForm.jam_pulang_normal" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Jam Pulang Jumat</label>
                            <input type="time" x-model="defaultForm.jam_pulang_jumat" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        <div class="mb-3">
                            <p class="font-bold text-gray-800">Jendela Scan</p>
                            <p class="text-xs text-gray-500">Atur jam bisa scan masuk dan scan pulang.</p>
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Masuk Mulai</label>
                                <input type="time" x-model="defaultForm.scan_masuk_mulai" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Masuk Sampai</label>
                                <input type="time" x-model="defaultForm.scan_masuk_sampai" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Pulang Mulai</label>
                                <input type="time" x-model="defaultForm.scan_keluar_mulai" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Pulang Sampai</label>
                                <input type="time" x-model="defaultForm.scan_keluar_sampai" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none">
                            </div>
                        </div>
                    </div>
                    <button
                        type="submit"
                        :disabled="savingDefault"
                        class="w-full rounded-2xl bg-gray-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-gray-800 disabled:cursor-wait disabled:opacity-60"
                    >
                        <span x-show="!savingDefault">Simpan Default</span>
                        <span x-show="savingDefault">Menyimpan...</span>
                    </button>
                </form>
            </div>
        </aside>
    </div>

    @once
        <script>
            function manajemenWaktu(initialState, wire) {
                return {
                    state: JSON.parse(JSON.stringify(initialState)),
                    selectedDate: initialState.selectedDate,
                    eventForm: {},
                    preset: {},
                    defaultForm: JSON.parse(JSON.stringify(initialState.settings)),
                    rombelSearch: '',
                    loading: false,
                    saving: false,
                    savingDefault: false,
                    requestTimer: null,
                    formError: '',
                    bulan: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],

                    init() {
                        this.resetFormFromDate(this.selectedDate);

                        window.addEventListener('waktu-state-updated', (event) => {
                            this.applyServerState(event.detail?.state || event.detail);
                        });
                    },

                    applyServerState(nextState) {
                        if (!nextState || typeof nextState !== 'object') {
                            this.finishRequest();
                            return;
                        }

                        this.state = JSON.parse(JSON.stringify(nextState));
                        this.selectedDate = this.state.selectedDate;
                        this.defaultForm = JSON.parse(JSON.stringify(this.state.settings));
                        this.resetFormFromDate(this.selectedDate);
                        this.finishRequest();
                    },

                    beginRequest(flag) {
                        this.formError = '';
                        this.loading = flag === 'loading';
                        this.saving = flag === 'saving';
                        this.savingDefault = flag === 'savingDefault';

                        if (this.requestTimer) {
                            clearTimeout(this.requestTimer);
                        }

                        this.requestTimer = setTimeout(() => {
                            this.finishRequest();
                            this.formError = 'Proses menyimpan terlalu lama. Cek koneksi/server, lalu coba lagi.';
                        }, 20000);
                    },

                    finishRequest() {
                        this.loading = false;
                        this.saving = false;
                        this.savingDefault = false;

                        if (this.requestTimer) {
                            clearTimeout(this.requestTimer);
                            this.requestTimer = null;
                        }
                    },

                    runServerAction(action, fallbackError) {
                        let request;

                        try {
                            request = typeof action === 'function' ? action() : action;
                        } catch (error) {
                            this.finishRequest();
                            this.formError = this.errorMessage(error, fallbackError);
                            return Promise.resolve();
                        }

                        return Promise.resolve(request)
                            .then((nextState) => {
                                this.applyServerState(nextState);
                            })
                            .catch((error) => {
                                this.finishRequest();
                                this.formError = this.errorMessage(error, fallbackError);
                            })
                            .finally(() => {
                                this.finishRequest();
                            });
                    },

                    selectDate(date) {
                        this.selectedDate = date;
                        this.state.selectedDate = date;
                        this.resetFormFromDate(date);
                    },

                    selectedEvent() {
                        return this.state.eventsByDate[this.selectedDate] || null;
                    },

                    selectedDefault() {
                        const day = this.state.days.find((item) => item && item.date === this.selectedDate);

                        return day ? day.default : {
                            tipe: 'normal',
                            label: 'Normal',
                            jam_masuk: this.state.settings.jam_masuk,
                            jam_pulang: this.state.settings.jam_pulang_normal,
                            scan_masuk_mulai: this.state.settings.scan_masuk_mulai,
                            scan_masuk_sampai: this.state.settings.scan_masuk_sampai,
                            scan_keluar_mulai: this.state.settings.scan_keluar_mulai,
                            scan_keluar_sampai: this.state.settings.scan_keluar_sampai,
                            keterangan: 'Jadwal normal.',
                        };
                    },

                    resetFormFromDate(date) {
                        this.formError = '';
                        const event = this.state.eventsByDate[date] || null;
                        const defaults = this.selectedDefault();
                        const defaultType = defaults.tipe === 'normal' ? 'pulang_cepat' : defaults.tipe;

                        this.preset = {
                            tipe: defaultType,
                            jam_masuk: defaultType === 'libur' ? '' : (defaults.jam_masuk || this.state.settings.jam_masuk),
                            jam_pulang: defaultType === 'libur' ? '' : (defaults.jam_pulang || this.state.settings.jam_pulang_normal),
                            gunakan_window_scan: false,
                            scan_masuk_mulai: defaultType === 'libur' ? '' : (defaults.scan_masuk_mulai || this.state.settings.scan_masuk_mulai),
                            scan_masuk_sampai: defaultType === 'libur' ? '' : (defaults.scan_masuk_sampai || this.state.settings.scan_masuk_sampai),
                            scan_keluar_mulai: defaultType === 'libur' ? '' : (defaults.scan_keluar_mulai || this.state.settings.scan_keluar_mulai),
                            scan_keluar_sampai: defaultType === 'libur' ? '' : (defaults.scan_keluar_sampai || this.state.settings.scan_keluar_sampai),
                            keterangan: '',
                        };

                        this.eventForm = {
                            tanggal_mulai: date,
                            tanggal_selesai: date,
                            nama_acara: event ? (event.nama_acara || '') : '',
                            keterangan: event ? (event.keterangan || '') : '',
                            selected_rombel_ids: event ? event.details.map((item) => Number(item.rombel_id)) : [],
                            detail_kelas: {},
                        };

                        if (event) {
                            event.details.forEach((item) => {
                                this.eventForm.detail_kelas[String(item.rombel_id)] = {
                                    tipe: item.tipe,
                                    jam_masuk: item.jam_masuk || '',
                                    jam_pulang: item.jam_pulang || '',
                                    gunakan_window_scan: Boolean(item.gunakan_window_scan),
                                    scan_masuk_mulai: item.scan_masuk_mulai || this.state.settings.scan_masuk_mulai || '',
                                    scan_masuk_sampai: item.scan_masuk_sampai || this.state.settings.scan_masuk_sampai || '',
                                    scan_keluar_mulai: item.scan_keluar_mulai || this.state.settings.scan_keluar_mulai || '',
                                    scan_keluar_sampai: item.scan_keluar_sampai || this.state.settings.scan_keluar_sampai || '',
                                    keterangan: item.keterangan || '',
                                };
                            });
                        }
                    },

                    syncPresetTime() {
                        if (this.preset.tipe === 'libur') {
                            this.preset.jam_masuk = '';
                            this.preset.jam_pulang = '';
                            this.preset.gunakan_window_scan = false;
                            this.preset.scan_masuk_mulai = '';
                            this.preset.scan_masuk_sampai = '';
                            this.preset.scan_keluar_mulai = '';
                            this.preset.scan_keluar_sampai = '';
                            return;
                        }

                        if (!this.preset.jam_masuk) {
                            this.preset.jam_masuk = this.state.settings.jam_masuk;
                        }

                        if (!this.preset.jam_pulang) {
                            this.preset.jam_pulang = this.preset.tipe === 'khusus'
                                ? this.state.settings.jam_pulang_jumat
                                : this.state.settings.jam_pulang_normal;
                        }

                        this.fillPresetScanFromDefault();
                    },

                    fillPresetScanFromDefault() {
                        if (!this.preset.scan_masuk_mulai) {
                            this.preset.scan_masuk_mulai = this.state.settings.scan_masuk_mulai;
                        }

                        if (!this.preset.scan_masuk_sampai) {
                            this.preset.scan_masuk_sampai = this.state.settings.scan_masuk_sampai;
                        }

                        if (!this.preset.scan_keluar_mulai) {
                            this.preset.scan_keluar_mulai = this.state.settings.scan_keluar_mulai;
                        }

                        if (!this.preset.scan_keluar_sampai) {
                            this.preset.scan_keluar_sampai = this.state.settings.scan_keluar_sampai;
                        }
                    },

                    detailFromPreset() {
                        this.syncPresetTime();

                        const gunakanWindowScan = this.preset.tipe !== 'libur' && Boolean(this.preset.gunakan_window_scan);

                        return {
                            tipe: this.preset.tipe,
                            jam_masuk: this.preset.tipe === 'libur' ? '' : this.preset.jam_masuk,
                            jam_pulang: this.preset.tipe === 'libur' ? '' : this.preset.jam_pulang,
                            gunakan_window_scan: gunakanWindowScan,
                            scan_masuk_mulai: gunakanWindowScan ? this.preset.scan_masuk_mulai : '',
                            scan_masuk_sampai: gunakanWindowScan ? this.preset.scan_masuk_sampai : '',
                            scan_keluar_mulai: gunakanWindowScan ? this.preset.scan_keluar_mulai : '',
                            scan_keluar_sampai: gunakanWindowScan ? this.preset.scan_keluar_sampai : '',
                            keterangan: this.preset.keterangan || '',
                        };
                    },

                    isRombelSelected(id) {
                        return this.eventForm.selected_rombel_ids.includes(Number(id));
                    },

                    toggleRombel(id) {
                        id = Number(id);
                        const key = String(id);

                        if (this.isRombelSelected(id)) {
                            this.eventForm.selected_rombel_ids = this.eventForm.selected_rombel_ids.filter((item) => item !== id);
                            delete this.eventForm.detail_kelas[key];
                            return;
                        }

                        this.eventForm.selected_rombel_ids.push(id);
                        this.eventForm.detail_kelas[key] = this.detailFromPreset();
                    },

                    selectFilteredRombel() {
                        this.filteredRombel().forEach((rombel) => {
                            if (!this.isRombelSelected(rombel.id)) {
                                this.eventForm.selected_rombel_ids.push(Number(rombel.id));
                                this.eventForm.detail_kelas[String(rombel.id)] = this.detailFromPreset();
                            }
                        });
                    },

                    clearRombel() {
                        this.eventForm.selected_rombel_ids = [];
                        this.eventForm.detail_kelas = {};
                    },

                    applyPresetToAll() {
                        this.eventForm.selected_rombel_ids.forEach((id) => {
                            this.eventForm.detail_kelas[String(id)] = this.detailFromPreset();
                        });
                    },

                    applyTypeDefault(id) {
                        const key = String(id);
                        const detail = this.eventForm.detail_kelas[key];

                        if (!detail) {
                            return;
                        }

                        if (detail.tipe === 'libur') {
                            detail.jam_masuk = '';
                            detail.jam_pulang = '';
                            detail.gunakan_window_scan = false;
                            detail.scan_masuk_mulai = '';
                            detail.scan_masuk_sampai = '';
                            detail.scan_keluar_mulai = '';
                            detail.scan_keluar_sampai = '';
                            return;
                        }

                        if (!detail.jam_masuk) {
                            detail.jam_masuk = this.state.settings.jam_masuk;
                        }

                        if (!detail.jam_pulang) {
                            detail.jam_pulang = detail.tipe === 'khusus'
                                ? this.state.settings.jam_pulang_jumat
                                : this.state.settings.jam_pulang_normal;
                        }

                        if (detail.gunakan_window_scan) {
                            this.fillDetailScanFromDefault(detail);
                        }
                    },

                    fillDetailScanFromDefault(detail) {
                        if (!detail.scan_masuk_mulai) {
                            detail.scan_masuk_mulai = this.state.settings.scan_masuk_mulai;
                        }

                        if (!detail.scan_masuk_sampai) {
                            detail.scan_masuk_sampai = this.state.settings.scan_masuk_sampai;
                        }

                        if (!detail.scan_keluar_mulai) {
                            detail.scan_keluar_mulai = this.state.settings.scan_keluar_mulai;
                        }

                        if (!detail.scan_keluar_sampai) {
                            detail.scan_keluar_sampai = this.state.settings.scan_keluar_sampai;
                        }
                    },

                    filteredRombel() {
                        const keyword = this.rombelSearch.trim().toLowerCase();

                        if (!keyword) {
                            return this.state.rombel;
                        }

                        return this.state.rombel.filter((rombel) => {
                            return [rombel.nama, rombel.tingkat, rombel.jurusan, rombel.indeks]
                                .filter(Boolean)
                                .join(' ')
                                .toLowerCase()
                                .includes(keyword);
                        });
                    },

                    selectedRombel() {
                        const ids = this.eventForm.selected_rombel_ids;

                        return this.state.rombel.filter((rombel) => ids.includes(Number(rombel.id)));
                    },

                    detailLabel(id) {
                        const detail = this.eventForm.detail_kelas[String(id)] || {};
                        const label = this.state.tipeOptions[detail.tipe] || detail.tipe || '-';

                        if (detail.tipe === 'libur') {
                            return label;
                        }

                        const scanLabel = detail.gunakan_window_scan
                            ? ` • Scan ${this.formatRange(detail.scan_masuk_mulai, detail.scan_masuk_sampai)} / ${this.formatRange(detail.scan_keluar_mulai, detail.scan_keluar_sampai)}`
                            : ' • Scan default';

                        return `${label} • ${detail.jam_masuk || '--:--'} - ${detail.jam_pulang || '--:--'}${scanLabel}`;
                    },

                    monthEvents() {
                        return Object.values(this.state.eventsByDate || {})
                            .sort((a, b) => a.tanggal.localeCompare(b.tanggal));
                    },

                    nextMonth() {
                        let month = Number(this.state.month) + 1;
                        let year = Number(this.state.year);

                        if (month > 12) {
                            month = 1;
                            year++;
                        }

                        this.loadMonth(year, month);
                    },

                    previousMonth() {
                        let month = Number(this.state.month) - 1;
                        let year = Number(this.state.year);

                        if (month < 1) {
                            month = 12;
                            year--;
                        }

                        this.loadMonth(year, month);
                    },

                    loadMonth(year, month) {
                        this.beginRequest('loading');
                        this.runServerAction(
                            () => wire.changeMonth(year, month),
                            'Gagal memuat bulan. Coba lagi.',
                        );
                    },

                    goToday() {
                        this.beginRequest('loading');
                        this.runServerAction(
                            () => wire.goToday(),
                            'Gagal memuat tanggal hari ini. Coba lagi.',
                        );
                    },

                    saveEvent() {
                        this.formError = '';

                        if (!this.eventForm.tanggal_mulai || !this.eventForm.tanggal_selesai) {
                            this.formError = 'Tanggal mulai dan tanggal selesai wajib diisi.';
                            return;
                        }

                        if (this.eventForm.tanggal_selesai < this.eventForm.tanggal_mulai) {
                            this.formError = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
                            return;
                        }

                        if (!this.eventForm.nama_acara.trim()) {
                            this.formError = 'Nama event wajib diisi.';
                            return;
                        }

                        if (this.eventForm.selected_rombel_ids.length === 0) {
                            this.formError = 'Minimal pilih satu kelas terdampak.';
                            return;
                        }

                        for (const id of this.eventForm.selected_rombel_ids) {
                            const detail = this.eventForm.detail_kelas[String(id)] || {};
                            this.applyTypeDefault(id);

                            if (detail.tipe !== 'libur' && detail.gunakan_window_scan) {
                                const scanFields = [
                                    detail.scan_masuk_mulai,
                                    detail.scan_masuk_sampai,
                                    detail.scan_keluar_mulai,
                                    detail.scan_keluar_sampai,
                                ];

                                if (scanFields.some((value) => !value)) {
                                    this.formError = 'Lengkapi semua jam scan masuk dan scan pulang untuk kelas yang window scan khususnya aktif.';
                                    return;
                                }
                            }
                        }

                        this.beginRequest('saving');
                        this.runServerAction(
                            () => wire.saveEvent(JSON.parse(JSON.stringify(this.eventForm))),
                            'Gagal menyimpan. Periksa kembali data yang diisi.',
                        );
                    },

                    saveDefault() {
                        this.beginRequest('savingDefault');
                        this.runServerAction(
                            () => wire.saveDefault(JSON.parse(JSON.stringify(this.defaultForm))),
                            'Gagal menyimpan default. Periksa kembali jam yang diisi.',
                        );
                    },

                    deleteSelectedDate() {
                        if (!this.selectedEvent()) {
                            return;
                        }

                        if (!confirm('Hapus semua event kelas pada tanggal ini?')) {
                            return;
                        }

                        this.beginRequest('saving');
                        this.runServerAction(
                            () => wire.deleteDate(this.selectedDate),
                            'Gagal menghapus event. Coba lagi.',
                        );
                    },

                    resetToDefault() {
                        if (!this.selectedEvent()) {
                            return;
                        }

                        if (!confirm('Kembalikan jadwal ke default untuk tanggal ini?')) {
                            return;
                        }

                        this.beginRequest('saving');
                        this.runServerAction(
                            () => wire.deleteDate(this.selectedDate),
                            'Gagal reset jadwal ke default. Coba lagi.',
                        );
                    },

                    scrollToForm() {
                        this.$nextTick(() => {
                            document.getElementById('form-event-waktu')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    },


                    errorMessage(error, fallback) {
                        const errors = error?.response?.data?.errors || error?.errors || null;

                        if (errors) {
                            const first = Object.values(errors).flat()[0];
                            if (first) {
                                return first;
                            }
                        }

                        return fallback;
                    },

                    formatDate(date) {
                        if (!date) {
                            return '-';
                        }

                        const parts = date.split('-');

                        if (parts.length !== 3) {
                            return date;
                        }

                        return `${parts[2]} ${this.bulan[Number(parts[1]) - 1]} ${parts[0]}`;
                    },

                    formatRange(start, end) {
                        if (!start || !end) {
                            return '--:--';
                        }

                        return `${start} - ${end}`;
                    },

                    scanMasukLabel() {
                        return this.formatRange(this.state.settings.scan_masuk_mulai, this.state.settings.scan_masuk_sampai);
                    },

                    scanKeluarLabel() {
                        return this.formatRange(this.state.settings.scan_keluar_mulai, this.state.settings.scan_keluar_sampai);
                    },

                    calendarClass(day) {
                        if (day.date === this.selectedDate) {
                            return 'border-blue-main bg-blue-50 shadow-sm ring-2 ring-blue-main/10';
                        }

                        if (day.hasEvent) {
                            return 'border-orange-200 bg-orange-50 hover:border-orange-400';
                        }

                        if (day.default.tipe === 'libur') {
                            return 'border-red-100 bg-red-50/70 hover:border-red-200';
                        }

                        if (day.default.tipe === 'khusus') {
                            return 'border-purple-100 bg-purple-50/70 hover:border-purple-200';
                        }

                        return 'border-gray-100 bg-white hover:border-blue-main/40 hover:bg-blue-50/40';
                    },

                    typePillClass(type) {
                        const classes = {
                            normal: 'bg-blue-50 text-blue-main',
                            pulang_cepat: 'bg-yellow-50 text-yellow-700',
                            pjj: 'bg-cyan-50 text-cyan-700',
                            libur: 'bg-red-50 text-red-600',
                            khusus: 'bg-purple-50 text-purple-700',
                        };

                        return classes[type] || 'bg-gray-50 text-gray-600';
                    },

                    tinyDotClass(type) {
                        const classes = {
                            normal: 'bg-blue-main',
                            pulang_cepat: 'bg-yellow-500',
                            pjj: 'bg-cyan-500',
                            libur: 'bg-red-500',
                            khusus: 'bg-purple-500',
                        };

                        return classes[type] || 'bg-gray-400';
                    },
                };
            }
        </script>
    @endonce
</div>
