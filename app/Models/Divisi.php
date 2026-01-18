<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $fillable = [
        'kode',
        'nama_divisi',
        'batas_jam_masuk',
        'batas_jam_pulang',
    ];
}
