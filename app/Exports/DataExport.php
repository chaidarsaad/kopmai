<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductsExport(),
            new CategoriesExport(),
            new ShopsExport()
        ];
    }
}

class ProductsExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Product::select(
            'id',
            'name',
            'category_id',
            'shop_id',
            'price',
            'modal',
            'laba',
            'stock',
            'description',
            'image',
        )->orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'category_id',
            'shop_id',
            'price',
            'modal',
            'laba',
            'stock',
            'description',
            'image',
        ];
    }

    public function title(): string
    {
        return 'Produk';
    }
}

class CategoriesExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Category::select('id', 'name')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'name'
        ];
    }

    public function title(): string
    {
        return 'Kategori';
    }
}

class ShopsExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Shop::select('id', 'name')->orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'name'
        ];
    }

    public function title(): string
    {
        return 'Tenant';
    }
}
