<?php

namespace App\Models\Murid\Rombel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurusan extends Model
{
    use SoftDeletes;

    protected $table = 'jurusan';

    protected $fillable = ['nama'];

    public function rombel()
    {
        return $this->hasMany(Rombel::class)->withTrashed();
    }
}