<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Contoh Nama', '1234567890', '9876543210', 'L', '081234567890', 'siswa@email.com', 'Jl. Contoh No. 1']
        ];
    }

    public function headings(): array
    {
        return [
            'nama',
            'nis',
            'nisn',
            'jenis_kelamin',
            'no_hp',
            'email',
            'alamat'
        ];
    }
}
