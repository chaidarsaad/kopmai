<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nomor_induk_santri',
        'nama_santri',
        'nama_wali_santri',
    ];
}
