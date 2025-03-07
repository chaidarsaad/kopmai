<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductEditImport implements ToModel, WithMultipleSheets, WithHeadingRow
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

        return Product::updateOrCreate(
            ['id' => $row['id']],
            [
                'name' => $row['name'],
                'category_id' => $row['category_id'],
                'shop_id' => $row['shop_id'],
                'stock' => $row['stock'],
                'price' => $row['price'],
                'modal' => $row['modal'],
                'laba' => $row['laba'],
                'is_active' => $row['is_active'] ?? 0,
                'description' => $row['description'] ?? '',
                'image' => $row['image'],
            ]
        );
    }

    public function getRowCount()
    {
        return $this->count;
    }
}
