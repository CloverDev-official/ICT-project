<div
    class="flex flex-col lg:flex-row gap-6 w-full max-w-6xl"
    data-purpose="calendar-dashboard"
    x-data="{ selectedDate: null }"
>
    <!-- BEGIN: Left Section - Calendar Grid -->
    <section class="flex-grow bg-white rounded-2xl shadow-sm border border-slate-100 p-6" data-purpose="calendar-view">
        <!-- BEGIN: Calendar Header -->
        <header class="grid grid-cols-1 gap-4 md:flex md:justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-slate-800">
                {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
            </h1>
            <div class="flex items-center gap-4">
                <div class="flex border border-slate-200 rounded-lg overflow-hidden">
                    <button wire:click="previousMonth" class="p-2 hover:bg-slate-50 border-r border-slate-200 flex items-center justify-center">
                        <iconify-icon icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
                    </button>
                    <button wire:click="nextMonth" class="p-2 hover:bg-slate-50 flex items-center justify-center">
                        <iconify-icon class="rotate-180" icon="lineicons:chevron-left" width="20" height="20"></iconify-icon>
                    </button>
                </div>
                <button
                @click="
                    selectedDate = '{{ now()->format('Y-m-d') }}';
                    $dispatchTo('manajemen.waktu.detail-jadwal', 'open-custom', { date: '{{ now()->format('Y-m-d') }}' });
                "
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs md:text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                    atur jadwal kustom
                </button>
                <button 
                    wire:click="goToday"
                    @click="
                        selectedDate = '{{ now()->format('Y-m-d') }}';
                        $dispatchTo('manajemen.waktu.detail-jadwal', 'select-date', { date: '{{ now()->format('Y-m-d') }}' });
                    "
                    class="px-4 py-2 border border-slate-200 rounded-lg text-xs md:text-sm font-medium hover:bg-slate-50 transition-colors"
                >
                    Hari Ini
                </button>
            </div>
        </header>
        <!-- END: Calendar Header -->
        <!-- BEGIN: Calendar Table -->
        <div class="w-full border-t border-l border-slate-100 rounded-sm overflow-hidden">
            <!-- Days of Week -->
            <div class="calendar-grid bg-white">
                <div class="p-3 text-sm font-bold text-center text-slate-700 border-r border-b border-slate-100">Sen</div>
                <div class="p-3 text-sm font-bold text-center text-slate-700 border-r border-b border-slate-100">Sel</div>
                <div class="p-3 text-sm font-bold text-center text-slate-700 border-r border-b border-slate-100">Rab</div>
                <div class="p-3 text-sm font-bold text-center text-slate-700 border-r border-b border-slate-100">Kam</div>
                <div class="p-3 text-sm font-bold text-center text-slate-700 border-r border-b border-slate-100">Jum</div>
                <div class="p-3 text-sm font-bold text-center text-slate-700 border-r border-b border-slate-100">Sab</div>
                <div class="p-3 text-sm font-bold text-center text-slate-700 border-r border-b border-slate-100">Min</div>
            </div>
            <div class="calendar-grid">

            @foreach ($calendar as $item)

                @if (!$item)

                    <div class="calendar-cell border-r border-b border-slate-100"></div>
                @else

                    @php
                        $j = $jadwal[$item['date']] ?? null;

                        $schedule = $j
                            ? [
                                'masuk' => $j['jam_masuk'],
                                'pulang' => $j['jam_pulang'],
                                'type' => $j['tipe']
                            ]
                            : ($defaultSchedule[$item['date']] ?? [
                                'masuk' => null,
                                'pulang' => null,
                                'type' => 'libur'
                            ]);
                    @endphp

                    <div
                        @click="selectedDate = '{{ $item['date'] }}'"
                        wire:click="$dispatchTo('manajemen.waktu.detail-jadwal', 'select-date', { date: '{{ $item['date'] }}' })"
                        class="calendar-cell p-2 flex flex-col items-center cursor-pointer rounded-xl transition"
                        :class="selectedDate === '{{ $item['date'] }}' ? 'ring-2 ring-blue-500 bg-blue-50' : ''"
                    >

                        {{-- TANGGAL --}}
                        <span class="
                            font-bold mb-1

                            @if(
                                $schedule['type'] == 'libur' ||
                                ($j && $j['tipe'] == 'libur')
                            )
                                text-calendar-red
                            @elseif (
                                $schedule['type'] == 'normal'
                            )
                                text-calendar-blue

                            @elseif ($schedule['type'] == 'custom')
                                text-calendar-yellow
                            @else
                                text-calendar-purple
                            @endif
                        ">
                            {{ $item['day'] }}
                        </span>

                        {{-- ========================= --}}
                        {{-- CUSTOM HOLIDAY --}}
                        {{-- ========================= --}}

                        @if ($j && $j['tipe'] == 'libur')

                            <span class="max-w-14 bg-red-100 text-red-500 text-[10px] px-2 py-0.5 rounded-full font-bold line-clamp-1">
                                {{ $j['nama_acara'] ?? 'Libur' }}
                            </span>

                        {{-- ========================= --}}
                        {{-- CUSTOM EVENT --}}
                        {{-- ========================= --}}

                        @elseif ($j && $j['tipe'] == 'custom')

                            <span class="max-w-14 bg-yellow-400 text-white text-[10px] px-2 py-0.5 rounded-full font-bold line-clamp-1">
                                {{ $j['nama_acara'] ?? 'Custom' }}
                            </span>

                        {{-- ========================= --}}
                        {{-- JUMAT --}}
                        {{-- ========================= --}}

                        @elseif ($schedule['type'] == 'khusus')

                            <span class="max-w-14 bg-purple-100 text-purple-500 text-[10px] px-2 py-0.5 rounded-full font-bold">
                                khusus
                            </span>

                        {{-- ========================= --}}
                        {{-- LIBUR DEFAULT --}}
                        {{-- ========================= --}}

                        @elseif ($schedule['type'] == 'libur')

                            <span class="bg-red-100 text-red-500 text-[10px] px-2 py-0.5 rounded-full font-bold">
                                Libur
                            </span>

                        @endif
                    </div>

                @endif

            @endforeach

        </div>
        </div>
        <!-- END: Calendar Table -->
        <!-- BEGIN: Legend -->
        <footer class="mt-8 flex flex-wrap gap-6 items-center text-sm font-medium text-slate-600">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-calendar-blue"></span>
                        Hari Normal
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-calendar-purple"></span>
                        Jadwal Khusus
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-calendar-yellow"></span>
                        Jadwal Kustom
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-calendar-red"></span>
                Libur
            </div>
        </footer>
    <!-- END: Legend -->
    </section>
    <!-- END: Left Section -->
    <!-- BEGIN: Right Sidebar - Detail Tanggal -->
    <livewire:manajemen.waktu.detail-jadwal :month="$month" :year="$year" />
</div>