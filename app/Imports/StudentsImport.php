<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts
{
    use Importable;

    protected $classroomId;

    public function __construct($classroomId)
    {
        $this->classroomId = $classroomId;
    }

    /**
     * Tentukan kolom unik untuk upsert (jika NIS sudah ada, update data)
     */
    public function uniqueBy()
    {
        return 'nis';
    }

    /**
     * Mapping setiap baris Excel ke Model Student
     */
    public function model(array $row)
    {
        return new Student([
            'classroom_id' => $this->classroomId,
            'name'         => $row['nama'],
            'nis'          => (string) $row['nis'],
            'nisn'         => (string) ($row['nisn'] ?? null),
            'gender'       => $row['jenis_kelamin'] ?? null,
            'phone'        => $row['no_hp'] ?? null,
            'email'        => $row['email'] ?? null,
            'address'      => $row['alamat'] ?? null,
        ]);
    }

    /**
     * Aturan validasi untuk setiap baris
     */
    public function rules(): array
    {
        return [
            'nama'  => 'required|string|max:255',
            'nis'   => 'required|numeric|digits_between:1,20',
            'nisn'  => 'nullable|numeric|digits_between:1,20',
            'email' => 'nullable|email|max:255',
        ];
    }

    /**
     * Pesan error kustom
     */
    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom Nama wajib diisi.',
            'nis.required'  => 'Kolom NIS wajib diisi.',
            'nis.digits_between' => 'NIS maksimal 20 digit.',
        ];
    }
}
