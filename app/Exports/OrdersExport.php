<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class OrdersExport implements FromCollection, WithHeadings, WithCustomStartCell
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();
    }

    public function headings(): array
    {
        return [
            "Tanggal",
            "Tujuan (SANTRI)",
            "Kelas",
            "Jumlah",
            "Nama Barang",
            "Tenant",
            "Harga Satuan",
            "Modal",
            "Laba",
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Order::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('payment_status', 'paid')
            ->with(['classroom', 'items.product'])
            ->get()
            ->flatMap(function ($order) {
                $data = [];

                foreach ($order->items as $item) {
                    $data[] = [
                        'created_at'  => $order->created_at,
                        'nama_santri'  => $order->nama_santri,
                        'kelas'        => $order->classroom ? $order->classroom->name : 'Tidak ada Kelas',
                        'jumlah'       => $item->quantity,
                        'nama_barang'  => $item->product ? $item->product->name : 'Produk tidak ditemukan',
                        'tenant'  => $item->product ? $item->product->shop->name : 'Tenant tidak ditemukan',
                        'harga_satuan' => $item->product ? $item->product->price : 0,
                        'modal'        => $item->product ? $item->product->modal : 0,
                        'laba'         => $item->product ? $item->product->laba : 0,
                    ];
                }

                $data[] = [
                    'created_at'  => '',
                    'nama_santri'  => '',
                    'kelas'        => '',
                    'jumlah'       => '',
                    'nama_barang'  => 'TOTAL ORDER (Tanpa Ongkir):',
                    'tenant'       => '',
                    'harga_satuan' => $order->subtotal,
                    'modal'        => '',
                    'laba'         => '',
                ];

                $data[] = [
                    'created_at'  => null,
                    'nama_santri'  => null,
                    'kelas'        => null,
                    'jumlah'       => null,
                    'nama_barang'  => null,
                    'tenant'  => null,
                    'harga_satuan' => null,
                    'modal'        => null,
                    'laba'         => null,
                ];

                return $data;
            });
    }


    public function startCell(): string
    {
        return 'A1';
    }
}
