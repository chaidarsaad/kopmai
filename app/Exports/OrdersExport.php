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
    //     ];
    // }

    public function collection()
    {
        return Order::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('payment_status', 'paid')
            ->with(['items.product'])
            ->get()
            ->flatMap(function ($order) {
                $data = [];

                $data[] = [
                    'order_number' => 'No. Pesanan',
                    'created_at' => 'Tanggal',
                    'email' => 'Email Akun',
                    'nama_wali_santri' => 'Nama Wali (BIN/BINTI)',
                    'no_hp_wali' => 'No HP Wali (BIN/BINTI)',
                    'nama_santri' => 'Nama Santri',
                    'note' => 'Catatan Tambahan',
                    'nama_barang' => 'Nama Produk',
                    'jumlah' => 'Jumlah Produk',
                    'harga_jual' => 'Harga Jual',
                    'harga_beli' => 'Harga Beli',
                    'tenant' => 'Tenant',
                    'tanggal_bayar' => 'Tanggal Bayar',
                    'nominal_bayar' => 'Nominal Bayar',
                    // 'harga_satuan' => 'Harga Total',
                ];

                foreach ($order->items as $item) {
                    $data[] = [
                        'order_number' => $order->order_number,
                        'created_at' => Carbon::parse($order->created_at)->translatedFormat('l, d F Y H:i:s'),
                        'email' => $order->user->email,
                        'nama_wali_santri' => $order->student && $order->student->nama_wali_santri
                            ? $order->student->nama_wali_santri
                            : 'Nama wali santri tidak tersedia',
                        'no_hp_wali' => $order->phone,
                        'nama_santri' => $order->student && $order->student->nama_santri
                            ? $order->student->nama_santri
                            : 'Nama santri tidak tersedia',
                        'note' => $order->note ? $order->note : 'Tidak ada catatan',
                        'nama_barang' => $item->product ? $item->product->name : 'Produk tidak ditemukan',
                        'jumlah' => $item->quantity,
                        'harga_jual' => $item->product
                            ? ($item->product->price == 0
                                ? 'Harga jual produk ini tidak tersedia, cek data produk'
                                : 'Rp ' . number_format($item->product->price, 2, ',', '.'))
                            : 'Produk tidak ditemukan',
                        'harga_beli' => $item->product
                            ? ($item->product->buying_price == 0
                                ? 'Harga beli produk ini tidak tersedia, cek data produk'
                                : 'Rp ' . number_format($item->product->buying_price, 2, ',', '.'))
                            : 'Produk tidak ditemukan',
                        'tenant' => $item->product ? $item->product->shop->name : 'Tenant tidak ditemukan',
                        'tanggal_bayar' => $order->paid_date ? Carbon::parse($order->paid_at)->translatedFormat('l, d F Y') : 'Tanggal pembayaran tidak tersedia, cek data pesanan',
                        'nominal_bayar' => $order->nominal_pembayaran ? 'Rp ' . number_format($order->nominal_pembayaran, 2, ',', '.') : 'Nominal pembayaran tidak tersedia, cek data pesanan',
                        // 'harga_satuan' => $item->product ? 'Rp ' . number_format(($item->product->price * $item->quantity), 2, ',', '.') : 'Rp 0,00',
                    ];
                }

                $data[] = [
                    'order_number' => '',
                    'created_at' => '',
                    'email' => '',
                    'nama_wali_santri' => '',
                    'no_hp_wali' => '',
                    'nama_santri' => '',
                    'note' => '',
                    'nama_barang' => '',
                    'jumlah' => '',
                    'harga_jual' => '',
                    'harga_beli' => '',
                    'tenant' => '',
                    'tanggal_bayar' => '',
                    'nominal_bayar' => '',
                    // 'harga_satuan' => 'Rp ' . number_format($order->subtotal, 2, ',', '.'),
                ];

                $data[] = [
                    'order_number' => null,
                    'created_at' => null,
                    'email' => null,
                    'nama_wali_santri' => null,
                    'no_hp_wali' => null,
                    'nama_santri' => null,
                    'note' => null,
                    'nama_barang' => null,
                    'jumlah' => null,
                    'harga_jual' => null,
                    'harga_beli' => null,
                    'tenant' => null,
                    'tanggal_bayar' => null,
                    'nominal_bayar' => null,
                    // 'harga_satuan' => null,
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

                $highestColumn = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();

                // ✅ Atur ukuran kolom otomatis
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // ✅ Format kolom harga
                $columnsToFormat = ['H', 'J', 'M'];
                foreach ($columnsToFormat as $col) {
                    $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                        ->getNumberFormat()
                        ->setFormatCode('"Rp" #,##0.00');
                }

                // ✅ Format semua baris yang merupakan header (cek jika A = 'No. Pesanan')
                for ($row = 1; $row <= $lastRow; $row++) {
                    $cellValue = $sheet->getCell("A{$row}")->getValue();
                    if ($cellValue === 'No. Pesanan') {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => 'center'],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8CB9C'],
                            ],
                        ]);
                    }
                }

                // ✅ Sel lainnya tetap diratakan ke tengah
                $sheet->getStyle("A2:{$highestColumn}{$lastRow}")->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);
            }
        ];
    }



}
