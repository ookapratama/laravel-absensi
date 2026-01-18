<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Divisi extends Model
{
    use LogsActivity;

    protected $table = 'divisis';

    protected $fillable = [
        'kode',
        'nama',
        'jam_masuk',
        'jam_pulang',
        'toleransi_terlambat',
        'is_aktif',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
        'toleransi_terlambat' => 'integer',
        'is_aktif' => 'boolean',
    ];

    /**
     * Pegawai yang ada di divisi ini
     */
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }

    /**
     * Scope untuk divisi aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Jam masuk dengan toleransi
     */
    public function getJamMasukDenganToleransiAttribute()
    {
        if ($this->jam_masuk && $this->toleransi_terlambat > 0) {
            return $this->jam_masuk->copy()->addMinutes($this->toleransi_terlambat);
        }
        return $this->jam_masuk;
    }
}
