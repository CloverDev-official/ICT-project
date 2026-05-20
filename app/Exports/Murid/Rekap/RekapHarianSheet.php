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

class RekapHarianSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, WithChunkReading, WithCustomStartCell, WithEvents, ShouldAutoSize
{
    private AbsenRekapQuery $rekapQuery;
    private ?string $tanggalDari;
    private ?string $tanggalSampai;

    public function __construct(?string $tanggalDari, ?string $tanggalSampai, ?int $rombelId)
    {
        $this->rekapQuery = new AbsenRekapQuery($tanggalDari, $tanggalSampai, $rombelId);
        $this->tanggalDari = $tanggalDari;
        $this->tanggalSampai = $tanggalSampai;
    }

    public function query(): Builder
    {
        $dateExpr = $this->rekapQuery->dateExpr();

        return $this->rekapQuery->base()
            ->selectRaw("{$dateExpr} as tanggal")
            ->selectRaw("COUNT(*) as total_murid")
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Hadir' THEN 1 ELSE 0 END) as hadir")
            ->selectRaw("SUM(CASE WHEN absen_murid.status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat")
            ->selectRaw("SUM(CASE WHEN absen_murid.status IN ('Izin', 'Sakit') THEN 1 ELSE 0 END) as izin_sakit")
            ->selectRaw("SUM(CASE WHEN absen_murid.keterangan IS NOT NULL AND absen_murid.keterangan <> '' THEN 1 ELSE 0 END) as catatan")
            ->groupBy(DB::raw($dateExpr))
            ->orderBy('tanggal');
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Hari',
            'Total Murid',
            'Hadir',
            'Tidak Hadir',
            'Terlambat',
            'Izin/Sakit',
            '% Hadir',
            '% Tidak Hadir',
            'Catatan',
        ];
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function map($row): array
    {
        $totalMurid = (int) $row->total_murid;
        $hadir = (int) $row->hadir;
        $terlambat = (int) $row->terlambat;
        $izinSakit = (int) $row->izin_sakit;
        $tidakHadir = max($totalMurid - $hadir - $terlambat - $izinSakit, 0);
        $persenHadir = $this->formatPercent($hadir, $totalMurid);
        $persenTidak = $this->formatPercent($tidakHadir, $totalMurid);
        $catatan = (int) $row->catatan;

        return [
            $row->tanggal ? Carbon::parse($row->tanggal)->format('d-m-Y') : '-',
            $this->dayName($row->tanggal),
            $totalMurid,
            $hadir,
            $tidakHadir,
            $terlambat,
            $izinSakit,
            $persenHadir,
            $persenTidak,
            $catatan > 0 ? "Ada: {$catatan}" : '-',
        ];
    }

    public function title(): string
    {
        return 'Rekap Harian';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'J';
                $headerRow = 7;
                $highestRow = $sheet->getHighestRow();

                $summary = $this->summaryStats();
                $titleColor = '1E40AF';
                $sectionColor = 'DBEAFE';

                $sheet->setCellValue('A1', 'Rekap Kehadiran Harian');
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A2', 'Sekolah: ' . $this->schoolName());
                $sheet->mergeCells("A2:D2");
                $sheet->setCellValue('E2', 'Periode: ' . $this->periodeLabel());
                $sheet->mergeCells("E2:G2");
                $sheet->setCellValue('H2', 'Export: ' . now()->format('d-m-Y H:i'));
                $sheet->mergeCells("H2:{$lastColumn}2");
                $sheet->setCellValue('A3', 'Ringkasan');
                $sheet->mergeCells("A3:{$lastColumn}3");

                $sheet->mergeCells('A4:B5');
                $sheet->mergeCells('C4:D5');
                $sheet->mergeCells('E4:F5');
                $sheet->mergeCells('G4:H5');
                $sheet->mergeCells('I4:J5');

                $sheet->setCellValue('A4', "Total Murid\n{$summary['total_murid']}");
                $sheet->setCellValue('C4', "Hadir\n{$summary['hadir']}");
                $sheet->setCellValue('E4', "Tidak Hadir\n{$summary['tidak_hadir']}");
                $sheet->setCellValue('G4', "Terlambat\n{$summary['terlambat']}");
                $sheet->setCellValue('I4', "Izin/Sakit\n{$summary['izin_sakit']}");

                $sheet->setCellValue('A6', 'Persentase Kehadiran Keseluruhan: ' . $summary['persen_hadir']);
                $sheet->mergeCells("A6:{$lastColumn}6");

                $titleStyle = [
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $titleColor]],
                ];

                $sectionStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $sectionColor]],
                ];

                $headerStyle = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
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
                $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray($sectionStyle);
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray($headerStyle);
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$highestRow}")->applyFromArray($borderStyle);

                $sheet->getStyle('A4:J5')->getAlignment()->setWrapText(true);
                $sheet->getStyle('A4:J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4:J5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A4:B5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');
                $sheet->getStyle('C4:D5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
                $sheet->getStyle('E4:F5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                $sheet->getStyle('G4:H5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7');
                $sheet->getStyle('I4:J5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E7FF');

                $sheet->getStyle('A6')->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->freezePane('A8');
            },
        ];
    }

    private function summaryStats(): array
    {
        $row = $this->rekapQuery->base()
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

    public function chunkSize(): int
    {
        return 1000;
    }
}
