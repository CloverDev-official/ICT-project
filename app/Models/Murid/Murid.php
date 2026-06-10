<?php

namespace App\Models\Murid;

use App\Models\Murid\Rombel\Rombel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Murid extends Model
{

    use HasFactory, SoftDeletes;
    protected $table = 'murid';

    protected $fillable = [
        'uuid',
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
        'kode_pos',
        'hp',
        'email',
        'nama_ayah',
        'nama_ibu',
        'nama_wali',
        'image_path',
        'rombel_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date:Y-m-d',
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
     * Relasi ke Rombel
     */
    public function rombel()
    {
        return $this->belongsTo(Rombel::class)->withTrashed();
    }

    /**
     * Relasi ke status absen
     */
    public function statusAbsen()
    {
        return $this->hasMany(AbsenMurid::class)->withTrashed();
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
