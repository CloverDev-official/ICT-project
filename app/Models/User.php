<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    public function canAccess(string $permission): bool
    {
        if ($this->role?->id === 1) {
            return true;
        }

        $permissions = $this->role?->permissions;

        if (is_array($permissions)) {
            return in_array($permission, $permissions, true);
        }

        return false;
    }

    public function defaultRouteName(): string
    {
        $routeMap = [
            'dashboard' => 'dashboard',
            'pilih-absen' => 'pilih-absen',
            'laporan' => 'rekap-absen-murid',
            'rekap-absen-murid' => 'rekap-absen-murid',
            'rekap-absen-guru' => 'rekap-absen-guru',
            'riwayat' => 'riwayat-absen-murid',
            'absensi' => 'absensi-murid',
            'absensi-murid' => 'absensi-murid',
            'absensi-guru' => 'absensi-guru',
            'data-master' => 'data-murid',
            'data-murid' => 'data-murid',
            'data-guru' => 'data-guru',
            'data-kelas' => 'data-kelas',
            'data-jurusan' => 'data-jurusan',
            'manajemen' => 'manajemen-waktu',
            'generate-qr' => 'generate-QR',
            'manajemen-waktu' => 'manajemen-waktu',
            'manajemen-murid' => 'manajemen-murid',
            'manajemen-lainnya' => 'manajemen-tahun-ajaran',
            'manajemen-role' => 'manajemen-role',
            'manajemen-tahun-ajaran' => 'manajemen-tahun-ajaran',
            'pengaturan' => 'pengaturan',
            'profil' => 'profil',
        ];

        foreach ($routeMap as $permission => $routeName) {
            if ($this->canAccess($permission)) {
                return $routeName;
            }
        }

        return 'profil';
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
