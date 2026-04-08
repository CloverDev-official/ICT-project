<?php

namespace App\Models\Murid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Murid extends Model
{
    use HasUlids;

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
        return $this->hasMany(StatusAbsenMurid::class);
    }
}