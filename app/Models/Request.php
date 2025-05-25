<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'request_number',
        'user_id',
        'tanggal_permohonan',
        'nama_pemesan',
        'kelas_divisi',
        'nama_barang',
        'jumlah_barang',
        'tujuan',
        'sumber_dana',
        'budget',
        'deadline',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function (Request $model) {
            $model->uid = (string) str()->uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uid';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
