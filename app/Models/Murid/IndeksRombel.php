<?php

namespace App\Models\Murid;

use Illuminate\Database\Eloquent\Model;

class IndeksRombel extends Model
{
    protected $table = 'indeks_rombel';

    protected $fillable = ['nama'];

    public function rombel()
    {
        return $this->hasMany(Rombel::class);
    }
}