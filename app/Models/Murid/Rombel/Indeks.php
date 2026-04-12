<?php

namespace App\Models\Murid\Rombel;

use Illuminate\Database\Eloquent\Model;

class Indeks extends Model
{
    protected $table = 'indeks';

    protected $fillable = ['nama'];

    public function rombel()
    {
        return $this->hasMany(Rombel::class);
    }
}