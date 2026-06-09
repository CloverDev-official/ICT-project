<?php

namespace App\Models\Murid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsenMurid extends Model
{
    use SoftDeletes;

    protected $table = 'absen_murid';

    protected $fillable = [
        'murid_id',
        'status',
        'waktu_masuk',
        'waktu_keluar',
        'keterangan',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'waktu_masuk' => 'string',
        'waktu_keluar' => 'string',
    ];

    /**
     * Relasi ke Murid
     */
    public function murid()
    {
        return $this->belongsTo(Murid::class)->withTrashed();
    }
}