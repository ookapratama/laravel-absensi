<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Kantor extends Model
{
    use LogsActivity;

    protected $table = 'kantors';

    protected $fillable = [
        'nama',
        'kode',
        'alamat',
        'latitude',
        'longitude',
        'radius_meter',
        'is_aktif',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meter' => 'integer',
        'is_aktif' => 'boolean',
    ];

    /**
     * Pegawai yang berkantor di sini (kantor utama)
     */
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }

    /**
     * Pegawai yang bisa absen di lokasi ini (many-to-many)
     */
    public function pegawaiAbsen()
    {
        return $this->belongsToMany(Pegawai::class, 'lokasi_absen_pegawais')
            ->withPivot('is_aktif')
            ->withTimestamps();
    }

    /**
     * Scope untuk kantor aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
