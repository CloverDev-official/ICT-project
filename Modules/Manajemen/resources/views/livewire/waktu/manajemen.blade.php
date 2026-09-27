<div
    x-data="manajemenWaktu(@js($state), $wire)"
    x-init="init(); loadInitialState()"
    x-cloak
    wire:ignore
    class="space-y-5 bg-slate-50/70 p-3 sm:p-5"
>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep p-5 sm:p-7">
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
                        Pilih tanggal, atur event, lalu tentukan kelas dan jendela scan-nya.
                    </p>
                </div>
            </div>

            <button
                type="button"
                @click="goToday()"
                :disabled="loading || !isReady"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-blue-main transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white/70 disabled:cursor-wait disabled:opacity-60"
            >
                <iconify-icon icon="solar:calendar-mark-bold" width="20" height="20"></iconify-icon>
                Hari Ini
            </button>
        </div>
    </div>

    <div
        x-show="!isReady"
        x-transition.opacity
        class="grid grid-cols-1 gap-5 lg:grid-cols-12"
    >
        <section class="space-y-5 lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
                <div class="mb-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="h-7 w-48 animate-pulse rounded-xl bg-gray-200"></div>
                        <div class="mt-3 h-4 w-72 max-w-full animate-pulse rounded-xl bg-gray-100"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-11 w-11 animate-pulse rounded-2xl bg-gray-100"></div>
                        <div class="h-11 w-11 animate-pulse rounded-2xl bg-gray-100"></div>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-1 sm:gap-2">
                    <template x-for="index in 42" :key="index">
                        <div class="min-h-16 animate-pulse rounded-2xl border border-gray-100 bg-gray-50 sm:min-h-24"></div>
                    </template>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-4 h-6 w-40 animate-pulse rounded-xl bg-gray-200"></div>
                <div class="space-y-3">
                    <div class="h-12 animate-pulse rounded-2xl bg-gray-100"></div>
                    <div class="h-12 animate-pulse rounded-2xl bg-gray-100"></div>
                    <div class="h-12 animate-pulse rounded-2xl bg-gray-100"></div>
                </div>
            </div>

        </section>

        <aside class="space-y-5 lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
                <div class="h-5 w-28 animate-pulse rounded-xl bg-blue-100"></div>
                <div class="mt-3 h-8 w-44 animate-pulse rounded-xl bg-gray-200"></div>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div class="h-24 animate-pulse rounded-2xl bg-gray-100"></div>
                    <div class="h-24 animate-pulse rounded-2xl bg-gray-100"></div>
                </div>
                <div class="mt-5 space-y-3">
                    <div class="h-12 animate-pulse rounded-2xl bg-gray-100"></div>
                    <div class="h-12 animate-pulse rounded-2xl bg-gray-100"></div>
                    <div class="h-28 animate-pulse rounded-2xl bg-gray-100"></div>
                </div>
            </div>
        </aside>
    </div>

    <div
        x-show="isReady"
        x-transition.opacity
        class="grid grid-cols-1 gap-6 lg:grid-cols-12"
    >
        <section class="space-y-6 lg:col-span-8">
            <div class="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800" x-text="state.monthLabel"></h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Pilih tanggal untuk melihat statusnya dan mulai mengatur event.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="previousMonth()"
                            :disabled="loading || !isReady"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:border-blue-main hover:text-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/20 disabled:cursor-wait disabled:opacity-50"
                            aria-label="Bulan sebelumnya"
                        >
                            <iconify-icon icon="lineicons:chevron-left" width="19" height="19"></iconify-icon>
                        </button>
                        <button
                            type="button"
                            @click="nextMonth()"
                            :disabled="loading || !isReady"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:border-blue-main hover:text-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/20 disabled:cursor-wait disabled:opacity-50"
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
                                    class="group flex min-h-16 w-full flex-col rounded-xl border p-2 text-left transition focus:outline-none focus:ring-2 focus:ring-blue-main/30 sm:min-h-24 sm:p-3"
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

            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Event Bulan Ini</h3>
                        <p class="text-sm text-gray-500">Ringkasan tanggal yang punya jadwal khusus per kelas.</p>
                    </div>
                    <div class="rounded-full bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-main">
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

            <div class="rounded-2xl border border-blue-100 bg-white p-4 sm:p-6">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-main text-white"><iconify-icon icon="solar:clock-circle-bold" width="18" height="18"></iconify-icon></span>
                    <div>
                        <h3 class="font-semibold text-slate-800">Jendela Scan Default</h3>
                        <p class="text-xs text-slate-500">Digunakan pada hari tanpa event.</p>
                    </div>
                </div>
                <form @submit.prevent="saveDefault" class="grid gap-5 lg:grid-cols-2">
                    <div class="border-t border-slate-200 pt-4">
                        <div class="mb-3"><p class="font-bold text-gray-800">Senin–Kamis</p><p class="text-xs text-gray-500">Jendela scan masuk dan pulang reguler.</p></div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div><label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Masuk Mulai</label><input type="time" x-model="defaultForm.scan_masuk_mulai" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/10"></div>
                            <div><label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Masuk Sampai</label><input type="time" x-model="defaultForm.scan_masuk_sampai" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/10"><p class="mt-1 text-[11px] text-gray-500">Jam dasar sebelum toleransi.</p></div>
                            <div class="col-span-2"><label class="mb-1 block text-xs font-bold uppercase text-gray-400">Toleransi Masuk (Menit)</label><input type="number" min="0" max="720" step="1" x-model.number="defaultForm.toleransi_masuk" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/10"><p class="mt-1 text-[11px] text-gray-500">Batas scan disimpan sebagai jam dasar + toleransi.</p></div>
                            <div><label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Pulang Mulai</label><input type="time" x-model="defaultForm.scan_keluar_mulai" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/10"></div>
                            <div><label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Pulang Sampai</label><input type="time" x-model="defaultForm.scan_keluar_sampai" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/10"></div>
                        </div>
                    </div>
                    <div class="border-t border-amber-200 pt-4">
                        <div class="mb-3"><p class="font-bold text-gray-800">Khusus Jumat</p><p class="text-xs text-gray-500">Scan masuk mengikuti hari reguler.</p></div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div><label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Pulang Mulai</label><input type="time" x-model="defaultForm.scan_keluar_jumat_mulai" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/10"></div>
                            <div><label class="mb-1 block text-xs font-bold uppercase text-gray-400">Scan Pulang Sampai</label><input type="time" x-model="defaultForm.scan_keluar_jumat_sampai" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/10"></div>
                        </div>
                    </div>
                    <button type="submit" :disabled="savingDefault || !isReady" class="lg:col-span-2 justify-self-end rounded-xl bg-blue-main px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-deep focus:outline-none focus:ring-2 focus:ring-blue-main/30 disabled:cursor-wait disabled:opacity-60"><span x-show="!savingDefault">Simpan Waktu Scan</span><span x-show="savingDefault">Menyimpan...</span></button>
                </form>
            </div>
        </section>

        <aside class="flex flex-col gap-5 lg:col-span-4 lg:sticky lg:top-5 lg:max-h-[calc(100vh-2.5rem)] lg:overflow-y-auto lg:pr-2 self-start">
            <div class="relative rounded-xl border border-slate-200 bg-white p-3 sm:p-4">
                <div class="mb-3 flex items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-blue-main">Tanggal Dipilih</p>
                        <h3 class="text-lg font-bold text-gray-800" x-text="formatDate(selectedDate)"></h3>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center justify-end gap-1.5">
                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold" :class="selectedEvent() ? 'bg-orange-50 text-orange-600' : typePillClass(selectedDefault().tipe)" x-text="selectedEvent() ? 'Ada Event' : selectedDefault().label"></span>
                        <button
                            type="button"
                            x-show="selectedEvent()"
                            @click="resetToDefault"
                            class="rounded-full border border-gray-200 px-2.5 py-1 text-[11px] font-bold text-gray-600 transition hover:border-blue-main hover:text-blue-main"
                        >
                            Reset ke Default
                        </button>
                    </div>
                </div>

                <div
                    x-show="dateLoading"
                    x-transition.opacity
                    class="absolute inset-x-4 top-24 z-20 rounded-2xl border border-blue-100 bg-white/95 p-4 shadow-lg backdrop-blur sm:inset-x-5"
                >
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 animate-pulse rounded-2xl bg-blue-100"></div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-gray-800">Memuat detail tanggal...</p>
                            <div class="mt-2 h-3 w-52 max-w-full animate-pulse rounded-full bg-gray-100"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-4 grid gap-2 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs text-gray-600">
                        <template x-if="selectedEvent()">
                            <div>
                                <p class="font-bold text-gray-800" x-text="selectedEvent().nama_acara"></p>
                                <p class="mt-1" x-text="selectedEvent().summary"></p>
                                <p class="mt-1 text-[11px] text-gray-400">Detail kelas ada di bawah.</p>
                            </div>
                        </template>
                        <template x-if="!selectedEvent()">
                            <div>
                                <p class="font-bold text-gray-800" x-text="selectedDefault().label"></p>
                                <p class="mt-1" x-text="selectedDefault().keterangan"></p>
                                <p class="mt-1 text-[11px] text-gray-400">Mengikuti jendela scan aktif.</p>
                            </div>
                        </template>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs text-gray-600">
                        <p class="text-[11px] font-bold uppercase text-gray-400">Jendela Scan</p>
                        <div class="mt-1.5 space-y-1">
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

            </div>

            <div id="form-event-waktu" class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
                <form @submit.prevent="saveEvent" class="space-y-5">
                    <section class="border-b border-slate-200 pb-5">
                        <div class="mb-4 flex items-start gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-main text-xs font-bold text-white">1</span>
                            <div>
                                <h4 class="font-semibold text-slate-800">Tanggal & Event</h4>
                                <p class="text-xs text-slate-500">Tentukan rentang tanggal dan informasi event.</p>
                            </div>
                        </div>
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
                    </section>

                    <section class="border-b border-slate-200 pb-5">
                        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-main text-xs font-bold text-white">2</span>
                                    <p class="font-semibold text-slate-800">Preset Kelas</p>
                                </div>
                                <p class="text-xs text-gray-500">Pilih tipe lalu isi jendela scan. Preset ini langsung dipakai saat kelas dipilih.</p>
                            </div>
                            <button type="button" @click="applyPresetToAll" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-main transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-main/20">
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
                                <input type="text" x-model="preset.keterangan" placeholder="Keterangan kelas" class="rounded-2xl border border-gray-200 px-3 py-3 text-sm focus:border-blue-main focus:outline-none sm:col-span-3">
                            </div>

                            <div class="border-l-2 border-blue-main/30 pl-3" x-show="preset.tipe !== 'libur'">
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Scan Kelas</p>
                                    <p class="mt-1 text-xs text-gray-500">Langsung isi waktu scan untuk kelas yang dipilih.</p>
                                </div>
                                <div
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
                                        <p class="mt-1 text-[11px] text-gray-400">Jam dasar sebelum toleransi.</p>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Toleransi Masuk (Menit)</label>
                                        <input type="number" min="0" max="720" step="1" x-model.number="preset.toleransi_masuk" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                        <p class="mt-1 text-[11px] text-gray-400">Batas akhir = jam dasar + toleransi.</p>
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

                                <p class="mt-3 text-xs text-blue-700">Jendela ini akan disalin ke kelas yang dipilih.</p>
                            </div>
                        </div>
                    </section>

                    <section class="border-b border-slate-200 pb-5">
                        <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-main text-xs font-bold text-white">3</span>
                                    <p class="font-semibold text-slate-800">Kelas Terdampak</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    <span x-text="eventForm.selected_rombel_ids.length"></span> kelas dipilih.
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="selectFilteredRombel" class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-bold text-blue-main hover:bg-blue-100">Pilih tampil</button>
                                <button type="button" @click="clearRombel" class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100">Kosongkan</button>
                            </div>
                        </div>

                        <input type="search" x-model.debounce.150ms="rombelSearch" placeholder="Cari kelas berdasarkan nama, tingkat, atau jurusan..." class="mb-3 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm placeholder:text-slate-400 focus:border-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/10">

                        <div class="max-h-56 space-y-1 overflow-y-auto rounded-xl border border-slate-100 p-1.5">
                            <template x-for="rombel in filteredRombel()" :key="rombel.id">
                                <button
                                    type="button"
                                    @click="toggleRombel(rombel.id)"
                                    class="flex w-full items-center justify-between gap-3 rounded-lg border px-3 py-2.5 text-left transition focus:outline-none focus:ring-2 focus:ring-blue-main/20"
                                    :class="isRombelSelected(rombel.id) ? 'border-blue-main bg-blue-50' : 'border-transparent hover:border-slate-200 hover:bg-slate-50'"
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
                    </section>

                    <section class="space-y-3" x-show="eventForm.selected_rombel_ids.length > 0">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-main text-xs font-bold text-white">4</span>
                                <p class="font-semibold text-slate-800">Detail Per Kelas</p>
                            </div>
                            <p class="text-xs text-slate-400">Bisa berbeda.</p>
                        </div>

                        <div class="max-h-[560px] space-y-2 overflow-y-auto pr-1">
                            <div class="grid grid-cols-1 gap-2">
	                                <template x-for="rombel in visibleSelectedRombel()" :key="rombel.id">
                                    <div class="rounded-xl border border-slate-200 bg-white p-3">
                                    <div class="mb-3 flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-800" x-text="rombel.nama"></p>
                                            <p class="text-xs text-gray-400" x-text="detailLabel(rombel.id)"></p>
                                        </div>
                                        <button type="button" @click="toggleRombel(rombel.id)" class="rounded-lg px-2 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-200">Hapus</button>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            <select x-model="eventForm.detail_kelas[String(rombel.id)].tipe" @change="applyTypeDefault(rombel.id)" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none sm:col-span-2">
                                                <template x-for="(label, key) in state.tipeOptions" :key="key">
                                                    <option :value="key" x-text="label"></option>
                                                </template>
                                            </select>
                                            <textarea x-model="eventForm.detail_kelas[String(rombel.id)].keterangan" rows="2" placeholder="Keterangan khusus kelas ini" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-main focus:outline-none sm:col-span-2"></textarea>
                                        </div>

                                        <div class="border-l-2 border-blue-main/30 pl-3" x-show="eventForm.detail_kelas[String(rombel.id)].tipe !== 'libur'">
                                            <div>
                                                <p class="text-sm font-bold text-gray-800">Scan Kelas</p>
                                                <p class="mt-1 text-xs text-gray-500">Perubahan di sini langsung menjadi jadwal scan kelas ini.</p>
                                            </div>
                                            <div
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
                                                    <p class="mt-1 text-[11px] text-gray-400">Jam dasar sebelum toleransi.</p>
                                                </div>
                                                <div class="col-span-2">
                                                    <label class="mb-1 block text-[11px] font-bold uppercase text-gray-400">Toleransi Masuk (Menit)</label>
                                                    <input type="number" min="0" max="720" step="1" x-model.number="eventForm.detail_kelas[String(rombel.id)].toleransi_masuk" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-main focus:outline-none">
                                                    <p class="mt-1 text-[11px] text-gray-400">Batas akhir = jam dasar + toleransi.</p>
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

	                                            <p class="mt-3 text-xs text-blue-700">Scanner akan menggunakan Scan Kelas ini.</p>
	                            </div>
                                <div class="pt-3" x-show="selectedRombel().length > selectedDetailLimit">
                                    <button
                                        type="button"
                                        @click="showMoreSelectedDetails"
                                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm font-bold text-blue-main transition hover:border-blue-main hover:bg-blue-50"
                                    >
                                        Tampilkan <span x-text="Math.min(detailBatchSize, selectedRombel().length - selectedDetailLimit)"></span> kelas lagi
                                    </button>
                                </div>
	                        </div>
	                    </div>
                                </template>
                            </div>
                        </div>
                    </section>

                    <template x-if="formError">
                        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600" x-text="formError"></div>
                    </template>

                    <div class="flex flex-col-reverse gap-2 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end">
                        <button
                            type="submit"
                            :disabled="saving || dateLoading || !isReady"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-main px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-deep focus:outline-none focus:ring-2 focus:ring-blue-main/30 disabled:cursor-wait disabled:opacity-60"
                        >
                            <span x-show="!saving">Simpan Event</span>
                            <span x-show="saving">Menyimpan...</span>
                        </button>
                        <button
                            type="button"
                            @click="resetFormFromDate(selectedDate)"
                            :disabled="dateLoading"
                            class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-blue-main hover:text-blue-main focus:outline-none focus:ring-2 focus:ring-blue-main/20"
                        >
                            Reset Form
                        </button>
                    </div>

                    <button
                        type="button"
                        x-show="selectedEvent()"
                        @click="deleteSelectedDate"
                        :disabled="saving || dateLoading || !isReady"
                        class="w-full rounded-xl border border-red-100 px-5 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-200 disabled:cursor-wait disabled:opacity-60"
                    >
                        Hapus Semua Event di Tanggal Ini
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
                    defaultForm: {
                        ...JSON.parse(JSON.stringify(initialState.settings)),
                        scan_masuk_sampai: initialState.settings.scan_masuk_sampai_dasar || initialState.settings.scan_masuk_sampai,
                    },
                    rombelSearch: '',
                    isReady: Boolean(initialState.ready),
                    bootingInitial: false,
                    dateLoading: false,
                    dateLoadTimer: null,
                    dateRequestToken: 0,
                    selectedDetailLimit: 24,
                    detailBatchSize: 24,
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

                    loadInitialState() {
                        if (this.isReady || this.bootingInitial) {
                            return;
                        }

                        this.bootingInitial = true;
                        this.beginRequest('loading');
                        this.runServerAction(
                            () => wire.loadInitialState(),
                            'Gagal memuat kalender. Coba muat ulang halaman.',
                        ).finally(() => {
                            this.bootingInitial = false;
                            this.scheduleSelectedDateDetails(this.selectedDate);
                        });
                    },

                    applyServerState(nextState) {
                        if (!nextState || typeof nextState !== 'object') {
                            this.finishRequest();
                            return;
                        }

                        this.state = JSON.parse(JSON.stringify(nextState));
                        this.isReady = Boolean(this.state.ready);
                        this.selectedDate = this.state.selectedDate || this.selectedDate;

                        if (this.state.settings) {
                            this.defaultForm = JSON.parse(JSON.stringify(this.state.settings));
                            this.defaultForm.scan_masuk_sampai = this.state.settings.scan_masuk_sampai_dasar || this.state.settings.scan_masuk_sampai;
                        }

                        this.resetFormFromDate(this.selectedDate);
                        this.finishRequest();
                    },

                    beginRequest(flag) {
                        this.formError = '';
                        this.loading = flag === 'loading';
                        this.saving = flag === 'saving';
                        this.savingDefault = flag === 'savingDefault';
                        this.dateLoading = flag === 'dateLoading';

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
                        this.dateLoading = false;

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
                        this.dateRequestToken++;
                        this.dateLoading = false;
                        this.selectedDate = date;
                        this.state.selectedDate = date;
                        this.resetFormFromDate(date);
                        this.scheduleSelectedDateDetails(date);
                    },

                    scheduleSelectedDateDetails(date) {
                        const event = this.state.eventsByDate[date] || null;

                        if (!event || event.details_loaded) {
                            return;
                        }

                        if (this.dateLoadTimer) {
                            clearTimeout(this.dateLoadTimer);
                        }

                        const token = ++this.dateRequestToken;

                        this.dateLoadTimer = setTimeout(() => {
                            this.loadSelectedDateDetails(date, token);
                        }, 120);
                    },

                    loadSelectedDateDetails(date, token = ++this.dateRequestToken) {
                        const event = this.state.eventsByDate[date] || null;

                        if (!event || event.details_loaded || date !== this.selectedDate) {
                            return Promise.resolve();
                        }

                        this.beginRequest('dateLoading');

                        return Promise.resolve(wire.loadDateDetails(date))
                            .then((response) => {
                                if (token === this.dateRequestToken && date === this.selectedDate) {
                                    this.applyDateDetails(response);
                                }
                            })
                            .catch((error) => {
                                if (token === this.dateRequestToken) {
                                    this.finishRequest();
                                    this.formError = this.errorMessage(error, 'Gagal memuat detail tanggal. Coba pilih tanggal lagi.');
                                }
                            })
                            .finally(() => {
                                if (token === this.dateRequestToken) {
                                    this.finishRequest();
                                }
                            });
                    },

                    applyDateDetails(response) {
                        if (!response || response.selectedDate !== this.selectedDate) {
                            return;
                        }

                        if (response.event) {
                            this.state.eventsByDate[response.selectedDate] = JSON.parse(JSON.stringify(response.event));
                        } else {
                            delete this.state.eventsByDate[response.selectedDate];
                        }

                        this.resetFormFromDate(response.selectedDate);
                    },

                    selectedEvent() {
                        return this.state.eventsByDate[this.selectedDate] || null;
                    },

                    selectedDefault() {
                        const day = this.state.days.find((item) => item && item.date === this.selectedDate);

                        return day ? day.default : {
                            tipe: 'normal',
                            label: 'Normal',
                            scan_masuk_mulai: this.state.settings.scan_masuk_mulai,
                            scan_masuk_sampai: this.state.settings.scan_masuk_sampai,
                            scan_keluar_mulai: this.state.settings.scan_keluar_mulai,
                            scan_keluar_sampai: this.state.settings.scan_keluar_sampai,
                            keterangan: 'Jadwal normal.',
                        };
                    },

                    resetFormFromDate(date) {
                        this.formError = '';
                        this.selectedDetailLimit = this.detailBatchSize;
                        const event = this.state.eventsByDate[date] || null;
                        const defaults = this.selectedDefault();
                        const defaultType = defaults.tipe === 'normal' ? 'pulang_cepat' : defaults.tipe;

                        this.preset = {
                            tipe: defaultType,
                            gunakan_window_scan: defaultType !== 'libur',
                            scan_masuk_mulai: defaultType === 'libur' ? '' : (defaults.scan_masuk_mulai || this.state.settings.scan_masuk_mulai),
                            scan_masuk_sampai: defaultType === 'libur' ? '' : this.state.settings.scan_masuk_sampai_dasar,
                            toleransi_masuk: this.state.settings.toleransi_masuk,
                            scan_keluar_mulai: defaultType === 'libur' ? '' : (defaults.scan_keluar_mulai || this.state.settings.scan_keluar_mulai),
                            scan_keluar_sampai: defaultType === 'libur' ? '' : (defaults.scan_keluar_sampai || this.state.settings.scan_keluar_sampai),
                            keterangan: '',
                        };

                        this.eventForm = {
                            tanggal_mulai: date,
                            tanggal_selesai: date,
                            nama_acara: event ? (event.nama_acara || '') : '',
                            keterangan: event ? (event.keterangan || '') : '',
                            selected_rombel_ids: event && event.details_loaded ? event.details.map((item) => Number(item.rombel_id)) : [],
                            detail_kelas: {},
                        };

                        if (event && event.details_loaded) {
                            event.details.forEach((item) => {
                                this.eventForm.detail_kelas[String(item.rombel_id)] = {
                                    tipe: item.tipe,
                                    gunakan_window_scan: item.tipe !== 'libur',
                                    scan_masuk_mulai: item.scan_masuk_mulai || defaults.scan_masuk_mulai || '',
                                    scan_masuk_sampai: item.scan_masuk_sampai_dasar || this.state.settings.scan_masuk_sampai_dasar || '',
                                    toleransi_masuk: item.toleransi_masuk ?? this.state.settings.toleransi_masuk,
                                    scan_keluar_mulai: item.scan_keluar_mulai || defaults.scan_keluar_mulai || '',
                                    scan_keluar_sampai: item.scan_keluar_sampai || defaults.scan_keluar_sampai || '',
                                    keterangan: item.keterangan || '',
                                };
                            });
                        }
                    },

                    syncPresetTime() {
                        if (this.preset.tipe === 'libur') {
                            this.preset.gunakan_window_scan = false;
                            this.preset.scan_masuk_mulai = '';
                            this.preset.scan_masuk_sampai = '';
                            this.preset.toleransi_masuk = this.state.settings.toleransi_masuk;
                            this.preset.scan_keluar_mulai = '';
                            this.preset.scan_keluar_sampai = '';
                            return;
                        }

                        this.preset.gunakan_window_scan = true;
                        this.fillPresetScanFromDefault();
                    },

                    fillPresetScanFromDefault() {
                        const defaults = this.selectedDefault();

                        if (!this.preset.scan_masuk_mulai) {
                            this.preset.scan_masuk_mulai = defaults.scan_masuk_mulai;
                        }

                        if (!this.preset.scan_masuk_sampai) {
                            this.preset.scan_masuk_sampai = this.state.settings.scan_masuk_sampai_dasar;
                        }

                        if (!Number.isInteger(this.preset.toleransi_masuk)) {
                            this.preset.toleransi_masuk = this.state.settings.toleransi_masuk;
                        }

                        if (!this.preset.scan_keluar_mulai) {
                            this.preset.scan_keluar_mulai = defaults.scan_keluar_mulai;
                        }

                        if (!this.preset.scan_keluar_sampai) {
                            this.preset.scan_keluar_sampai = defaults.scan_keluar_sampai;
                        }
                    },

                    detailFromPreset() {
                        this.syncPresetTime();

                        const gunakanWindowScan = this.preset.tipe !== 'libur';

                        return {
                            tipe: this.preset.tipe,
                            gunakan_window_scan: gunakanWindowScan,
                            scan_masuk_mulai: gunakanWindowScan ? this.preset.scan_masuk_mulai : '',
                            scan_masuk_sampai: gunakanWindowScan ? this.preset.scan_masuk_sampai : '',
                            toleransi_masuk: gunakanWindowScan ? this.preset.toleransi_masuk : null,
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
                            detail.gunakan_window_scan = false;
                            detail.scan_masuk_mulai = '';
                            detail.scan_masuk_sampai = '';
                            detail.toleransi_masuk = this.state.settings.toleransi_masuk;
                            detail.scan_keluar_mulai = '';
                            detail.scan_keluar_sampai = '';
                            return;
                        }

                        detail.gunakan_window_scan = true;
                        this.fillDetailScanFromDefault(detail);
                    },

                    fillDetailScanFromDefault(detail) {
                        const defaults = this.selectedDefault();

                        if (!detail.scan_masuk_mulai) {
                            detail.scan_masuk_mulai = defaults.scan_masuk_mulai;
                        }

                        if (!detail.scan_masuk_sampai) {
                            detail.scan_masuk_sampai = this.state.settings.scan_masuk_sampai_dasar;
                        }

                        if (!Number.isInteger(detail.toleransi_masuk)) {
                            detail.toleransi_masuk = this.state.settings.toleransi_masuk;
                        }

                        if (!detail.scan_keluar_mulai) {
                            detail.scan_keluar_mulai = defaults.scan_keluar_mulai;
                        }

                        if (!detail.scan_keluar_sampai) {
                            detail.scan_keluar_sampai = defaults.scan_keluar_sampai;
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

                    visibleSelectedRombel() {
                        return this.selectedRombel().slice(0, this.selectedDetailLimit);
                    },

                    showMoreSelectedDetails() {
                        this.selectedDetailLimit += this.detailBatchSize;
                    },

                    detailLabel(id) {
                        const detail = this.eventForm.detail_kelas[String(id)] || {};
                        const label = this.state.tipeOptions[detail.tipe] || detail.tipe || '-';

                        if (detail.tipe === 'libur') {
                            return label;
                        }

                        const scanLabel = ` • Scan ${this.formatRange(detail.scan_masuk_mulai, detail.scan_masuk_sampai)} / ${this.formatRange(detail.scan_keluar_mulai, detail.scan_keluar_sampai)}`;

                        return `${label}${scanLabel}`;
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
                        ).finally(() => {
                            this.scheduleSelectedDateDetails(this.selectedDate);
                        });
                    },

                    goToday() {
                        this.beginRequest('loading');
                        this.runServerAction(
                            () => wire.goToday(),
                            'Gagal memuat tanggal hari ini. Coba lagi.',
                        ).finally(() => {
                            this.scheduleSelectedDateDetails(this.selectedDate);
                        });
                    },

                    saveEvent() {
                        this.formError = '';
                        const payload = JSON.parse(JSON.stringify(this.eventForm));

                        if (!payload.tanggal_mulai || !payload.tanggal_selesai) {
                            this.formError = 'Tanggal mulai dan tanggal selesai wajib diisi.';
                            return;
                        }

                        if (payload.tanggal_selesai < payload.tanggal_mulai) {
                            this.formError = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
                            return;
                        }

                        payload.nama_acara = (payload.nama_acara || '').trim();
                        payload.keterangan = payload.keterangan || '';
                        payload.selected_rombel_ids = (payload.selected_rombel_ids || []).map((id) => Number(id));
                        payload.detail_kelas = payload.detail_kelas || {};

                        if (!payload.nama_acara) {
                            this.formError = 'Nama event wajib diisi.';
                            return;
                        }

                        if (payload.selected_rombel_ids.length === 0) {
                            this.formError = 'Minimal pilih satu kelas terdampak.';
                            return;
                        }

                        for (const id of payload.selected_rombel_ids) {
                            this.applyTypeDefault(id);
                            const key = String(id);
                            const detail = this.eventForm.detail_kelas[key] || {};
                            payload.detail_kelas[key] = {
                                tipe: detail.tipe || 'libur',
                                gunakan_window_scan: detail.tipe !== 'libur',
                                scan_masuk_mulai: detail.scan_masuk_mulai || '',
                                scan_masuk_sampai: detail.scan_masuk_sampai || '',
                                toleransi_masuk: Number(detail.toleransi_masuk),
                                scan_keluar_mulai: detail.scan_keluar_mulai || '',
                                scan_keluar_sampai: detail.scan_keluar_sampai || '',
                                keterangan: detail.keterangan || '',
                            };

                            if (payload.detail_kelas[key].gunakan_window_scan) {
                                if (!Number.isInteger(payload.detail_kelas[key].toleransi_masuk)
                                    || payload.detail_kelas[key].toleransi_masuk < 0
                                    || payload.detail_kelas[key].toleransi_masuk > 720) {
                                    this.formError = 'Toleransi masuk setiap kelas harus berupa bilangan bulat antara 0 sampai 720 menit.';
                                    return;
                                }

                                const scanFields = [
                                    payload.detail_kelas[key].scan_masuk_mulai,
                                    payload.detail_kelas[key].scan_masuk_sampai,
                                    payload.detail_kelas[key].scan_keluar_mulai,
                                    payload.detail_kelas[key].scan_keluar_sampai,
                                ];

                                if (scanFields.some((value) => !value)) {
                                    this.formError = 'Lengkapi semua jam scan masuk dan scan pulang untuk kelas yang window scan khususnya aktif.';
                                    return;
                                }
                            }
                        }

                        this.beginRequest('saving');
                        this.runServerAction(
                            () => wire.saveEvent(payload),
                            'Gagal menyimpan. Periksa kembali data yang diisi.',
                        ).finally(() => {
                            this.scheduleSelectedDateDetails(this.selectedDate);
                        });
                    },

                    saveDefault() {
                        const payload = JSON.parse(JSON.stringify(this.defaultForm || {}));
                        const requiredTimes = {
                            scan_masuk_mulai: 'Jam mulai scan masuk wajib diisi.',
                            scan_masuk_sampai: 'Jam akhir scan masuk wajib diisi.',
                            scan_keluar_mulai: 'Jam mulai scan pulang wajib diisi.',
                            scan_keluar_sampai: 'Jam akhir scan pulang wajib diisi.',
                            scan_keluar_jumat_mulai: 'Jam mulai scan pulang Jumat wajib diisi.',
                            scan_keluar_jumat_sampai: 'Jam akhir scan pulang Jumat wajib diisi.',
                        };

                        for (const [field, message] of Object.entries(requiredTimes)) {
                            payload[field] = payload[field] || '';

                            if (!payload[field]) {
                                this.formError = message;
                                return;
                            }
                        }

                        payload.toleransi_masuk = Number(payload.toleransi_masuk);
                        if (!Number.isInteger(payload.toleransi_masuk) || payload.toleransi_masuk < 0 || payload.toleransi_masuk > 720) {
                            this.formError = 'Toleransi masuk harus berupa bilangan bulat antara 0 sampai 720 menit.';
                            return;
                        }

                        this.beginRequest('savingDefault');
                        this.runServerAction(
                            () => wire.saveDefault(payload),
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
                        const errors = error?.response?.data?.errors
                            || error?.response?.data?.serverMemo?.errors
                            || error?.errors
                            || error?.serverMemo?.errors
                            || null;

                        if (errors) {
                            const first = Object.values(errors).flat()[0];
                            if (first) {
                                return first;
                            }
                        }

                        return error?.message || fallback;
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
                        const defaults = this.selectedDefault();
                        return this.formatRange(defaults.scan_masuk_mulai, defaults.scan_masuk_sampai);
                    },

                    scanKeluarLabel() {
                        const defaults = this.selectedDefault();
                        return this.formatRange(defaults.scan_keluar_mulai, defaults.scan_keluar_sampai);
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
