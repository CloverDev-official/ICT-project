<?php

namespace App\Models\Murid;

use Illuminate\Database\Eloquent\Model;

class StatusAbsenMurid extends Model
{
    protected $table = 'status_absen_murid';

    protected $fillable = [
        'murid_id',
        'status',
        'waktu_masuk',
        'waktu_keluar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_masuk' => 'datetime:H:i:s',
        'waktu_keluar' => 'datetime:H:i:s',
    ];

    /**
     * Relasi ke Murid
     */
    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }
}