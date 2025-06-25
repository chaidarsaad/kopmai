<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class SingleOrderExport implements FromCollection, WithHeadings, WithCustomStartCell
{
    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function headings(): array
    {
        return [
            "Tujuan (SANTRI)",
            "Kelas",
            "Jumlah",
            "Nama Barang",
            "Harga Satuan",
        ];
    }

    public function collection()
    {
        $order = Order::where('id', $this->orderId)
            ->with(['items.product'])
            ->first();

        if (!$order) {
            return collect([]);
        }

        $data = $order->items->map(function ($item) use ($order) {
            return [
                'jumlah' => $item->quantity,
                'nama_barang' => $item->product ? $item->product->name : 'Produk tidak ditemukan',
                'harga_satuan' => $item->product ? $item->product->price : 0,
            ];
        });

        $data->push([
            'kelas' => '',
            'jumlah' => '',
            'nama_barang' => 'TOTAL ORDER (Tanpa Ongkir):',
            'harga_satuan' => $order->subtotal,
        ]);

        return $data;
    }

    public function startCell(): string
    {
        return 'A1';
    }
}
