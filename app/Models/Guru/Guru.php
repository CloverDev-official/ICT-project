<?php

namespace App\Models\Guru;

use App\Models\Murid\Rombel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Guru extends Model
{
    use HasUlids;

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
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi ke Rombel (Many to Many)
     */
    public function rombel()
    {
        return $this->belongsToMany(Rombel::class, 'guru_rombel')
                    ->withPivot('role')
                    ->withTimestamps();
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
        return $this->hasMany(StatusAbsenGuru::class);
    }
}