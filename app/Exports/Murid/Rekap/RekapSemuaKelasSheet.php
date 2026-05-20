<?php

namespace App\Exports\Murid\Rekap;

use App\Models\Setting;
use App\Services\Murid\Rekap\AbsenRekapQuery;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapSemuaKelasSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, WithChunkReading, WithCustomStartCell, WithEvents, ShouldAutoSize
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
        return 'Rekap Semua Kelas';
    }

    public function startCell(): string
    {
        return 'A10';
    }

    public function headings(): array
    {
        return [
            'Kelas',
            'Periode',
            'Total Murid',
            'Hadir',
            'Tidak Hadir',
            'Terlambat',
            'Izin/Sakit',
            '% Hadir',
        ];
    }

    public function query(): Builder
    {
        $rombelNamaExpr = $this->rekapQuery->rombelNamaExpr();

        return $this->rekapQuery->base()
            ->select('rombel.id')
            ->selectRaw("{$rombelNamaExpr} as kelas")
            ->selectRaw('COUNT(*) as total_murid')
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Hadir' THEN 1 ELSE 0 END) as hadir")
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat")
            ->selectRaw("SUM(CASE WHEN absen_murid.status IN ('Izin', 'Sakit') THEN 1 ELSE 0 END) as izin_sakit")
            ->groupBy('rombel.id', DB::raw($rombelNamaExpr))
            ->orderBy(DB::raw($rombelNamaExpr));
    }

    public function map($row): array
    {
        $totalMurid = (int) $row->total_murid;
        $hadir = (int) $row->hadir;
        $terlambat = (int) $row->terlambat;
        $izinSakit = (int) $row->izin_sakit;
        $tidakHadir = max($totalMurid - $hadir - $terlambat - $izinSakit, 0);

        return [
            trim((string) ($row->kelas ?? '')) ?: 'Tanpa Kelas',
            $this->periodeLabel(),
            $totalMurid,
            $hadir,
            $tidakHadir,
            $terlambat,
            $izinSakit,
            $this->formatPercent($hadir, $totalMurid),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'H';
                $headerRow = 10;
                $highestRow = $sheet->getHighestRow();

                $summary = $this->summaryStats();

                $titleColor = '0F172A';
                $sectionColor = 'E2E8F0';
                $headerColor = 'F1F5F9';
                $accentColor = 'DBEAFE';

                $sheet->setCellValue('A1', 'Rekap Kehadiran Semua Kelas');
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A2', 'Sekolah: ' . $this->schoolName());
                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('E2', 'Periode: ' . $this->periodeLabel());
                $sheet->mergeCells('E2:F2');
                $sheet->setCellValue('G2', 'Export: ' . now()->format('d-m-Y H:i'));
                $sheet->mergeCells('G2:H2');

                $sheet->setCellValue('A3', 'Ringkasan');
                $sheet->mergeCells("A3:{$lastColumn}3");

                $sheet->mergeCells('A4:B5');
                $sheet->mergeCells('C4:D5');
                $sheet->mergeCells('E4:F5');
                $sheet->mergeCells('G4:H5');

                $sheet->setCellValue('A4', "Total Kelas\n{$summary['total_kelas']}");
                $sheet->setCellValue('C4', "Total Murid\n{$summary['total_murid']}");
                $sheet->setCellValue('E4', "Hadir\n{$summary['hadir']}");
                $sheet->setCellValue('G4', "Tidak Hadir\n{$summary['tidak_hadir']}");

                $sheet->mergeCells('A6:B7');
                $sheet->mergeCells('C6:D7');
                $sheet->mergeCells('E6:H7');

                $sheet->setCellValue('A6', "Terlambat\n{$summary['terlambat']}");
                $sheet->setCellValue('C6', "Izin/Sakit\n{$summary['izin_sakit']}");
                $sheet->setCellValue('E6', "Persentase Kehadiran\n{$summary['persen_hadir']}");

                $sheet->setCellValue('A9', 'Rekap Semua Kelas');
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
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ];

                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray($titleStyle);
                $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray($sectionStyle);
                $sheet->getStyle("A9:{$lastColumn}9")->applyFromArray($sectionStyle);

                $sheet->getStyle('A4:H7')->applyFromArray($summaryStyle);
                $sheet->getStyle('A4:H7')->getAlignment()->setWrapText(true);

                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray($headerStyle);

                $totalRow = $highestRow + 1;
                $sheet->setCellValue("A{$totalRow}", 'Total');
                $sheet->setCellValue("B{$totalRow}", '-');
                $sheet->setCellValue("C{$totalRow}", $summary['total_murid']);
                $sheet->setCellValue("D{$totalRow}", $summary['hadir']);
                $sheet->setCellValue("E{$totalRow}", $summary['tidak_hadir']);
                $sheet->setCellValue("F{$totalRow}", $summary['terlambat']);
                $sheet->setCellValue("G{$totalRow}", $summary['izin_sakit']);
                $sheet->setCellValue("H{$totalRow}", $summary['persen_hadir']);

                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$totalRow}")
                    ->applyFromArray($borderStyle);

                $sheet->getStyle("A{$totalRow}:{$lastColumn}{$totalRow}")
                    ->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E7FF']],
                    ]);

                $sheet->getStyle("C{$headerRow}:{$lastColumn}{$totalRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("A{$headerRow}:B{$totalRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->freezePane('A11');
            },
        ];
    }

    private function summaryStats(): array
    {
        $row = $this->rekapQuery->base()
            ->selectRaw('COUNT(DISTINCT rombel.id) as total_kelas')
            ->selectRaw('COUNT(*) as total_murid')
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Hadir' THEN 1 ELSE 0 END) as hadir")
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat")
            ->selectRaw("SUM(CASE WHEN absen_murid.status IN ('Izin', 'Sakit') THEN 1 ELSE 0 END) as izin_sakit")
            ->first();

        $totalMurid = (int) ($row->total_murid ?? 0);
        $hadir = (int) ($row->hadir ?? 0);
        $terlambat = (int) ($row->terlambat ?? 0);
        $izinSakit = (int) ($row->izin_sakit ?? 0);
        $tidakHadir = max($totalMurid - $hadir - $terlambat - $izinSakit, 0);

        return [
            'total_kelas' => (int) ($row->total_kelas ?? 0),
            'total_murid' => $totalMurid,
            'hadir' => $hadir,
            'tidak_hadir' => $tidakHadir,
            'terlambat' => $terlambat,
            'izin_sakit' => $izinSakit,
            'persen_hadir' => $this->formatPercent($hadir, $totalMurid),
        ];
    }

    private function formatPercent(int $value, int $total): string
    {
        if ($total === 0) {
            return '0%';
        }

        return number_format(($value / $total) * 100, 2) . '%';
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

    public function chunkSize(): int
    {
        return 1000;
    }
}
