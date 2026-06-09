<?php

namespace App\Models\Guru\Rombel;

use App\Models\Guru\Guru;
use App\Models\Murid\Rombel\Rombel;
use Illuminate\Database\Eloquent\Model;

class GuruRombel extends Model
{
    protected $table = 'guru_rombel';

    protected $fillable = ['guru_id', 'rombel_id'];

    /**
     * Relasi ke Guru
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class)->withTrashed();
    }

    /**
     * Relasi ke Rombel
     */
    public function rombel()
    {
        return $this->belongsTo(Rombel::class)->withTrashed();
    }
}
