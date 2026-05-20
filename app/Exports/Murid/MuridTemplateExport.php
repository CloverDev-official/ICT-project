<?php

namespace App\Exports\Murid;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MuridTemplateExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStyles, WithColumnWidths
{
    public function styles(Worksheet $sheet)
    {
        return [
            'A5:T5' => [
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
            'C' => 16.57, // NIPD
            'D' => 4.43,  // JK
            'E' => 10.29, // NISN
            'F' => 17, // Tempat Lahir
            'G' => 13.43, // Tanggal Lahir
            'H' => 10.29, // Agama
            'I' => 45, // Alamat
            'J' => 2.71,  // RT
            'K' => 3.71,  // RW
            'L' => 17.43, // Kelurahan
            'M' => 14, // Kecamatan
            'N' => 9.29, // Kode Pos
            'O' => 14.71, // Telepon
            'P' => 25.14, // E-mail
            'Q' => 27.43, // Nama Ayah
            'R' => 27.43, // Nama Ibu
            'S' => 27.43, // Nama Wali
            'T' => 17.57, // Rombel Saat Ini
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIPD',
            'JK',
            'NISN',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'Kode Pos',
            'Telepon',
            'E-mail',
            'Nama Ayah',
            'Nama Ibu',
            'Nama Wali',
            'Rombel Saat Ini',
        ];
    }

    public function collection(): Collection
    {
        return collect([
            [
                'No' => 1,
                'Nama' => 'Abdul rojak rojali',
                'NIPD' => '12345',
                'JK' => 'L',
                'NISN' => '99887766',
                'Tempat Lahir' => 'Bandung',
                'Tanggal Lahir' => '2008-01-12',
                'Agama' => 'Islam',
                'Alamat' => 'Jl. Contoh',
                'RT' => '01',
                'RW' => '02',
                'Kelurahan' => 'Sukamaju',
                'Kecamatan' => 'Antapani',
                'Kode Pos' => '40291',
                'Telepon' => '08123456789',
                'E-mail' => 'siswa@example.com',
                'Nama Ayah' => 'Nama Ayah',
                'Nama Ibu' => 'Nama Ibu',
                'Nama Wali' => 'Nama Wali',
                'Rombel Saat Ini' => 'X PPLG A',
            ],
        ]);
    }

    public function startCell(): string
    {
        return 'A5';
    }
}
