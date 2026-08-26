<?php

namespace App\Exports\Murid\Rekap;

use App\Enums\AttendanceStatus;
use App\Models\Setting;
use App\Services\Murid\Rekap\AbsenRekapQuery;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class RekapSemuaKelasSheet implements FromQuery, WithChunkReading, WithColumnWidths, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithTitle
{
    private AbsenRekapQuery $rekapQuery;

    private ?string $tanggalDari;

    private ?string $tanggalSampai;

    public function __construct(?string $tanggalDari, ?string $tanggalSampai)
    {
        $this->rekapQuery = new AbsenRekapQuery($tanggalDari, $tanggalSampai, null);
        $this->tanggalDari = $tanggalDari;
        $this->tanggalSampai = $tanggalSampai;
    }

    public function title(): string
    {
        return 'Rekap Kehadiran';
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        return [
            'No',
            'Kelas',
            AttendanceStatus::Hadir->value,
            AttendanceStatus::Izin->value,
            AttendanceStatus::Alpa->value,
            'Tidak absen pulang',
            'Persentase Kehadiran',
        ];
    }

    public function query(): Builder
    {
        $rombelNamaExpr = $this->rekapQuery->rombelNamaExpr();
        [$startDate, $endDate] = $this->dateRange();

        return DB::table('rombel')
            ->leftJoin('tingkat', 'tingkat.id', '=', 'rombel.tingkat_id')
            ->leftJoin('jurusan', 'jurusan.id', '=', 'rombel.jurusan_id')
            ->leftJoin('indeks', 'indeks.id', '=', 'rombel.indeks_id')
            ->leftJoin('murid', 'murid.rombel_id', '=', 'rombel.id')
            ->leftJoin('absen_murid', function ($join) use ($startDate, $endDate) {
                $join->on('absen_murid.murid_id', '=', 'murid.id');

                if ($startDate && $endDate) {
                    $join->whereBetween('absen_murid.tanggal', [$startDate, $endDate]);
                }
            })
            ->select('rombel.id')
            ->selectRaw("{$rombelNamaExpr} as kelas")
            ->selectRaw('SUM(CASE WHEN absen_murid.status = ? THEN 1 ELSE 0 END) as hadir', [AttendanceStatus::Hadir->value])
            ->selectRaw('SUM(CASE WHEN absen_murid.status IN (?, ?, ?) THEN 1 ELSE 0 END) as izin', [
                AttendanceStatus::Izin->value,
                AttendanceStatus::Sakit->value,
                AttendanceStatus::Selesai->value,
            ])
            ->selectRaw('SUM(CASE WHEN absen_murid.status = ? THEN 1 ELSE 0 END) as alpa', [AttendanceStatus::Alpa->value])
            ->selectRaw("SUM(CASE WHEN LOWER(absen_murid.status) IN ('masuk','terlambat') THEN 1 ELSE 0 END) as tidak_absen_pulang")
            ->selectRaw('COUNT(absen_murid.id) as total')
            ->groupBy('rombel.id', DB::raw($rombelNamaExpr))
            ->orderBy(DB::raw($rombelNamaExpr));
    }

    public function map($row): array
    {
        $hadir = (int) $row->hadir;
        $izin = (int) $row->izin;
        $alpa = (int) $row->alpa;
        $tidakAbsenPulang = (int) ($row->tidak_absen_pulang ?? 0);
        $total = (int) $row->total;

        return [
            '',
            trim((string) ($row->kelas ?? '')) ?: 'Tanpa Kelas',
            $this->displayValue($hadir, $total),
            $this->displayValue($izin, $total),
            $this->displayValue($alpa, $total),
            $this->displayValue($tidakAbsenPulang, $total),
            $this->formatAttendanceRateOrDash($hadir, $izin, $alpa),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'G';
                $headerRow = 8;
                $highestRow = $sheet->getHighestRow();
                $dataStartRow = $headerRow + 1;
                $no = 1;

                $summary = $this->summaryStats();
                for ($row = $dataStartRow; $row <= $highestRow; $row++) {
                    $sheet->setCellValue("A{$row}", $no++);
                }
                $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI ABSENSI KELAS');
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A2', strtoupper($this->schoolName()));
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A4', 'Kelas');
                $sheet->setCellValue('B4', ': Semua Kelas');
                $sheet->mergeCells('B4:C4');
                $sheet->setCellValue('D4', 'Periode');
                $sheet->setCellValue('E4', ': '.$this->periodeLabel());
                $sheet->setCellValue('A5', 'Tanggal Cetak');
                $sheet->setCellValue('B5', ': '.now()->format('d/m/Y'));
                $sheet->mergeCells('B5:C5');

                $sheet->getStyle("A1:{$lastColumn}2")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                ]);

                $totalRow = $highestRow + 1;
                $sheet->setCellValue("A{$totalRow}", 'TOTAL');
                $sheet->setCellValue("C{$totalRow}", $summary['hadir']);
                $sheet->setCellValue("D{$totalRow}", $summary['izin']);
                $sheet->setCellValue("E{$totalRow}", $summary['alpa']);
                $sheet->setCellValue("F{$totalRow}", $summary['tidak_absen_pulang']);
                $sheet->setCellValue("G{$totalRow}", $summary['persen_kehadiran']);

                $sheet->getStyle("A{$totalRow}:{$lastColumn}{$totalRow}")
                    ->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                    ]);

                $rekapRow = $totalRow + 1;
                $sheet->setCellValue(
                    "A{$rekapRow}",
                    'Rekap: Hadir '.$summary['persen_hadir'].' | Izin '.$summary['persen_izin'].' | Alpa '.$summary['persen_alpa']
                );
                $sheet->mergeCells("A{$rekapRow}:{$lastColumn}{$rekapRow}");

                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$rekapRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                $sheet->getStyle("A{$rekapRow}:{$lastColumn}{$rekapRow}")
                    ->getFont()
                    ->setBold(true);

                $ttdStartRow = $rekapRow + 2;
                $ttdNameRow = $ttdStartRow + 4;
                $ttdRoleRow = $ttdNameRow + 1;

                $sheet->setCellValue("E{$ttdStartRow}", 'Mengetahui,');
                $sheet->mergeCells("E{$ttdStartRow}:F{$ttdStartRow}");
                $sheet->setCellValue("E{$ttdNameRow}", '-');
                $sheet->mergeCells("E{$ttdNameRow}:F{$ttdNameRow}");
                $sheet->setCellValue("E{$ttdRoleRow}", 'Wali Kelas');
                $sheet->mergeCells("E{$ttdRoleRow}:F{$ttdRoleRow}");

                $sheet->getStyle("E{$ttdStartRow}:F{$ttdRoleRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$ttdStartRow}:F{$ttdRoleRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("E{$ttdStartRow}:F{$ttdRoleRow}")
                    ->getFont()
                    ->setSize(12);
                $sheet->getStyle("E{$ttdNameRow}:F{$ttdNameRow}")
                    ->getFont()
                    ->setBold(true);

                $sheet->getStyle("A{$headerRow}:A{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$headerRow}:B{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("C{$headerRow}:{$lastColumn}{$totalRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->freezePane('A9');

                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.35);
                $sheet->getPageMargins()->setRight(0.3);
                $sheet->getPageMargins()->setLeft(0.3);
                $sheet->getPageMargins()->setBottom(0.35);
                $sheet->getPageMargins()->setHeader(0.2);
                $sheet->getPageMargins()->setFooter(0.2);
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->getPageSetup()->setVerticalCentered(false);
                $sheet->getPageSetup()->setPrintArea("A1:{$lastColumn}{$ttdRoleRow}");
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 33,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 20,
            'G' => 30,
        ];
    }

    private function summaryStats(): array
    {
        [$startDate, $endDate] = $this->dateRange();
        $row = DB::table('rombel')
            ->leftJoin('murid', 'murid.rombel_id', '=', 'rombel.id')
            ->leftJoin('absen_murid', function ($join) use ($startDate, $endDate) {
                $join->on('absen_murid.murid_id', '=', 'murid.id');

                if ($startDate && $endDate) {
                    $join->whereBetween('absen_murid.tanggal', [$startDate, $endDate]);
                }
            })
            ->selectRaw('SUM(CASE WHEN absen_murid.status = ? THEN 1 ELSE 0 END) as hadir', [AttendanceStatus::Hadir->value])
            ->selectRaw('SUM(CASE WHEN absen_murid.status IN (?, ?) THEN 1 ELSE 0 END) as izin', [
                AttendanceStatus::Izin->value,
                AttendanceStatus::Sakit->value,
            ])
            ->selectRaw('SUM(CASE WHEN absen_murid.status = ? THEN 1 ELSE 0 END) as alpa', [AttendanceStatus::Alpa->value])
            ->selectRaw("SUM(CASE WHEN LOWER(absen_murid.status) IN ('masuk','terlambat') THEN 1 ELSE 0 END) as tidak_absen_pulang")
            ->selectRaw('COUNT(absen_murid.id) as total')
            ->first();

        $hadir = (int) ($row->hadir ?? 0);
        $izin = (int) ($row->izin ?? 0);
        $alpa = (int) ($row->alpa ?? 0);
        $tidakAbsenPulang = (int) ($row->tidak_absen_pulang ?? 0);
        $total = (int) ($row->total ?? 0);

        return [
            'hadir' => $hadir,
            'izin' => $izin,
            'alpa' => $alpa,
            'tidak_absen_pulang' => $tidakAbsenPulang,
            'total' => $total,
            'persen_hadir' => $this->formatPercent($hadir, $total),
            'persen_izin' => $this->formatPercent($izin, $total),
            'persen_alpa' => $this->formatPercent($alpa, $total),
            'komposisi' => $this->formatPresenceCompositionOrDash($hadir, $izin, $alpa, $total),
            'persen_kehadiran' => $this->formatPercent($hadir, $hadir + $izin + $alpa),
        ];
    }

    private function formatAttendanceRateOrDash(int $hadir, int $izin, int $alpa): string
    {
        $meetings = $hadir + $izin + $alpa;

        if ($meetings === 0) {
            return '0%';
        }

        return number_format((($hadir) / $meetings) * 100, 2).'%';
    }

    private function formatPresenceCompositionOrDash(int $hadir, int $izin, int $alpa, int $total): string
    {
        if ($total === 0) {
            return '-';
        }

        return 'Hadir '.$this->formatPercent($hadir, $total)
            .' | Izin '.$this->formatPercent($izin, $total)
            .' | Alpa '.$this->formatPercent($alpa, $total);
    }

    private function formatPercent(int $value, int $total): string
    {
        if ($total === 0) {
            return '0%';
        }

        return number_format(($value / $total) * 100, 2).'%';
    }

    private function formatPercentOrDash(int $value, int $total): string
    {
        if ($total === 0) {
            return '-';
        }

        return $this->formatPercent($value, $total);
    }

    private function displayValue(int $value, int $total): string|int
    {
        if ($total === 0) {
            return '-';
        }

        return $value;
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
        if (! $this->tanggalDari && ! $this->tanggalSampai) {
            return '-';
        }

        $start = $this->tanggalDari ?: $this->tanggalSampai;
        $end = $this->tanggalSampai ?: $this->tanggalDari;

        if (! $start || ! $end) {
            return '-';
        }

        if ($start === $end) {
            return Carbon::parse($start)->format('d M Y');
        }

        return Carbon::parse($start)->format('d M Y').
            ' s/d '.
            Carbon::parse($end)->format('d M Y');
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function dateRange(): array
    {
        if (! $this->tanggalDari && ! $this->tanggalSampai) {
            return [null, null];
        }

        $start = $this->tanggalDari ? Carbon::parse($this->tanggalDari) : Carbon::parse($this->tanggalSampai);
        $end = $this->tanggalSampai ? Carbon::parse($this->tanggalSampai) : Carbon::parse($this->tanggalDari);

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        return [$start->toDateString(), $end->toDateString()];
    }
}
