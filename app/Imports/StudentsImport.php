<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    public $count = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->count++;
        return new Student([
            'nomor_induk_santri' => $row['nomor_induk_santri'],
            'nama_santri' => $row['nama_santri'],
            'nama_wali_santri' => $row['nama_wali_santri'],
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nomor_induk_santri' => ['required', 'unique:students,nomor_induk_santri'],
            '*.nama_santri' => ['required', 'unique:students,nama_santri'],
            '*.nama_wali_santri' => ['required'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.nomor_induk_santri.required' => 'Nomor Induk Santri wajib diisi.',
            '*.nomor_induk_santri.unique' => 'Nomor Induk Santri sudah ada.',
            '*.nama_santri.required' => 'Nama Santri wajib diisi.',
            '*.nama_santri.unique' => 'Nama Santri sudah ada.',
            '*.nama_wali_santri.required' => 'BIN / BINTI wajib diisi.',
        ];
    }

    public function getRowCount()
    {
        return $this->count;
    }
}
