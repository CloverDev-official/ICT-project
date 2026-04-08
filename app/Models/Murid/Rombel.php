<?php

namespace App\Models\Murid;

use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    protected $table = 'rombel';

    protected $fillable = [
        'tingkat_kelas_id',
        'jurusan_id',
        'indeks_rombel_id',
    ];

    /**
     * Relasi ke Tingkat Kelas
     */
    public function tingkatKelas()
    {
        return $this->belongsTo(TingkatKelas::class);
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
    public function indeksRombel()
    {
        return $this->belongsTo(IndeksRombel::class);
    }

    /**
     * Relasi ke Murid
     */
    public function murid()
    {
        return $this->hasMany(Murid::class);
    }

    public function getNamaLengkapAttribute()
    {
        return optional($this->tingkatKelas)->nama .
            ' ' .
            optional($this->jurusan)->nama .
            ' ' .
            optional($this->indeksRombel)->nama;
    }
}
