<?php

namespace App\Models\Guru\Rombel;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role;

class RoleGuruRombel extends Model
{
    protected $table = "role_guru_rombel";

    protected $fillable = ['guru_rombel_id', 'role_id'];

    public function guru_rombel()
    {
        return $this->belongsTo(GuruRombel::class, 'guru_rombel_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
