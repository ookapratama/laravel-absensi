<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $fillable = [
        'nama_lengkap',
        'nik',
        'foto',
        'gender',
        'alamat',
        'no_telp',

        'user_id',
        'divisi_id',
        'kantor_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // public function divisi() {
    //     return $this->belongsTo(Divisi:class);
    // }

    // public function kantor() {
    //     return $this->belongsTo(Kantor:class);
    // }

}
