<div class="flex flex-col lg:flex-row gap-6 w-full max-w-6xl" data-purpose="calendar-dashboard"" >
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
                <a href="{{ route('tambah-event') }}">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs md:text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                        tambah Event
                    </button>
                </a>
                <button class="px-4 py-2 border border-slate-200 rounded-lg text-xs md:text-sm font-medium hover:bg-slate-50 transition-colors">
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
                            $schedule = \App\Support\DateHelper::getDefaultSchedule(
                                $item['date']
                            );
                        @endphp

                        <div 
                            class="calendar-cell p-2 flex flex-col items-center"
                            wire:click="selectDate('{{ $item['date'] }}')"
                        >

                            <span class="
                                font-bold mb-1

                                @if($schedule['type'] == 'libur')
                                    text-calendar-red
                                @else
                                    text-slate-800
                                @endif
                            ">
                                {{ $item['day'] }}
                            </span>

                            @if ($schedule['type'] == 'jumat')

                                <span class="bg-purple-100 text-purple-500 text-[10px] px-2 py-0.5 rounded-full font-bold">
                                    Jumat
                                </span>

                            @endif

                            @if ($schedule['type'] == 'libur')

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
                        Jumat (Jadwal Khusus)
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-calendar-yellow"></span>
                        Event Khusus
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-300"></span>
                Libur
            </div>
        </footer>
    <!-- END: Legend -->
    </section>
    <!-- END: Left Section -->
    <!-- BEGIN: Right Sidebar - Detail Tanggal -->
    <aside class="w-full lg:w-80 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col" data-purpose="date-detail-sidebar">
        <div class="p-6 flex-grow">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Detail Tanggal</h2>
            <div class="mb-6">
                <div class="text-xl font-bold text-slate-900 mb-3">24 Mei 2024</div>
                <span class="inline-block bg-yellow-100 text-yellow-600 text-sm font-bold px-4 py-1.5 rounded-full">
                    Event Khusus
                </span>
            </div>
            <div class="space-y-6 border-t border-slate-100 pt-6">
                <!-- Event Name -->
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1">Acara</label>
                    <p class="text-slate-800 font-bold">Acara Class Meeting</p>
                </div>
                <!-- Schedule -->
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-3">Jadwal</label>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-700 font-medium">Masuk</span>
                            <span class="text-slate-900 font-bold">07:00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-700 font-medium">Pulang</span>
                            <span class="text-slate-900 font-bold">12:00</span>
                        </div>
                    </div>
                </div>
                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-1">Keterangan</label>
                    <p class="text-slate-600 leading-relaxed text-sm">
                        Pulang lebih awal karena acara sekolah.
                    </p>
                </div>
            </div>
        </div>
        <!-- Action Button -->
        <div class="p-6 border-t border-slate-100">
            <button class="w-full py-3 border border-blue-500 text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-colors shadow-sm">
                Edit Event
            </button>
        </div>
    </aside>
</div>