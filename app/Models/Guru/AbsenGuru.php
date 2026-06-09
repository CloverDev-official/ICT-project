<?php

namespace App\Models\Guru;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsenGuru extends Model
{
    use SoftDeletes;

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
        return $this->belongsTo(Guru::class)->withTrashed();
    }
}