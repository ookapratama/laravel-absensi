<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    protected $fillable = [
        'user_id',
        'gambar',
        'judul',
        'isi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return \Illuminate\Support\Facades\Storage::url($this->gambar);
        }
        return asset('assets/img/elements/18.jpg'); // Default placeholder
    }
}
