<?php

namespace Modules\Manajemen\Livewire\Waktu;

use App\Helpers\ToastMagic;
use App\Models\JadwalAbsen;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Manajemen extends Component
{
    private const DEFAULT_SCAN_MASUK_MULAI = '06:30';
    private const DEFAULT_SCAN_MASUK_SAMPAI = '07:30';
    private const DEFAULT_SCAN_PULANG_MULAI = '15:30';
    private const DEFAULT_SCAN_PULANG_SAMPAI = '16:30';
    private const DEFAULT_SCAN_PULANG_JUMAT_MULAI = '11:30';
    private const DEFAULT_SCAN_PULANG_JUMAT_SAMPAI = '13:00';

    private const TIPE_OPTIONS = [
        'normal' => 'Masuk Normal',
        'pulang_cepat' => 'Pulang Cepat',
        'pjj' => 'PJJ',
        'libur' => 'Libur',
        'khusus' => 'Hari Spesial',
    ];

    public int $month;
    public int $year;
    public array $state = [];

    public function mount(): void
    {
        $today = now();

        $this->month = (int) $today->format('m');
        $this->year = (int) $today->format('Y');
        $this->state = $this->emptyState($today->format('Y-m-d'));
    }

    public function loadInitialState(): array
    {
        $selectedDate = $this->state['selectedDate'] ?? now()->format('Y-m-d');

        $this->refreshState($selectedDate);
        $this->dispatch('waktu-state-updated', state: $this->state);

        return $this->state;
    }

    public function changeMonth(int $year, int $month): array
    {
        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
            return $this->state;
        }

        $this->year = $year;
        $this->month = $month;
        $this->refreshState(sprintf('%04d-%02d-01', $year, $month));
        $this->dispatch('waktu-state-updated', state: $this->state);

        return $this->state;
    }

    public function goToday(): array
    {
        $today = now();

        $this->year = (int) $today->format('Y');
        $this->month = (int) $today->format('m');
        $this->refreshState($today->format('Y-m-d'));
        $this->dispatch('waktu-state-updated', state: $this->state);

        return $this->state;
    }

    public function saveDefault(array $payload): array
    {
        $payload = $this->normalizeTimeFields($payload, [
            'scan_masuk_mulai',
            'scan_masuk_sampai',
            'scan_keluar_mulai',
            'scan_keluar_sampai',
            'scan_keluar_jumat_mulai',
            'scan_keluar_jumat_sampai',
        ]);

        $validated = validator($payload, [
            'scan_masuk_mulai' => ['required', 'date_format:H:i'],
            'scan_masuk_sampai' => ['required', 'date_format:H:i'],
            'scan_keluar_mulai' => ['required', 'date_format:H:i'],
            'scan_keluar_sampai' => ['required', 'date_format:H:i'],
            'scan_keluar_jumat_mulai' => ['required', 'date_format:H:i'],
            'scan_keluar_jumat_sampai' => ['required', 'date_format:H:i'],
        ], [
            'scan_masuk_mulai.required' => 'Jam mulai scan masuk wajib diisi.',
            'scan_masuk_sampai.required' => 'Jam akhir scan masuk wajib diisi.',
            'scan_keluar_mulai.required' => 'Jam mulai scan pulang wajib diisi.',
            'scan_keluar_sampai.required' => 'Jam akhir scan pulang wajib diisi.',
            'scan_keluar_jumat_mulai.required' => 'Jam mulai scan pulang Jumat wajib diisi.',
            'scan_keluar_jumat_sampai.required' => 'Jam akhir scan pulang Jumat wajib diisi.',
        ])->validate();

        $now = now();
        Setting::upsert([
            ['key' => 'jadwal.scan_masuk_mulai', 'value' => $validated['scan_masuk_mulai'], 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'jadwal.scan_masuk_sampai', 'value' => $validated['scan_masuk_sampai'], 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'jadwal.scan_keluar_mulai', 'value' => $validated['scan_keluar_mulai'], 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'jadwal.scan_keluar_sampai', 'value' => $validated['scan_keluar_sampai'], 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'jadwal.scan_keluar_jumat_mulai', 'value' => $validated['scan_keluar_jumat_mulai'], 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'jadwal.scan_keluar_jumat_sampai', 'value' => $validated['scan_keluar_jumat_sampai'], 'created_at' => $now, 'updated_at' => $now],
        ], ['key'], ['value', 'updated_at']);

        $this->refreshState($this->state['selectedDate'] ?? now()->format('Y-m-d'));
        $this->dispatch('waktu-state-updated', state: $this->state);

        ToastMagic::success('Jendela Scan Disimpan', 'Jendela scan masuk dan pulang berhasil diperbarui.');

        return $this->state;
    }

    public function saveEvent(array $payload): array
    {
        foreach (($payload['detail_kelas'] ?? []) as $rombelId => $row) {
            $payload['detail_kelas'][$rombelId] = $this->normalizeTimeFields($row, [
                'scan_masuk_mulai',
                'scan_masuk_sampai',
                'scan_keluar_mulai',
                'scan_keluar_sampai',
            ], true);
        }

        $validated = validator($payload, [
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['required', 'date_format:Y-m-d'],
            'nama_acara' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'selected_rombel_ids' => ['required', 'array', 'min:1'],
            'selected_rombel_ids.*' => ['integer', 'exists:rombel,id'],
            'detail_kelas' => ['required', 'array'],
            'detail_kelas.*.tipe' => ['required', 'in:normal,pulang_cepat,pjj,libur,khusus'],
            'detail_kelas.*.gunakan_window_scan' => ['nullable', 'boolean'],
            'detail_kelas.*.scan_masuk_mulai' => ['nullable', 'date_format:H:i'],
            'detail_kelas.*.scan_masuk_sampai' => ['nullable', 'date_format:H:i'],
            'detail_kelas.*.scan_keluar_mulai' => ['nullable', 'date_format:H:i'],
            'detail_kelas.*.scan_keluar_sampai' => ['nullable', 'date_format:H:i'],
            'detail_kelas.*.keterangan' => ['nullable', 'string'],
        ], [
            'nama_acara.required' => 'Nama event wajib diisi.',
            'selected_rombel_ids.required' => 'Minimal pilih satu kelas terdampak.',
            'selected_rombel_ids.min' => 'Minimal pilih satu kelas terdampak.',
        ])->validate();

        if ($validated['tanggal_selesai'] < $validated['tanggal_mulai']) {
            throw ValidationException::withMessages([
                'tanggal_selesai' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            ]);
        }

        foreach (($validated['detail_kelas'] ?? []) as $rombelId => $row) {
            $tipe = $row['tipe'] ?? 'libur';
            $gunakanWindowScan = filter_var($row['gunakan_window_scan'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if ($tipe !== 'libur' && $gunakanWindowScan) {
                foreach ([
                    'scan_masuk_mulai' => 'Scan masuk mulai',
                    'scan_masuk_sampai' => 'Scan masuk sampai',
                    'scan_keluar_mulai' => 'Scan pulang mulai',
                    'scan_keluar_sampai' => 'Scan pulang sampai',
                ] as $field => $label) {
                    if (blank($row[$field] ?? null)) {
                        throw ValidationException::withMessages([
                            "detail_kelas.{$rombelId}.{$field}" => "{$label} wajib diisi jika window scan kelas diaktifkan.",
                        ]);
                    }
                }
            }
        }

        $rombelIds = collect($validated['selected_rombel_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $detailKelas = $validated['detail_kelas'];
        $tanggalMulai = $validated['tanggal_mulai'];
        $tanggalSelesai = $validated['tanggal_selesai'];

        DB::transaction(function () use ($validated, $rombelIds, $detailKelas, $tanggalMulai, $tanggalSelesai) {
            $dates = $this->dateRange($tanggalMulai, $tanggalSelesai);
            $now = now();

            $jadwalRows = collect($dates)
                ->map(fn (string $tanggal) => [
                    'tanggal' => $tanggal,
                    'nama_acara' => $validated['nama_acara'],
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'tipe' => 'custom',
                    'keterangan' => $validated['keterangan'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all();

            JadwalAbsen::upsert($jadwalRows, ['tanggal'], [
                'nama_acara',
                'jam_masuk',
                'jam_pulang',
                'tipe',
                'keterangan',
                'updated_at',
            ]);

            $jadwalIdsByDate = JadwalAbsen::whereIn('tanggal', $dates)
                ->pluck('id', 'tanggal')
                ->all();

            $supportsScanWindow = $this->supportsRombelScanWindow();
            $detailRows = [];

            foreach ($dates as $tanggal) {
                $jadwalId = $jadwalIdsByDate[$tanggal] ?? null;

                if (!$jadwalId) {
                    continue;
                }

                foreach ($rombelIds as $rombelId) {
                    $key = (string) $rombelId;
                    $row = $detailKelas[$key] ?? [];
                    $tipe = $row['tipe'] ?? 'libur';

                    $detail = [
                        'jadwal_absen_id' => $jadwalId,
                        'rombel_id' => $rombelId,
                        'tipe' => $tipe,
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'keterangan' => $row['keterangan'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if ($supportsScanWindow) {
                        $gunakanWindowScan = $tipe !== 'libur'
                            && filter_var($row['gunakan_window_scan'] ?? false, FILTER_VALIDATE_BOOLEAN);

                        $detail = array_merge($detail, [
                            'gunakan_window_scan' => $gunakanWindowScan,
                            'scan_masuk_mulai' => $gunakanWindowScan ? ($row['scan_masuk_mulai'] ?? null) : null,
                            'scan_masuk_sampai' => $gunakanWindowScan ? ($row['scan_masuk_sampai'] ?? null) : null,
                            'scan_keluar_mulai' => $gunakanWindowScan ? ($row['scan_keluar_mulai'] ?? null) : null,
                            'scan_keluar_sampai' => $gunakanWindowScan ? ($row['scan_keluar_sampai'] ?? null) : null,
                        ]);
                    }

                    $detailRows[] = $detail;
                }
            }

            if ($detailRows) {
                $updateColumns = ['tipe', 'jam_masuk', 'jam_pulang', 'keterangan', 'updated_at'];

                if ($supportsScanWindow) {
                    $updateColumns = array_merge($updateColumns, [
                        'gunakan_window_scan',
                        'scan_masuk_mulai',
                        'scan_masuk_sampai',
                        'scan_keluar_mulai',
                        'scan_keluar_sampai',
                    ]);
                }

                DB::table('jadwal_absen_rombel')->upsert(
                    $detailRows,
                    ['jadwal_absen_id', 'rombel_id'],
                    $updateColumns,
                );
            }

            DB::table('jadwal_absen_rombel')
                ->whereIn('jadwal_absen_id', array_values($jadwalIdsByDate))
                ->whereNotIn('rombel_id', $rombelIds)
                ->delete();
        });

        $this->refreshState($tanggalMulai);
        $this->dispatch('waktu-state-updated', state: $this->state);

        ToastMagic::success('Event Kelas Disimpan', 'Jadwal untuk kelas terdampak berhasil diperbarui.');

        return $this->state;
    }

    public function deleteDate(string $tanggal): array
    {
        if (!$this->isValidDate($tanggal)) {
            return $this->state;
        }

        JadwalAbsen::where('tanggal', $tanggal)->delete();

        $this->refreshState($tanggal);
        $this->dispatch('waktu-state-updated', state: $this->state);

        ToastMagic::success('Event Dihapus', 'Event pada tanggal tersebut berhasil dihapus.');

        return $this->state;
    }

    public function loadDateDetails(string $tanggal): array
    {
        if (!$this->isValidDate($tanggal)) {
            return [
                'selectedDate' => $this->state['selectedDate'] ?? null,
                'event' => null,
            ];
        }

        $this->state['selectedDate'] = $tanggal;

        $event = JadwalAbsen::with(['rombelJadwal.rombel.tingkat', 'rombelJadwal.rombel.jurusan', 'rombelJadwal.rombel.indeks'])
            ->whereDate('tanggal', $tanggal)
            ->first();

        return [
            'selectedDate' => $tanggal,
            'event' => $event ? $this->formatEvent($event, true) : null,
        ];
    }

    private function refreshState(?string $selectedDate = null): void
    {
        $settings = $this->defaultSettings();
        $eventsByDate = $this->loadEventsByDate();
        $selectedDate = $this->normalizeSelectedDate($selectedDate);

        $this->state = [
            'year' => $this->year,
            'month' => $this->month,
            'monthLabel' => $this->monthLabel($this->year, $this->month),
            'today' => now()->format('Y-m-d'),
            'selectedDate' => $selectedDate,
            'settings' => $settings,
            'tipeOptions' => self::TIPE_OPTIONS,
            'rombel' => $this->loadRombel(),
            'eventsByDate' => $eventsByDate,
            'days' => $this->buildCalendarDays($eventsByDate, $settings),
            'ready' => true,
        ];
    }

    private function emptyState(?string $selectedDate = null): array
    {
        $settings = $this->fallbackSettings();
        $selectedDate = $this->normalizeSelectedDate($selectedDate);

        return [
            'year' => $this->year,
            'month' => $this->month,
            'monthLabel' => $this->monthLabel($this->year, $this->month),
            'today' => now()->format('Y-m-d'),
            'selectedDate' => $selectedDate,
            'settings' => $settings,
            'tipeOptions' => self::TIPE_OPTIONS,
            'rombel' => [],
            'eventsByDate' => [],
            'days' => $this->buildCalendarDays([], $settings),
            'ready' => false,
        ];
    }

    private function fallbackSettings(): array
    {
        return [
            'scan_masuk_mulai' => config('waktu-absensi.scan.masuk.mulai'),
            'scan_masuk_sampai' => config('waktu-absensi.scan.masuk.sampai'),
            'scan_keluar_mulai' => config('waktu-absensi.scan.pulang.mulai'),
            'scan_keluar_sampai' => config('waktu-absensi.scan.pulang.sampai'),
            'scan_keluar_jumat_mulai' => config('waktu-absensi.scan.jumat.pulang.mulai'),
            'scan_keluar_jumat_sampai' => config('waktu-absensi.scan.jumat.pulang.sampai'),
        ];
    }

    private function defaultSettings(): array
    {
        $settings = Setting::whereIn('key', [
            'jadwal.scan_masuk_mulai',
            'jadwal.scan_masuk_sampai',
            'jadwal.scan_keluar_mulai',
            'jadwal.scan_keluar_sampai',
            'jadwal.scan_keluar_jumat_mulai',
            'jadwal.scan_keluar_jumat_sampai',
        ])->pluck('value', 'key');

        return [
            'scan_masuk_mulai' => $this->formatTime($settings['jadwal.scan_masuk_mulai'] ?? config('waktu-absensi.scan.masuk.mulai')),
            'scan_masuk_sampai' => $this->formatTime($settings['jadwal.scan_masuk_sampai'] ?? config('waktu-absensi.scan.masuk.sampai')),
            'scan_keluar_mulai' => $this->formatTime($settings['jadwal.scan_keluar_mulai'] ?? config('waktu-absensi.scan.pulang.mulai')),
            'scan_keluar_sampai' => $this->formatTime($settings['jadwal.scan_keluar_sampai'] ?? config('waktu-absensi.scan.pulang.sampai')),
            'scan_keluar_jumat_mulai' => $this->formatTime($settings['jadwal.scan_keluar_jumat_mulai'] ?? config('waktu-absensi.scan.jumat.pulang.mulai')),
            'scan_keluar_jumat_sampai' => $this->formatTime($settings['jadwal.scan_keluar_jumat_sampai'] ?? config('waktu-absensi.scan.jumat.pulang.sampai')),
        ];
    }

    private function loadRombel(): array
    {
        return Rombel::with(['tingkat:id,nama', 'jurusan:id,nama', 'indeks:id,nama'])
            ->orderBy('tingkat_id')
            ->orderBy('jurusan_id')
            ->orderBy('indeks_id')
            ->get()
            ->map(fn (Rombel $rombel) => [
                'id' => $rombel->id,
                'nama' => $rombel->nama_lengkap,
                'tingkat' => $rombel->tingkat?->nama,
                'jurusan' => $rombel->jurusan?->nama,
                'indeks' => $rombel->indeks?->nama,
            ])
            ->values()
            ->all();
    }

    private function loadEventsByDate(): array
    {
        if (!Schema::hasTable('jadwal_absen') || !Schema::hasTable('jadwal_absen_rombel')) {
            return [];
        }

        $start = Carbon::create($this->year, $this->month, 1)->format('Y-m-d');
        $end = Carbon::create($this->year, $this->month, 1)->endOfMonth()->format('Y-m-d');

        return JadwalAbsen::with(['rombelJadwal.rombel.tingkat', 'rombelJadwal.rombel.jurusan', 'rombelJadwal.rombel.indeks'])
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->get()
            ->mapWithKeys(fn (JadwalAbsen $jadwal) => [
                $jadwal->tanggal->format('Y-m-d') => $this->formatEvent($jadwal, false),
            ])
            ->all();
    }

    private function formatEvent(JadwalAbsen $jadwal, bool $includeDetails = true): array
    {
        $details = $jadwal->rombelJadwal
            ->map(fn ($detail) => [
                'rombel_id' => $detail->rombel_id,
                'rombel' => $detail->rombel?->nama_lengkap ?? '-',
                'tipe' => $detail->tipe,
                'tipe_label' => self::TIPE_OPTIONS[$detail->tipe] ?? ucfirst((string) $detail->tipe),
                'jam_masuk' => $this->formatTime($detail->jam_masuk),
                'jam_pulang' => $this->formatTime($detail->jam_pulang),
                'gunakan_window_scan' => (bool) ($detail->gunakan_window_scan ?? false),
                'scan_masuk_mulai' => $this->formatTime($detail->scan_masuk_mulai ?? null),
                'scan_masuk_sampai' => $this->formatTime($detail->scan_masuk_sampai ?? null),
                'scan_keluar_mulai' => $this->formatTime($detail->scan_keluar_mulai ?? null),
                'scan_keluar_sampai' => $this->formatTime($detail->scan_keluar_sampai ?? null),
                'keterangan' => $detail->keterangan,
            ])
            ->values()
            ->all();

        $summary = collect($details)
            ->countBy('tipe_label')
            ->map(fn ($total, $label) => $label . ': ' . $total)
            ->values()
            ->implode(', ');

        return [
            'id' => $jadwal->id,
            'tanggal' => $jadwal->tanggal->format('Y-m-d'),
            'nama_acara' => $jadwal->nama_acara,
            'tipe' => $jadwal->tipe,
            'keterangan' => $jadwal->keterangan,
            'affected_count' => count($details),
            'summary' => $summary ?: 'Tidak ada kelas terdampak',
            'details' => $includeDetails ? $details : [],
            'details_loaded' => $includeDetails,
        ];
    }

    private function buildCalendarDays(array $eventsByDate, array $settings): array
    {
        $firstDate = Carbon::create($this->year, $this->month, 1);
        $daysInMonth = $firstDate->daysInMonth;
        $startOffset = $firstDate->dayOfWeekIso;
        $today = now()->format('Y-m-d');
        $days = [];
        $day = 1;

        for ($cell = 1; $cell <= 42; $cell++) {
            if ($cell < $startOffset || $day > $daysInMonth) {
                $days[] = null;
                continue;
            }

            $date = sprintf('%04d-%02d-%02d', $this->year, $this->month, $day);
            $default = $this->defaultForDate($date, $settings);
            $event = $eventsByDate[$date] ?? null;

            $days[] = [
                'date' => $date,
                'day' => $day,
                'iso' => $this->dayIso($date),
                'isToday' => $date === $today,
                'default' => $default,
                'hasEvent' => filled($event),
                'affectedCount' => $event['affected_count'] ?? 0,
                'title' => $event['nama_acara'] ?? $default['label'],
                'summary' => $event['summary'] ?? $default['keterangan'],
            ];

            $day++;
        }

        return $days;
    }

    private function defaultForDate(string $date, array $settings): array
    {
        $iso = $this->dayIso($date);

        if ($iso === 5) {
            return [
                'tipe' => 'khusus',
                'label' => 'Default Jumat',
                'jam_masuk' => null,
                'jam_pulang' => null,
                'scan_masuk_mulai' => $settings['scan_masuk_mulai'],
                'scan_masuk_sampai' => $settings['scan_masuk_sampai'],
                'scan_keluar_mulai' => $settings['scan_keluar_jumat_mulai'],
                'scan_keluar_sampai' => $settings['scan_keluar_jumat_sampai'],
                'keterangan' => 'Jadwal Jumat otomatis.',
            ];
        }

        if ($iso >= 6) {
            return [
                'tipe' => 'libur',
                'label' => 'Libur Default',
                'jam_masuk' => null,
                'jam_pulang' => null,
                'scan_masuk_mulai' => null,
                'scan_masuk_sampai' => null,
                'scan_keluar_mulai' => null,
                'scan_keluar_sampai' => null,
                'keterangan' => 'Sabtu/Minggu libur default, masih bisa dioverride.',
            ];
        }

        return [
            'tipe' => 'normal',
            'label' => 'Normal',
            'jam_masuk' => null,
            'jam_pulang' => null,
            'scan_masuk_mulai' => $settings['scan_masuk_mulai'],
            'scan_masuk_sampai' => $settings['scan_masuk_sampai'],
            'scan_keluar_mulai' => $settings['scan_keluar_mulai'],
            'scan_keluar_sampai' => $settings['scan_keluar_sampai'],
            'keterangan' => 'Jadwal normal.',
        ];
    }

    private function supportsRombelScanWindow(): bool
    {
        static $supported = null;

        if ($supported !== null) {
            return $supported;
        }

        if (!Schema::hasTable('jadwal_absen_rombel')) {
            return $supported = false;
        }

        foreach ([
            'gunakan_window_scan',
            'scan_masuk_mulai',
            'scan_masuk_sampai',
            'scan_keluar_mulai',
            'scan_keluar_sampai',
        ] as $column) {
            if (!Schema::hasColumn('jadwal_absen_rombel', $column)) {
                return $supported = false;
            }
        }

        return $supported = true;
    }

    private function dateRange(string $start, string $end): array
    {
        $dates = [];
        $current = Carbon::createFromFormat('Y-m-d', $start)->startOfDay();
        $last = Carbon::createFromFormat('Y-m-d', $end)->startOfDay();

        while ($current <= $last) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return $dates;
    }

    private function normalizeSelectedDate(?string $selectedDate): string
    {
        if ($selectedDate && $this->isValidDate($selectedDate)) {
            return $selectedDate;
        }

        return sprintf('%04d-%02d-01', $this->year, $this->month);
    }

    private function isValidDate(string $date): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        [$year, $month, $day] = array_map('intval', explode('-', $date));

        return checkdate($month, $day, $year);
    }

    private function dayIso(string $date): int
    {
        [$year, $month, $day] = array_map('intval', explode('-', $date));

        return (int) Carbon::create($year, $month, $day)->dayOfWeekIso;
    }

    private function monthLabel(int $year, int $month): string
    {
        return Carbon::create($year, $month, 1)->translatedFormat('F Y');
    }

    private function formatTime($time): ?string
    {
        if (!$time) {
            return null;
        }

        return substr((string) $time, 0, 5);
    }

    private function normalizeTimeFields(array $payload, array $fields, bool $emptyAsNull = false): array
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $payload)) {
                continue;
            }

            if ($payload[$field] === '' || $payload[$field] === null) {
                $payload[$field] = $emptyAsNull ? null : $payload[$field];
                continue;
            }

            $payload[$field] = $this->formatTime($payload[$field]);
        }

        return $payload;
    }

    public function render()
    {
        return view('manajemen::livewire.waktu.manajemen');
    }
}
