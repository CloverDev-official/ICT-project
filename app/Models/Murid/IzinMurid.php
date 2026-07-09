<?php

namespace App\Models\Murid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class IzinMurid extends Model
{
    use SoftDeletes;

    protected $table = 'izin_murid';

    protected $fillable = [
        'murid_id',
        'uuid',
        'alasan',
        'tanggal',
        'dari_jam',
        'sampai_jam',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'dari_jam' => 'string',
        'sampai_jam' => 'string',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi ke Murid.
     */
    public function murid()
    {
        return $this->belongsTo(Murid::class)->withTrashed();
    }
}
