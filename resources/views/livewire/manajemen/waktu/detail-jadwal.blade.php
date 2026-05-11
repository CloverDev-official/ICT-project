<aside class="w-full lg:w-80 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col">

    <div class="p-6 flex-grow">

        <h2 class="text-lg font-bold text-slate-800 mb-4">
            Detail Jadwal
        </h2>

        {{-- ========================= --}}
        {{-- CUSTOM MODE --}}
        {{-- ========================= --}}

        @if ($isCustomMode)

            <div class="space-y-5">

                {{-- TANGGAL --}}
                <div>
                    <label class="block text-sm text-slate-400 mb-2">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        wire:model="selectedDate"
                        class="w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    >
                </div>

                {{-- NAMA ACARA --}}
                <div>
                    <label class="block text-sm text-slate-400 mb-2">
                        Nama Acara
                    </label>

                    <input
                        type="text"
                        wire:model="title"
                        placeholder="Contoh: Hari Kemerdekaan"
                        class="w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    >
                </div>

                {{-- JAM --}}
                <div>

                    <label class="block text-sm text-slate-400 mb-2">
                        Jadwal
                    </label>

                    <div class="flex gap-2">

                        <input
                            type="time"
                            @disabled($scheduleMode == 'libur')   
                            wire:model="masuk"
                            class="w-1/2 rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        >

                        <input
                            type="time"
                            @disabled($scheduleMode == 'libur')
                            wire:model="pulang"
                            class="w-1/2 rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                        >

                    </div>

                </div>

                {{-- KETERANGAN --}}
                <div>

                    <label class="block text-sm text-slate-400 mb-2">
                        Keterangan
                    </label>

                    <textarea
                        wire:model="keterangan"
                        class="w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                    ></textarea>

                </div>

                {{-- HARI LIBUR --}}
                <div class="flex items-center gap-3">

                    <input
                        type="checkbox"
                            wire:change="toggleHoliday"
                            @checked($scheduleMode == 'libur')
                    >

                    @if ($scheduleMode == 'libur')

                        <div class="text-xs text-red-500">
                            Hari ini akan dianggap sebagai hari libur
                        </div>  

                    @else

                        <div class="text-xs text-green-600">
                            Hari ini akan dianggap sebagai hari aktif
                        </div>

                    @endif

                </div>

            </div>

        @else
        {{-- ========================= --}}
        {{-- NORMAL MODE --}}
        {{-- ========================= --}}

            @if ($selectedDate)

                {{-- TANGGAL --}}
                <div class="mb-3">

                    <div class="text-xl font-bold text-slate-900">
                        {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                    </div>

                </div>

                {{-- LIBUR --}}
                @if (
                    ($scheduleMode == 'libur')
                    && !$isEditing
                )
                
                    <div class="w-24 bg-red-50 border border-red-200 text-red-500 p-2 mb-4 text-xs rounded-full font-bold text-center">
                        Hari Libur
                    </div>

                    <div class="space-y-5 border-t border-gray-200 pt-6">


                        @if ($title)

                            <div>

                                <p class="text-sm text-slate-400 mb-1">
                                    Nama Acara
                                </p>

                                <div class="font-bold text-slate-800">
                                    {{ $title }}
                                </div>

                            </div>

                        @endif

                        <div>

                            <p class="text-sm text-slate-400 mb-1">
                                Keterangan
                            </p>

                            <div class="text-slate-700">
                                {{ $keterangan }}
                            </div>

                        </div>

                        <button 
                            wire:click="editJadwal"
                            class="w-full py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition"
                        >
                            Edit Jadwal
                        </button>

                    </div>

                @else

                    {{-- ========================= --}}
                    {{-- VIEW MODE --}}
                    {{-- ========================= --}}
                    @if (!$isEditing)

                        @if ($scheduleMode == 'normal')
                            <div class="w-24 bg-blue-50 border border-calendar-blue text-calendar-blue p-2 mb-4 text-xs rounded-full font-bold text-center">
                                Hari Normal
                            </div>
                        @elseif ($scheduleMode == 'khusus')
                            <div class="w-24 bg-purple-50 border border-calendar-purple-light text-calendar-purple p-2 mb-4 text-xs rounded-full font-bold text-center">
                                Hari Khusus
                            </div>
                        @elseif ($scheduleMode == 'custom')
                            <div class="w-24 bg-yellow-50 border border-calendar-yellow-light text-calendar-yellow p-2 mb-4 text-xs rounded-full font-bold text-center">
                                Hari Kustom
                            </div>
                        @endif

                        <div class="space-y-5 border-t pt-6">

                            <div>
                                @if ($title)

                                    <div>

                                        <p class="text-sm text-slate-400 mb-1">
                                            Nama Acara
                                        </p>

                                        <div class="font-bold text-slate-800 capitalize">
                                            {{ $title }}
                                        </div>

                                    </div>

                                @endif


                                <p class="text-slate-400 font-semibold text-sm mb-2 mt-2">Jadwal</p>

                                <div class="space-y-2">
                                    <div class="flex justify-between items-center gap-5">
                                        <p class="text-sm font-semibold text-slate-800">
                                            Masuk
                                        </p>
    
                                        <div class="font-bold text-slate-800">
                                            {{ $masuk }}
                                        </div>
                                    </div>
    
                                    <div class="flex justify-between items-center gap-5" >
    
                                        <p class="text-sm font-semibold text-slate-800">
                                            Pulang
                                        </p>
    
                                        <div class="font-bold text-slate-800">
                                            {{ $pulang }}
                                        </div>
    
                                    </div>
                                </div>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-slate-400 mb-1">
                                    Keterangan
                                </p>

                                <div class="text-sm text-slate-800">
                                    {{ $keterangan }}
                                </div>

                            </div>

                            <button
                                wire:click="editJadwal"
                                class="w-full py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition"
                            >
                                Edit Jadwal
                            </button>

                        </div>

                    @endif

                    {{-- ========================= --}}
                    {{-- EDIT MODE --}}
                    {{-- ========================= --}}
                    @if ($isEditing)

                        <div class="space-y-6 border-t pt-6">
                            
                            @if ($isSpecialSchedule)

                                <div>

                                    <label class="block text-sm text-slate-400 mb-2">
                                        Mode Jadwal
                                    </label>

                                    <select
                                        wire:model.live="scheduleMode"
                                        class="w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                                    >
                                        <option value="libur">
                                            Hari Libur
                                        </option>

                                        <option value="normal">
                                            Hari Aktif Normal
                                        </option>

                                        <option value="khusus">
                                            Jadwal Khusus
                                        </option>

                                    </select>

                                </div>

                            @endif

                            {{-- JAM --}}
                            <div>

                                <label class="block text-sm text-slate-400 mb-2">
                                    Jadwal
                                </label>

                                <div class="flex gap-2">

                                    <input
                                        type="time"
                                        wire:model="masuk"
                                        @disabled($scheduleMode == 'libur')
                                        class="w-1/2 rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                                    >

                                    <input
                                        type="time"
                                        wire:model="pulang"
                                        @disabled($scheduleMode == 'libur')
                                        class="w-1/2 rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                                    >

                                </div>

                            </div>

                            {{-- KETERANGAN --}}
                            <div>

                                <label class="block text-sm text-slate-400 mb-2">
                                    Keterangan
                                </label>

                                <textarea
                                    wire:model="keterangan"
                                    class="w-full rounded-xl border border-gray-300 focus:border-blue-main  focus:outline-hidden px-4 py-2"
                                ></textarea>

                            </div>

                        </div>

                    @endif

                @endif

            @else

                <div class="text-center text-slate-400 py-10">
                    Pilih tanggal terlebih dahulu
                </div>

            @endif

        @endif

    </div>

    {{-- ACTION --}}
    @if (($selectedDate && $isEditing) || $isCustomMode)

        <div class="p-6 border-t border-slate-100 flex gap-2">

            <button
                wire:click="saveJadwal"
                class="w-1/2 py-3 bg-blue-500 text-white font-bold rounded-xl hover:bg-blue-600 transition"
            >
                Simpan
            </button>

            <button
                wire:click="cancelEdit"
                class="w-1/2 py-3 bg-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-300 transition"
            >
                Batal
            </button>

        </div>

    @endif

</aside>
