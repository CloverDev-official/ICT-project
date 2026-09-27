<?php

namespace Modules\Laporan\Services\StudentAttendance;

use App\Models\Guru\Guru;
use App\Models\Murid\Rombel\Rombel;
use App\Models\User;
use Illuminate\Support\Str;

class StudentAttendanceAccessScope
{
    /**
     * A null result means the existing page rules do not restrict the user to
     * specific homerooms. An empty array means a homeroom user has no class.
     *
     * @return array<int, int>|null
     */
    public function accessibleRombelIds(User $user): ?array
    {
        if (! $user->hasOnlyRoles(['Wali Kelas', 'Wali Murid'])) {
            return null;
        }

        $guruId = Guru::query()
            ->where('user_id', $user->id)
            ->value('id');

        if (! $guruId) {
            return [];
        }

        return Rombel::query()
            ->where('wali_guru_id', $guruId)
            ->orderBy('id')
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }
}
