<?php

namespace App\Models;

use App\Models\Murid\Rombel\Rombel;
use Illuminate\Database\Eloquent\Model;

class JadwalAbsenRombel extends Model
{
    protected $table = 'jadwal_absen_rombel';

    protected $fillable = [
        'jadwal_absen_id',
        'rombel_id',
        'tipe',
        'jam_masuk',
        'jam_pulang',
        'keterangan',
    ];

    protected $casts = [
        'jam_masuk' => 'string',
        'jam_pulang' => 'string',
    ];

    public function jadwalAbsen()
    {
        return $this->belongsTo(JadwalAbsen::class, 'jadwal_absen_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
}
