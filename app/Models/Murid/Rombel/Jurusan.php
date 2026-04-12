<?php

namespace App\Models\Murid\Rombel;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table = 'jurusan';

    protected $fillable = ['nama'];

    public function rombel()
    {
        return $this->hasMany(Rombel::class);
    }
}