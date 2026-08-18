<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentAttendanceReportExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithColumnWidths
{
    protected $data;
    protected $className;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $className, $startDate, $endDate)
    {
        $this->data = $data;
        $this->className = $className;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $rows = [];
        $no = 1;

        if (empty($this->data)) {
            return [
                ['', 'Tidak ada data', '', '', '', '']
            ];
        }

        foreach ($this->data as $item) {
            $rows[] = [
                $no++,
                $item['nis'],
                $item['name'],
                $item['izin'],
                $item['sakit'],
                $item['alpa'],
                $item['total_tidak_hadir'],
            ];
        }

        // Tambahkan baris total
        $totalIzin = array_sum(array_column($this->data, 'izin'));
        $totalSakit = array_sum(array_column($this->data, 'sakit'));
        $totalAlpa = array_sum(array_column($this->data, 'alpa'));
        $totalTidakHadir = array_sum(array_column($this->data, 'total_tidak_hadir'));

        $rows[] = [
            '',
            'TOTAL',
            '',
            $totalIzin,
            $totalSakit,
            $totalAlpa,
            $totalTidakHadir,
        ];

        return $rows;
    }

    public function headings(): array
    {
        $title = 'REKAP PRESENSI SISWA - ' . strtoupper($this->className);
        $period = 'Periode: ' . \Carbon\Carbon::parse($this->startDate)->locale('id')->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($this->endDate)->locale('id')->translatedFormat('d F Y');

        return [
            [$title],
            [$period],
            [
                'No',
                'NIS',
                'Nama Siswa',
                'Izin',
                'Sakit',
                'Alpa',
                'Total Tidak Hadir'
            ]
        ];
    }

    public function title(): string
    {
        return 'Rekap Presensi Siswa';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 15,
            'C' => 30,
            'D' => 12,
            'E' => 12,
            'F' => 12,
            'G' => 18,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge header title (baris 1)
        $sheet->mergeCells('A1:G1');
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

        // Merge header periode (baris 2)
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '6C757D'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style header kolom (baris 3)
        $sheet->getStyle('A3:G3')->applyFromArray([
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

        // Border seluruh tabel (mulai dari baris 3)
        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 3) {
            $sheet->getStyle('A3:G' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);
        }

        // Center alignment untuk data angka (kolom D-G)
        if ($highestRow >= 3) {
            $sheet->getStyle('D4:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Baris total (baris terakhir) - style bold dan background kuning
        $lastRow = $highestRow;
        if ($lastRow >= 3) {
            $sheet->getStyle('A' . $lastRow . ':G' . $lastRow)->applyFromArray([
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
        $sheet->getRowDimension(3)->setRowHeight(25);

        return $sheet;
    }
}
