<?php

namespace App\Models\Murid\Rombel;

use Illuminate\Database\Eloquent\Model;

class Tingkat extends Model
{
    protected $table = 'tingkat';

    protected $fillable = ['nama'];

    /**
     * Relasi ke Rombel
     */
    public function rombel()
    {
        return $this->hasMany(Rombel::class);
    }
}