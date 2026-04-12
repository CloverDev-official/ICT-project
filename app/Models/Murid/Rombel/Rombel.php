<?php

namespace App\Models\Murid\Rombel;

use App\Models\Murid\Murid;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    protected $table = 'rombel';

    protected $fillable = [
        'tingkat_id',
        'jurusan_id',
        'indeks_id',
    ];

    /**
     * Relasi ke Tingkat Kelas
     */
    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class);
    }

    /**
     * Relasi ke Jurusan
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Relasi ke Indeks Rombel
     */
    public function indeks()
    {
        return $this->belongsTo(Indeks::class);
    }

    /**
     * Relasi ke Murid
     */
    public function murid()
    {
        return $this->hasMany(Murid::class);
    }
}
