<?php

namespace App\Models\Murid;

use App\Models\Murid\Rombel\Rombel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{

    use HasFactory;
    protected $table = 'murid';

    protected $fillable = [
        'public_id',
        'nama',
        'nipd',
        'jk',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'hp',
        'email',
        'nama_ayah',
        'nama_ibu',
        'nama_wali',
        'rombel_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->public_id) {
                $model->public_id = \Str::ulid();
            }
        });
    }

    /**
     * Relasi ke Rombel
     */
    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    /**
     * Relasi ke status absen
     */
    public function statusAbsen()
    {
        return $this->hasMany(AbsenMurid::class);
    }
}
