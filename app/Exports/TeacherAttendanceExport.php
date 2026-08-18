<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TeacherAttendanceExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithColumnWidths
{
    protected $data;

    /**
     * Constructor hanya menerima 1 argumen yaitu data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();
        $no = 1;

        if (empty($this->data)) {
            return collect([
                ['', 'Tidak ada data', '', '', '', '']
            ]);
        }

        foreach ($this->data as $item) {
            $rows->push([
                $no++,
                $item['Nama Guru'],
                $item['Hadir'],
                $item['Terlambat'],
                $item['Total Presensi'],
                $item['Persentase Kehadiran'],
            ]);
        }

        // Tambahkan baris total
        $totalHadir = collect($this->data)->sum('Hadir');
        $totalTerlambat = collect($this->data)->sum('Terlambat');
        $totalPresensi = collect($this->data)->sum('Total Presensi');
        $avgPersentase = $totalPresensi > 0 ? round(($totalHadir / $totalPresensi) * 100, 2) . '%' : '0%';

        $rows->push([
            '',
            'TOTAL',
            $totalHadir,
            $totalTerlambat,
            $totalPresensi,
            $avgPersentase,
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['REKAP PRESENSI GURU'],
            [
                'No',
                'Nama Guru',
                'Hadir',
                'Terlambat',
                'Total Presensi',
                'Persentase Kehadiran (%)'
            ]
        ];
    }

    public function title(): string
    {
        return 'Rekap Presensi Guru';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 35,
            'C' => 15,
            'D' => 15,
            'E' => 18,
            'F' => 22,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge header title
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style header kolom (baris 2)
        $sheet->getStyle('A2:F2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Border seluruh tabel (mulai dari baris 2)
        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 2) {
            $sheet->getStyle('A2:F' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);
        }

        // Center alignment untuk data angka (kolom C-F)
        if ($highestRow >= 2) {
            $sheet->getStyle('C3:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Baris total (baris terakhir) - style bold dan background kuning
        $lastRow = $highestRow;
        if ($lastRow >= 2) {
            $sheet->getStyle('A' . $lastRow . ':F' . $lastRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3CD']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // Tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);

        return $sheet;
    }
}
