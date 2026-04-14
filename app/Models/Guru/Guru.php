<?php

namespace App\Models\Guru;

use App\Models\Guru\Rombel\GuruRombel;
use App\Models\Guru\Rombel\RoleGuruRombel;
use App\Models\Murid\Rombel\Rombel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $fillable = [
        'public_id',
        'nama',
        'nuptk',
        'jk',
        'tempat_lahir',
        'tanggal_lahir',
        'nip',
        'status_kepegawaian',
        'jenis_ptk',
        'agama',
        'alamat_jalan',
        'rt',
        'rw',
        'desa_kelurahan',
        'kecamatan',
        'kode_pos',
        'telepon',
        'hp',
        'email',
        'password'
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
     * Relasi ke Rombel (Many to Many)
     */
    public function rombel()
    {
        return $this->belongsToMany(Rombel::class, 'guru_rombel')->withTimestamps();
    }

    /**
     * Relasi ke pivot (kalau mau akses langsung tabelnya)
     */
    public function guruRombel()
    {
        return $this->hasMany(GuruRombel::class);
    }

    /**
     * Relasi ke status absen
     */
    public function statusAbsen()
    {
        return $this->hasMany(AbsenGuru::class);
    }

    public function roleGuruRombel()
    {
        return $this->hasManyThrough(
            RoleGuruRombel::class,
            GuruRombel::class,
            'guru_id',
            'guru_rombel_id',
            'id',
            'id'
        );
    }
}
