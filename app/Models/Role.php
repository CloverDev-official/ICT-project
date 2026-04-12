<?php

namespace App\Models;

use App\Models\Guru\Rombel\RoleGuruRombel;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';

    protected $fillable = ['nama'];

    public function roleGuruRombel()
    {
        return $this->hasMany(RoleGuruRombel::class, 'role_id');
    }

    public function admin(){
        return $this->hasMany(Admin::class, 'role_id');
    }
}
