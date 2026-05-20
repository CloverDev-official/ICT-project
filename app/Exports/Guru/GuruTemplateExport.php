<?php

namespace App\Exports\Guru;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GuruTemplateExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStyles, WithColumnWidths
{
    public function styles(Worksheet $sheet)
    {
        return [
            'A5:S5' => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => [
                            'argb' => 'FF000000',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5.29,  // No
            'B' => 31.71, // Nama
            'C' => 16.57, // NUPTK
            'D' => 4.43,  // JK
            'E' => 17, // Tempat Lahir
            'F' => 13.43, // Tanggal Lahir
            'G' => 16.57, // NIP
            'H' => 20, // Status Kepegawaian
            'I' => 18, // Jenis PTK
            'J' => 10.29, // Agama
            'K' => 45, // Alamat
            'L' => 2.71,  // RT
            'M' => 3.71,  // RW
            'N' => 17.43, // Kelurahan
            'O' => 14, // Kecamatan
            'P' => 9.29, // Kode Pos
            'Q' => 14.71, // Telepon
            'R' => 14.71, // HP
            'S' => 25.14, // E-mail
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NUPTK',
            'JK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'NIP',
            'Status Kepegawaian',
            'Jenis PTK',
            'Agama',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'Kode Pos',
            'Telepon',
            'HP',
            'E-mail',
        ];
    }

    public function collection(): Collection
    {
        return collect([
            [
                'No' => 1,
                'Nama' => 'Siti Nurhayati',
                'NUPTK' => '1234567890',
                'JK' => 'P',
                'Tempat Lahir' => 'Bandung',
                'Tanggal Lahir' => '1990-01-12',
                'NIP' => '1987654321',
                'Status Kepegawaian' => 'PNS',
                'Jenis PTK' => 'Guru Mapel',
                'Agama' => 'Islam',
                'Alamat' => 'Jl. Contoh',
                'RT' => '01',
                'RW' => '02',
                'Kelurahan' => 'Sukamaju',
                'Kecamatan' => 'Antapani',
                'Kode Pos' => '40291',
                'Telepon' => '08123456789',
                'HP' => '08123456789',
                'E-mail' => 'guru@example.com',
            ],
        ]);
    }

    public function startCell(): string
    {
        return 'A5';
    }
}
