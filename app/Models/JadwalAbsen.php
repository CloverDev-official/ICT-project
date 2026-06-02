<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalAbsen extends Model
{
    protected $table = 'jadwal_absen';

    protected $fillable = [
        'tanggal',
        'nama_acara',
        'jam_masuk',
        'jam_pulang',
        'tipe',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'jam_masuk' => 'string',
        'jam_pulang' => 'string',
    ];

    public function rombelJadwal()
    {
        return $this->hasMany(JadwalAbsenRombel::class, 'jadwal_absen_id');
    }
}
