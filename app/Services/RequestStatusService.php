<?php

namespace App\Services;

use Carbon\Carbon;

class RequestStatusService
{
    // Status Constants
    const STATUS_WAITING    = 'Menunggu Verifikasi';
    const STATUS_PROCESSING = 'Sedang Proses Pengadaan';
    const STATUS_COMPLETED  = 'Selesai';
    const STATUS_REJECTED   = 'Pengajuan Ditolak';

    public static function getStatusLabel($status): string
    {
        return match ($status) {
            self::STATUS_WAITING    => 'Menunggu Verifikasi',
            self::STATUS_PROCESSING => 'Sedang Proses Pengadaan',
            self::STATUS_COMPLETED  => 'Selesai',
            self::STATUS_REJECTED   => 'Pengajuan Ditolak',
            default                 => 'Status Tidak Diketahui'
        };
    }

    public static function getStatusColor($status): string
    {
        return match ($status) {
            self::STATUS_WAITING    => 'text-orange-500',
            self::STATUS_PROCESSING => 'text-blue-500',
            self::STATUS_COMPLETED  => 'text-green-500',
            self::STATUS_REJECTED   => 'text-red-500',
            default                 => 'text-gray-500'
        };
    }

    public static function getStatusInfo($status): array
    {
        return match ($status) {
            self::STATUS_WAITING => [
                'icon'    => 'bi-clock-fill',
                'color'   => 'orange',
                'title'   => 'Menunggu Verifikasi',
                'message' => 'Koperasi sedang memverifikasi pengadaan Anda'
            ],
            self::STATUS_PROCESSING => [
                'icon'    => 'bi-box-seam-fill',
                'color'   => 'blue',
                'title'   => 'Pengadaan Diproses',
                'message' => 'Koperasi sedang memproses pengadaan Anda'
            ],

            self::STATUS_COMPLETED => [
                'icon'    => 'bi-check-circle-fill',
                'color'   => 'green',
                'title'   => 'Pengadaan Selesai',
                'message' => 'Pengadaan Anda telah selesai'
            ],
            self::STATUS_REJECTED => [
                'icon'    => 'bi-x-circle-fill',
                'color'   => 'red',
                'title'   => 'Pesanan Dibatalkan',
                'message' => 'Pengadaan Anda telah dibatalkan'
            ],
            default => [
                'icon'    => 'bi-info-circle-fill',
                'color'   => 'gray',
                'title'   => 'Status Tidak Diketahui',
                'message' => ''
            ]
        };
    }
}
