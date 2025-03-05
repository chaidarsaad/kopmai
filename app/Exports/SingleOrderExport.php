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
            "Modal",
            "Laba",
        ];
    }

    public function collection()
    {
        $order = Order::where('id', $this->orderId)
            ->with(['classroom', 'items.product'])
            ->first();

        if (!$order) {
            return collect([]);
        }

        $data = $order->items->map(function ($item) use ($order) {
            return [
                'nama_santri' => $order->nama_santri,
                'kelas'       => $order->classroom ? $order->classroom->name : 'Tidak ada Kelas',
                'jumlah'      => $item->quantity,
                'nama_barang' => $item->product ? $item->product->name : 'Produk tidak ditemukan',
                'harga_satuan' => $item->product ? $item->product->price : 0,
                'modal'       => $item->product ? $item->product->modal : 0,
                'laba'        => $item->product ? $item->product->laba : 0,
            ];
        });

        $data->push([
            'nama_santri'  => '',
            'kelas'        => '',
            'jumlah'       => '',
            'nama_barang'  => 'TOTAL ORDER (Tanpa Ongkir):',
            'harga_satuan' => $order->subtotal,
            'modal'        => '',
            'laba'         => '',
        ]);

        return $data;
    }

    public function startCell(): string
    {
        return 'A1';
    }
}
