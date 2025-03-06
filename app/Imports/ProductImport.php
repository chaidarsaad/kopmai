<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel, WithHeadingRow, WithMultipleSheets, SkipsEmptyRows
{
    public $count = 0;
    public function sheets(): array
    {
        return [
            0 => $this
        ];
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->count++;
        return new Product([
            'name' => $row['name'],
            'category_id' => $row['category_id'],
            'shop_id' => $row['shop_id'],
            'stock' => $row['stock'],
            'price' => $row['price'],
            'modal' => $row['modal'],
            'laba' => $row['laba'],
            'is_active' => $row['is_active'],
            'description' => $row['description'] ?? '',
            'image' => $row['image'],
        ]);
    }

    public function getRowCount()
    {
        return $this->count;
    }
}
