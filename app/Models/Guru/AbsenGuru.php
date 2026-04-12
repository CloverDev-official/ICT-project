<?php

namespace App\Models\Guru;

use Illuminate\Database\Eloquent\Model;

class AbsenGuru extends Model
{
    protected $table = 'absen_guru';

    protected $fillable = [
        'guru_id',
        'status',
        'waktu_masuk',
        'waktu_keluar',
        'keterangan',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_masuk' => 'datetime:H:i:s',
        'waktu_keluar' => 'datetime:H:i:s',
    ];

    /**
     * Relasi ke Guru
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}