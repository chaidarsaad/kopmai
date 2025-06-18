<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsEditImport implements ToModel, WithHeadingRow, WithValidation
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

        return Student::updateOrCreate(
            ['id' => $row['id']],
            [
                "nomor_induk_santri" => $row["nomor_induk_santri"],
                "nama_santri" => $row["nama_santri"],
                "nama_wali_santri" => $row["nama_wali_santri"],
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.id' => ['required', 'exists:students,id'],

            '*.nomor_induk_santri' => function ($attribute, $value, $fail) {
                $id = $this->getRowValue($attribute, 'id');
                if ($id && Student::where('nomor_induk_santri', $value)->where('id', '!=', $id)->exists()) {
                    $fail("Nomor Induk Santri sudah digunakan oleh siswa lain.");
                }
            },

            '*.nama_santri' => function ($attribute, $value, $fail) {
                $id = $this->getRowValue($attribute, 'id');
                if ($id && Student::where('nama_santri', $value)->where('id', '!=', $id)->exists()) {
                    $fail("Nama Santri sudah digunakan oleh siswa lain.");
                }
            },

            '*.nama_wali_santri' => ['required'],
        ];
    }

    private function getRowValue(string $attribute, string $field)
    {
        $rowIndex = explode('.', $attribute)[0];
        $rowData = request()->input($rowIndex, []);
        return $rowData[$field] ?? null;
    }


    public function customValidationMessages(): array
    {
        return [
            '*.nomor_induk_santri.required' => 'Nomor Induk Santri wajib diisi.',
            '*.nomor_induk_santri.unique' => 'Nomor Induk Santri sudah ada.',
            '*.nama_santri.required' => 'Nama Santri wajib diisi.',
            '*.nama_santri.unique' => 'Nama Santri sudah ada.',
            '*.nama_wali_santri.required' => 'Nama Wali Santri wajib diisi.',
        ];
    }

    public function getRowCount()
    {
        return $this->count;
    }
}
