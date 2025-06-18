<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class StudentsExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Student::select(
            'id',
            'nomor_induk_santri',
            'nama_santri',
            'nama_wali_santri',
        )->orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'nomor_induk_santri',
            'nama_santri',
            'nama_wali_santri',
        ];
    }

    public function title(): string
    {
        return 'Data Santri';
    }
}
