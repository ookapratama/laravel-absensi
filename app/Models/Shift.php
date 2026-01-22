<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Shift extends Model
{
    use LogsActivity;

    protected $fillable = [
        'divisi_id',
        'nama',
        'jam_masuk',
        'jam_pulang',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
