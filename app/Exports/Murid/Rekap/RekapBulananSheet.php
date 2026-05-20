<?php

namespace App\Exports\Murid\Rekap;

use App\Models\Setting;
use App\Services\Murid\Rekap\AbsenRekapQuery;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapBulananSheet implements FromCollection, WithTitle, WithCustomStartCell, WithEvents, ShouldAutoSize
{
    private AbsenRekapQuery $rekapQuery;
    private ?string $tanggalDari;
    private ?string $tanggalSampai;
    private ?int $rombelId;
    private ?string $kelasLabelCache = null;

    private array $dateHeaderRows = [];
    private array $tableHeaderRows = [];
    private array $dailySummaryTitleRows = [];
    private array $dailySummaryHeaderRows = [];
    private array $dailySummaryValueRows = [];
    private array $dateDataRanges = [];
    private int $monthlySummaryTitleRow = 0;
    private int $monthlySummaryHeaderRow = 0;
    private int $monthlySummaryValueRow = 0;
    private int $monthlySummaryExtraRow = 0;
    private int $startRow = 11;

    public function __construct(?string $tanggalDari, ?string $tanggalSampai, ?int $rombelId)
    {
        $this->rekapQuery = new AbsenRekapQuery($tanggalDari, $tanggalSampai, $rombelId);
        $this->tanggalDari = $tanggalDari;
        $this->tanggalSampai = $tanggalSampai;
        $this->rombelId = $rombelId;
    }

    public function startCell(): string
    {
        return 'A11';
    }

    public function collection(): LazyCollection
    {
        $this->resetTracking();
        $rows = $this->rekapQuery->base()
            ->select([
                'absen_murid.tanggal as tanggal',
                'murid.nama as nama',
                'murid.nipd as nipd',
                'absen_murid.status as status',
                'absen_murid.keterangan as keterangan',
                'absen_murid.waktu_masuk as waktu_masuk',
                'absen_murid.waktu_keluar as waktu_keluar',
            ])
            ->orderBy('absen_murid.tanggal')
            ->orderBy('murid.nama')
            ->cursor();

        $currentRow = $this->startRow;
        $currentDate = null;
        $currentDataStartRow = null;
        $dailyStats = $this->emptyDailyStats();
        $index = 1;

        return LazyCollection::make(function () use ($rows, &$currentRow, &$currentDate, &$currentDataStartRow, &$dailyStats, &$index) {
            foreach ($rows as $row) {
                $dateKey = $row->tanggal ? Carbon::parse($row->tanggal)->format('Y-m-d') : '-';

                if ($currentDate !== $dateKey) {
                    if ($currentDate !== null) {
                        if ($currentDataStartRow !== null && $currentRow - 1 >= $currentDataStartRow) {
                            $this->dateDataRanges[] = [$currentDataStartRow, $currentRow - 1];
                        }

                        foreach ($this->dailySummaryRows($dailyStats, $currentRow) as $summaryRow) {
                            yield $summaryRow;
                            $currentRow++;
                        }

                        yield $this->blankRow();
                        $currentRow++;
                    }

                    $currentDate = $dateKey;
                    $dailyStats = $this->emptyDailyStats();
                    $index = 1;

                    $this->dateHeaderRows[] = $currentRow;
                    yield [$this->dateLabel($row->tanggal), '', '', '', '', '', ''];
                    $currentRow++;

                    $this->tableHeaderRows[] = $currentRow;
                    yield $this->detailHeaderRow();
                    $currentRow++;
                    $currentDataStartRow = $currentRow;
                }

                $dailyStats = $this->accumulateDailyStats($dailyStats, $row);
                yield $this->detailRow($row, $index);
                $index++;
                $currentRow++;
            }

            if ($currentDate !== null) {
                if ($currentDataStartRow !== null && $currentRow - 1 >= $currentDataStartRow) {
                    $this->dateDataRanges[] = [$currentDataStartRow, $currentRow - 1];
                }

                foreach ($this->dailySummaryRows($dailyStats, $currentRow) as $summaryRow) {
                    yield $summaryRow;
                    $currentRow++;
                }

                yield $this->blankRow();
                $currentRow++;
            }

            foreach ($this->monthlySummaryRows($currentRow) as $summaryRow) {
                yield $summaryRow;
                $currentRow++;
            }
        });
    }

    public function title(): string
    {
        return 'Rekap Bulanan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'G';
                $highestRow = $sheet->getHighestRow();
                $titleColor = '0F172A';
                $sectionColor = 'E2E8F0';
                $headerColor = 'F1F5F9';
                $accentColor = 'DBEAFE';
                $sectionHeaderColor = 'BFDBFE';
                $sectionSummaryColor = 'E0F2FE';
                $zebraColors = ['FFFFFF', 'F8FAFC'];

                $summary = $this->summaryStats();

                $sheet->setCellValue('A1', 'Rekap Kehadiran Bulanan');
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A2', 'Sekolah: ' . $this->schoolName());
                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('E2', 'Kelas: ' . $this->kelasLabel());
                $sheet->mergeCells('E2:H2');
                $sheet->setCellValue('A3', 'Periode: ' . $this->periodeLabel());
                $sheet->mergeCells('A3:D3');
                $sheet->setCellValue('E3', 'Export: ' . now()->format('d-m-Y H:i'));
                $sheet->mergeCells('E3:H3');

                $sheet->setCellValue('A4', 'Ringkasan Bulanan');
                $sheet->mergeCells("A4:{$lastColumn}4");

                $sheet->mergeCells('A5:B6');
                $sheet->mergeCells('C5:D6');
                $sheet->mergeCells('E5:F6');
                $sheet->mergeCells('G5:G6');

                $izinSakit = $summary['izin'] + $summary['sakit'];

                $sheet->setCellValue('A5', "Total Absen\n{$summary['total_absen']}");
                $sheet->setCellValue('C5', "Hadir\n{$summary['hadir']}");
                $sheet->setCellValue('E5', "Izin/Sakit\n{$izinSakit}");
                $sheet->setCellValue('G5', "Alpa\n{$summary['alpa']}");

                $sheet->mergeCells('A7:C8');
                $sheet->mergeCells('D7:E8');
                $sheet->mergeCells('F7:G8');

                $sheet->setCellValue('A7', "Terlambat\n{$summary['terlambat']}");
                $sheet->setCellValue('D7', "Persentase Hadir\n{$summary['persen_hadir']}");
                $sheet->setCellValue('F7', "Total Hari\n{$summary['total_hari']}");

                $sheet->setCellValue('A9', 'Detail Per Tanggal');
                $sheet->mergeCells("A9:{$lastColumn}9");

                $titleStyle = [
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $titleColor]],
                ];

                $sectionStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $sectionColor]],
                ];

                $headerStyle = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $headerColor]],
                ];

                $summaryStyle = [
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $accentColor]],
                ];

                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                ];

                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray($titleStyle);
                $sheet->getStyle("A4:{$lastColumn}4")->applyFromArray($sectionStyle);
                $sheet->getStyle("A9:{$lastColumn}9")->applyFromArray($sectionStyle);
                $sheet->getStyle('A5:G8')->applyFromArray($summaryStyle);

                foreach ($this->dateHeaderRows as $row) {
                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $sectionHeaderColor]],
                    ]);
                }

                foreach ($this->tableHeaderRows as $row) {
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray($headerStyle);
                }

                foreach ($this->dailySummaryTitleRows as $row) {
                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $sectionSummaryColor]],
                    ]);
                }

                foreach ($this->dailySummaryHeaderRows as $row) {
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray($headerStyle);
                }

                foreach ($this->dailySummaryValueRows as $row) {
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                    ]);
                }

                foreach ($this->dateDataRanges as $range) {

                    [$startRow, $endRow] = $range;

                    if ($endRow < $startRow) {
                        continue;
                    }

                    // Zebra row
                    for ($rowIndex = $startRow; $rowIndex <= $endRow; $rowIndex++) {

                        $color = $zebraColors[$rowIndex % 2];

                        $sheet->getStyle("A{$rowIndex}:{$lastColumn}{$rowIndex}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB($color);
                    }

                    $conditionalStyles = [];

                    $statuses = [
                        'Hadir'     => ['BBF7D0', '166534'],
                        'Izin'      => ['BFDBFE', '1D4ED8'],
                        'Sakit'     => ['FDE68A', 'B45309'],
                        'Alpa'      => ['FCA5A5', '991B1B'],
                        'Terlambat' => ['C7D2FE', '3730A3'],
                    ];

                    foreach ($statuses as $text => [$bg, $font]) {

                        $conditional = new Conditional();

                        $conditional->setConditionType(Conditional::CONDITION_CELLIS);
                        $conditional->setOperatorType(Conditional::OPERATOR_EQUAL);
                        $conditional->addCondition('"' . $text . '"');

                        $conditional->getStyle()->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($bg);

                        $conditional->getStyle()->getFont()
                            ->getColor()->setRGB($font);

                        $conditionalStyles[] = $conditional;
                    }

                    // Lainnya
                    $lainnya = new Conditional();

                    $lainnya->setConditionType(Conditional::CONDITION_EXPRESSION);

                    $lainnya->addCondition(
                        '=AND(D1<>"Hadir",D1<>"Izin",D1<>"Sakit",D1<>"Alpa",D1<>"Terlambat",D1<>"-")'
                    );

                    $lainnya->getStyle()->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('E5E7EB');

                    $lainnya->getStyle()->getFont()
                        ->getColor()->setRGB('374151');

                    $conditionalStyles[] = $lainnya;

                    $statusRange = "D{$startRow}:D{$endRow}";

                    $sheet->getStyle($statusRange)
                        ->setConditionalStyles($conditionalStyles);
                }

                if ($this->monthlySummaryTitleRow > 0) {
                    $sheet->mergeCells("A{$this->monthlySummaryTitleRow}:{$lastColumn}{$this->monthlySummaryTitleRow}");
                    $sheet->getStyle("A{$this->monthlySummaryTitleRow}:{$lastColumn}{$this->monthlySummaryTitleRow}")
                        ->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                        ]);
                }

                if ($this->monthlySummaryHeaderRow > 0) {
                    $sheet->getStyle("A{$this->monthlySummaryHeaderRow}:{$lastColumn}{$this->monthlySummaryHeaderRow}")
                        ->applyFromArray($headerStyle);
                }

                if ($this->monthlySummaryValueRow > 0) {
                    $sheet->getStyle("A{$this->monthlySummaryValueRow}:{$lastColumn}{$this->monthlySummaryValueRow}")
                        ->applyFromArray(['font' => ['bold' => true]]);
                }

                if ($this->monthlySummaryExtraRow > 0) {
                    $sheet->mergeCells("A{$this->monthlySummaryExtraRow}:{$lastColumn}{$this->monthlySummaryExtraRow}");
                    $sheet->getStyle("A{$this->monthlySummaryExtraRow}:{$lastColumn}{$this->monthlySummaryExtraRow}")
                        ->applyFromArray([
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E7FF']],
                        ]);
                }

                $sheet->getStyle("A{$this->startRow}:{$lastColumn}{$highestRow}")
                    ->applyFromArray($borderStyle);

                $sheet->getStyle("A{$this->startRow}:{$lastColumn}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle("A{$this->startRow}:{$lastColumn}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle("A{$this->startRow}:A{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("C{$this->startRow}:{$lastColumn}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->freezePane('A11');
            },
        ];
    }

    private function detailHeaderRow(): array
    {
        return [
            'No',
            'Nama',
            'NIPD',
            'Status',
            'Keterangan',
            'Waktu Masuk',
            'Waktu Keluar',
        ];
    }

    private function detailRow($row, int $index): array
    {
        return [
            $index,
            $row->nama,
            $row->nipd ?: '-',
            $row->status,
            $row->keterangan ?: '-',
            $row->waktu_masuk ?: '-',
            $row->waktu_keluar ?: '-',
        ];
    }

    private function dailySummaryRows(array $stats, int $startRow): array
    {
        $this->dailySummaryTitleRows[] = $startRow;
        $this->dailySummaryHeaderRows[] = $startRow + 1;
        $this->dailySummaryValueRows[] = $startRow + 2;

        $total = $stats['total'];
        $hadir = $stats['hadir'];
        $izin = $stats['izin'];
        $sakit = $stats['sakit'];
        $alpa = $stats['alpa'];
        $terlambat = $stats['terlambat'];

        return [
            ['Ringkasan Tanggal', '', '', '', '', '', ''],
            [
                'Total',
                'Hadir',
                'Izin',
                'Sakit',
                'Alpa',
                'Terlambat',
                '% Hadir',
            ],
            [
                $total,
                $hadir,
                $izin,
                $sakit,
                $alpa,
                $terlambat,
                $this->formatPercent($hadir, $total),
            ],
        ];
    }

    private function monthlySummaryRows(int $startRow): array
    {
        $this->monthlySummaryTitleRow = $startRow;
        $this->monthlySummaryHeaderRow = $startRow + 1;
        $this->monthlySummaryValueRow = $startRow + 2;
        $this->monthlySummaryExtraRow = $startRow + 3;

        $summary = $this->summaryStats();

        return [
            ['Ringkasan Akhir Bulan', '', '', '', '', '', ''],
            ['Total Absen', 'Hadir', 'Izin', 'Sakit', 'Alpa', 'Terlambat', '% Hadir'],
            [
                $summary['total_absen'],
                $summary['hadir'],
                $summary['izin'],
                $summary['sakit'],
                $summary['alpa'],
                $summary['terlambat'],
                $summary['persen_hadir'],
            ],
            ["Total Hari: {$summary['total_hari']}", '', '', '', '', '', ''],
        ];
    }

    private function dateLabel(?string $date): string
    {
        if (!$date) {
            return 'Tanggal: -';
        }

        $formatted = Carbon::parse($date)->format('d-m-Y');
        $hari = $this->dayName($date);

        return "Tanggal: {$formatted} ({$hari})";
    }

    private function emptyDailyStats(): array
    {
        return [
            'total' => 0,
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'terlambat' => 0,
        ];
    }

    private function accumulateDailyStats(array $stats, $row): array
    {
        $stats['total']++;

        if ($row->status === 'Hadir') {
            $stats['hadir']++;
        } elseif ($row->status === 'Izin') {
            $stats['izin']++;
        } elseif ($row->status === 'Sakit') {
            $stats['sakit']++;
        } elseif ($row->status === 'Alpa') {
            $stats['alpa']++;
        } elseif ($row->status === 'Terlambat') {
            $stats['terlambat']++;
        }

        return $stats;
    }

    private function blankRow(): array
    {
        return ['', '', '', '', '', '', ''];
    }

    private function summaryStats(): array
    {
        $dateExpr = $this->rekapQuery->dateExpr();

        $row = $this->rekapQuery->base()
            ->selectRaw('COUNT(*) as total_absen')
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Hadir' THEN 1 ELSE 0 END) as hadir")
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Izin' THEN 1 ELSE 0 END) as izin")
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Sakit' THEN 1 ELSE 0 END) as sakit")
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Alpa' THEN 1 ELSE 0 END) as alpa")
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat")
            ->selectRaw("COUNT(DISTINCT {$dateExpr}) as total_hari")
            ->first();

        $totalAbsen = (int) ($row->total_absen ?? 0);
        $hadir = (int) ($row->hadir ?? 0);
        $izin = (int) ($row->izin ?? 0);
        $sakit = (int) ($row->sakit ?? 0);
        $alpa = (int) ($row->alpa ?? 0);
        $terlambat = (int) ($row->terlambat ?? 0);

        return [
            'total_absen' => $totalAbsen,
            'hadir' => $hadir,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpa' => $alpa,
            'terlambat' => $terlambat,
            'persen_hadir' => $this->formatPercent($hadir, $totalAbsen),
            'total_hari' => (int) ($row->total_hari ?? 0),
        ];
    }

    private function formatPercent(int $value, int $total): string
    {
        if ($total === 0) {
            return '0%';
        }

        return number_format(($value / $total) * 100, 2) . '%';
    }

    private function dayName(?string $date): string
    {
        if (!$date) {
            return '-';
        }

        $map = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $index = Carbon::parse($date)->dayOfWeekIso;

        return $map[$index] ?? '-';
    }

    private function schoolName(): string
    {
        return (string) (Setting::query()
            ->where('key', 'nama_sekolah')
            ->value('value')
            ?: config('app.name', 'Sekolah'));
    }

    private function periodeLabel(): string
    {
        if (!$this->tanggalDari && !$this->tanggalSampai) {
            return '-';
        }

        $start = $this->tanggalDari ?: $this->tanggalSampai;
        $end = $this->tanggalSampai ?: $this->tanggalDari;

        if (!$start || !$end) {
            return '-';
        }

        if ($start === $end) {
            return Carbon::parse($start)->format('d-m-Y');
        }

        return Carbon::parse($start)->format('d-m-Y') .
            ' - ' .
            Carbon::parse($end)->format('d-m-Y');
    }

    private function resetTracking(): void
    {
        $this->dateHeaderRows = [];
        $this->tableHeaderRows = [];
        $this->dailySummaryTitleRows = [];
        $this->dailySummaryHeaderRows = [];
        $this->dailySummaryValueRows = [];
        $this->dateDataRanges = [];
        $this->monthlySummaryTitleRow = 0;
        $this->monthlySummaryHeaderRow = 0;
        $this->monthlySummaryValueRow = 0;
        $this->monthlySummaryExtraRow = 0;
    }

    private function kelasLabel(): string
    {
        if ($this->kelasLabelCache !== null) {
            return $this->kelasLabelCache;
        }

        if (!$this->rombelId) {
            $this->kelasLabelCache = '-';
            return $this->kelasLabelCache;
        }

        $nameExpr = $this->rekapQuery->rombelNamaExpr();

        $label = DB::table('rombel')
            ->leftJoin('tingkat', 'tingkat.id', '=', 'rombel.tingkat_id')
            ->leftJoin('jurusan', 'jurusan.id', '=', 'rombel.jurusan_id')
            ->leftJoin('indeks', 'indeks.id', '=', 'rombel.indeks_id')
            ->where('rombel.id', $this->rombelId)
            ->selectRaw("{$nameExpr} as nama")
            ->value('nama');

        $this->kelasLabelCache = trim((string) $label) ?: 'Tanpa Kelas';

        return $this->kelasLabelCache;
    }
}
