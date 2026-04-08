<?php

namespace App\Models\Murid;

use Illuminate\Database\Eloquent\Model;

class TingkatKelas extends Model
{
    protected $table = 'tingkat_kelas';

    protected $fillable = ['nama'];

    /**
     * Relasi ke Rombel
     */
    public function rombel()
    {
        return $this->hasMany(Rombel::class);
    }
}