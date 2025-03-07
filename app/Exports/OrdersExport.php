<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersExport implements FromCollection, WithCustomStartCell, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();
    }

    // public function headings(): array
    // {
    //     return [
    //         "Tanggal",
    //         "Tujuan (SANTRI)",
    //         "Kelas",
    //         "Jumlah",
    //         "Nama Barang",
    //         "Tenant",
    //         "Harga Satuan",
    //         "Modal",
    //         "Laba",
    //     ];
    // }

    public function collection()
    {
        return Order::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('payment_status', 'paid')
            ->with(['classroom', 'items.product'])
            ->get()
            ->flatMap(function ($order) {
                $data = [];

                // Tambahkan header untuk setiap order
                $data[] = [
                    'created_at'  => 'Tanggal',
                    'nama_santri' => 'Tujuan (SANTRI)',
                    'kelas'       => 'Kelas',
                    'jumlah'      => 'Jumlah',
                    'nama_barang' => 'Nama Barang',
                    'tenant'      => 'Tenant',
                    'harga_satuan' => 'Harga Satuan',
                    'modal'       => 'Modal',
                    'laba'        => 'Laba',
                ];

                foreach ($order->items as $item) {
                    $data[] = [
                        'created_at'  => Carbon::parse($order->created_at)->format('d M Y H:i:s'),
                        'nama_santri' => $order->nama_santri,
                        'kelas'       => $order->classroom ? $order->classroom->name : 'Tidak ada Kelas',
                        'jumlah'      => $item->quantity,
                        'nama_barang' => $item->product ? $item->product->name : 'Produk tidak ditemukan',
                        'tenant'      => $item->product ? $item->product->shop->name : 'Tenant tidak ditemukan',
                        'harga_satuan' => $item->product ? $item->product->price : 0,
                        'modal'       => $item->product ? $item->product->modal : 0,
                        'laba'        => $item->product ? $item->product->laba : 0,
                    ];
                }

                $data[] = [
                    'created_at'  => '',
                    'nama_santri' => '',
                    'kelas'       => '',
                    'jumlah'      => '',
                    'nama_barang' => 'TOTAL ORDER (Tanpa Ongkir):',
                    'tenant'      => '',
                    'harga_satuan' => $order->subtotal,
                    'modal'       => '',
                    'laba'        => '',
                ];

                // Baris kosong sebagai pemisah antara order
                $data[] = [
                    'created_at'  => null,
                    'nama_santri' => null,
                    'kelas'       => null,
                    'jumlah'      => null,
                    'nama_barang' => null,
                    'tenant'      => null,
                    'harga_satuan' => null,
                    'modal'       => null,
                    'laba'        => null,
                ];

                return $data;
            });
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $lastRow = $event->sheet->getHighestRow();

                $sheet->getStyle("G2:I{$lastRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

                for ($row = 1; $row <= $lastRow; $row++) {
                    $cellValue = $sheet->getCell("A{$row}")->getValue();
                    if ($cellValue === 'Tanggal') {
                        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => 'center'],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8CB9C'],
                            ],
                        ]);
                    }
                }

                for ($row = 2; $row <= $lastRow; $row++) {
                    $cellValue = $sheet->getCell("E{$row}")->getValue();
                    if ($cellValue === 'TOTAL ORDER (Tanpa Ongkir):') {
                        foreach (['E', 'G'] as $col) {
                            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                                'font' => ['bold' => true],
                                'alignment' => ['horizontal' => 'center'],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => '00FF00'],
                                ],
                            ]);
                        }
                    }
                }

                $sheet->getStyle("A2:I{$lastRow}")->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);
            }
        ];
    }
}
