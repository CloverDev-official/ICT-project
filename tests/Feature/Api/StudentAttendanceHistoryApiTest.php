<?php

namespace Tests\Feature\Api;

use App\Enums\AttendanceStatus;
use App\Enums\StudentStatus;
use App\Models\Guru\Guru;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class StudentAttendanceHistoryApiTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->create([
            'id' => 1,
            'name' => 'Super Admin',
            'permissions' => [],
        ]);
    }

    public function test_anonymous_user_receives_401_for_list_and_summary(): void
    {
        $this->getJson('/api/riwayat-absensi-murid')->assertUnauthorized();
        $this->getJson('/api/riwayat-absensi-murid/summary')->assertUnauthorized();
    }

    public function test_authenticated_user_without_page_access_receives_403(): void
    {
        $user = $this->createUser('Operator', []);

        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid')
            ->assertForbidden();

        $this->getJson('/api/riwayat-absensi-murid/summary')
            ->assertForbidden();
    }

    public function test_authorized_user_can_list_only_the_documented_fields(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('rpl-a');
        $murid = $this->createMurid($rombel, 'Ahmad Fauzan', '001234', '0098765432');
        $attendance = $this->createAttendance($murid, AttendanceStatus::Hadir, '2026-08-27');

        $response = $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', $attendance->id)
            ->assertJsonPath('data.0.murid.nama', 'Ahmad Fauzan')
            ->assertJsonPath('data.0.murid.nipd', '001234')
            ->assertJsonPath('data.0.murid.nisn', '0098765432')
            ->assertJsonPath('data.0.status.code', 'hadir')
            ->assertJsonPath('data.0.tanggal', '2026-08-27')
            ->assertJsonPath('meta.per_page', 20)
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'murid' => ['id', 'uuid', 'nama', 'nipd', 'nisn'],
                    'jurusan' => ['id', 'nama'],
                    'tingkat' => ['id', 'nama'],
                    'indeks' => ['id', 'nama'],
                    'status' => ['code', 'label'],
                    'tanggal',
                ]],
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
                'links' => ['first', 'last', 'previous', 'next'],
            ]);

        $payload = $response->json('data.0');
        $this->assertArrayNotHasKey('password', $payload['murid']);
        $this->assertArrayNotHasKey('email', $payload['murid']);
        $this->assertArrayNotHasKey('alamat', $payload['murid']);
        $this->assertArrayNotHasKey('keterangan', $payload);
    }

    public function test_pagination_has_limits_and_deterministic_default_order(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('pagination');
        $murid = $this->createMurid($rombel, 'Pagination Student');
        $oldest = $this->createAttendance($murid, AttendanceStatus::Izin, '2026-08-01');
        $middle = $this->createAttendance($murid, AttendanceStatus::Sakit, '2026-08-02');
        $latest = $this->createAttendance($murid, AttendanceStatus::Hadir, '2026-08-03');

        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid?per_page=2&page=1')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $latest->id)
            ->assertJsonPath('data.1.id', $middle->id)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.last_page', 2);

        $this->getJson('/api/riwayat-absensi-murid?per_page=2&page=2')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $oldest->id);

        $this->getJson('/api/riwayat-absensi-murid?per_page=101')->assertUnprocessable();
        $this->getJson('/api/riwayat-absensi-murid?per_page=0')->assertUnprocessable();
        $this->getJson('/api/riwayat-absensi-murid?page=0')->assertUnprocessable();
    }

    public function test_jurusan_indeks_tingkat_and_murid_filters_can_be_combined(): void
    {
        $user = $this->createAuthorizedUser();
        $matchingRombel = $this->createRombel('matching');
        $otherRombel = $this->createRombel('other');
        $matchingStudent = $this->createMurid($matchingRombel, 'Matching Student');
        $otherStudent = $this->createMurid($otherRombel, 'Other Student');
        $matching = $this->createAttendance($matchingStudent, AttendanceStatus::Hadir, '2026-08-27');
        $this->createAttendance($otherStudent, AttendanceStatus::Hadir, '2026-08-27');

        $query = http_build_query([
            'jurusan' => $matchingRombel->jurusan->nama,
            'indeks' => $matchingRombel->indeks->nama,
            'tingkat' => $matchingRombel->tingkat->nama,
            'murid_id' => $matchingStudent->id,
        ]);

        $this->actingAs($user)
            ->getJson("/api/riwayat-absensi-murid?{$query}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matching->id);
    }

    public function test_each_rombel_reference_filter_works_independently(): void
    {
        $user = $this->createAuthorizedUser();
        $matchingRombel = $this->createRombel('single-filter-matching');
        $otherRombel = $this->createRombel('single-filter-other');
        $matchingStudent = $this->createMurid($matchingRombel, 'Single Filter Matching');
        $otherStudent = $this->createMurid($otherRombel, 'Single Filter Other');
        $matching = $this->createAttendance($matchingStudent, AttendanceStatus::Hadir, '2026-08-27');
        $this->createAttendance($otherStudent, AttendanceStatus::Hadir, '2026-08-27');

        foreach (['jurusan', 'indeks', 'tingkat'] as $filter) {
            $this->actingAs($user)
                ->getJson('/api/riwayat-absensi-murid?'.http_build_query([
                    $filter => $matchingRombel->{$filter}->nama,
                ]))
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $matching->id);
        }
    }

    public function test_name_search_is_case_insensitive_partial_and_returns_all_matches(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('search-name');
        $students = [
            $this->createMurid($rombel, 'Ahmad Fauzan'),
            $this->createMurid($rombel, 'Muhammad Ahmad'),
            $this->createMurid($rombel, 'Ahmad Fauzan'),
        ];
        $unmatched = $this->createMurid($rombel, 'Budi Santoso');

        foreach ($students as $student) {
            $this->createAttendance($student, AttendanceStatus::Hadir, '2026-08-27');
        }
        $this->createAttendance($unmatched, AttendanceStatus::Hadir, '2026-08-27');

        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid?search=%20%20AHMAD%20%20')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_nipd_and_nisn_search_preserve_leading_zeroes_and_use_partial_matching(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('search-id');
        $student = $this->createMurid($rombel, 'Identifier Student', '0012345678', '0009876543');
        $this->createAttendance($student, AttendanceStatus::Hadir, '2026-08-27');

        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid?search=001234')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.murid.nipd', '0012345678');

        $this->getJson('/api/riwayat-absensi-murid?search=000987')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.murid.nisn', '0009876543');
    }

    public function test_search_escapes_wildcards_and_sql_injection_like_input_is_harmless(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('search-security');
        $normal = $this->createMurid($rombel, 'Normal Student');
        $literal = $this->createMurid($rombel, 'Student 100%_Valid');
        $this->createAttendance($normal, AttendanceStatus::Hadir, '2026-08-27');
        $this->createAttendance($literal, AttendanceStatus::Hadir, '2026-08-27');

        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid?search='.urlencode('%_'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.murid.nama', 'Student 100%_Valid');

        $this->getJson('/api/riwayat-absensi-murid?search='.urlencode("%' OR 1=1 --"))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_status_filter_is_removed_and_list_returns_every_status(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('status');
        $student = $this->createMurid($rombel, 'Status Student');
        $this->createAttendance($student, AttendanceStatus::Hadir, '2026-08-27');
        $this->createAttendance($student, AttendanceStatus::Izin, '2026-08-26');

        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/riwayat-absensi-murid?status=hadir')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    public function test_date_range_is_inclusive_and_rejects_an_inverted_range(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('date');
        $student = $this->createMurid($rombel, 'Date Student');
        $this->createAttendance($student, AttendanceStatus::Hadir, '2026-07-31');
        $this->createAttendance($student, AttendanceStatus::Hadir, '2026-08-01');
        $this->createAttendance($student, AttendanceStatus::Hadir, '2026-08-31');
        $this->createAttendance($student, AttendanceStatus::Hadir, '2026-09-01');

        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid?date_from=2026-08-01&date_to=2026-08-31')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/riwayat-absensi-murid?date_from=2026-09-01&date_to=2026-08-01')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('date_to');
    }

    public function test_sorting_is_whitelisted_and_name_sorting_is_stable(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('sorting');
        $zeta = $this->createMurid($rombel, 'Zeta Student');
        $alpha = $this->createMurid($rombel, 'Alpha Student');
        $this->createAttendance($zeta, AttendanceStatus::Hadir, '2026-08-27');
        $this->createAttendance($alpha, AttendanceStatus::Hadir, '2026-08-27');

        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid?sort_by=nama&sort_direction=asc')
            ->assertOk()
            ->assertJsonPath('data.0.murid.nama', 'Alpha Student')
            ->assertJsonPath('data.1.murid.nama', 'Zeta Student');

        $this->getJson('/api/riwayat-absensi-murid?sort_by='.urlencode('tanggal desc, password'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort_by');

        $this->getJson('/api/riwayat-absensi-murid?sort_direction=sideways')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort_direction');
    }

    public function test_summary_returns_every_valid_status_zero_counts_and_grand_total(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('summary');
        $student = $this->createMurid($rombel, 'Summary Student');
        $this->createAttendance($student, AttendanceStatus::Hadir, '2026-08-27');
        $this->createAttendance($student, AttendanceStatus::Hadir, '2026-08-26');
        $this->createAttendance($student, AttendanceStatus::Izin, '2026-08-25');

        $response = $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid/summary')
            ->assertOk()
            ->assertJsonPath('data.grand_total', 3)
            ->assertJsonCount(1, 'data.rombels')
            ->assertJsonPath('data.rombels.0.total', 3)
            ->assertJsonCount(count(AttendanceStatus::cases()), 'data.rombels.0.statuses');

        $statuses = collect($response->json('data.rombels.0.statuses'))->keyBy('code');
        $this->assertSame(2, $statuses['hadir']['total']);
        $this->assertSame(1, $statuses['izin']['total']);
        $this->assertSame(0, $statuses['masuk']['total']);
        $this->assertSame(0, $statuses['selesai']['total']);
    }

    public function test_summary_groups_matching_jurusan_by_each_rombel_name(): void
    {
        $user = $this->createAuthorizedUser();
        $tingkatXi = Tingkat::query()->create(['nama' => 'XI']);
        $tingkatX = Tingkat::query()->create(['nama' => 'X']);
        $jurusan = Jurusan::query()->create(['nama' => 'PPLG']);
        $indeksA = Indeks::query()->create(['nama' => 'A']);
        $indeksB = Indeks::query()->create(['nama' => 'B']);
        $rombels = [
            Rombel::query()->create(['tahun_masuk' => '2026', 'tingkat_id' => $tingkatXi->id, 'jurusan_id' => $jurusan->id, 'indeks_id' => $indeksA->id]),
            Rombel::query()->create(['tahun_masuk' => '2026', 'tingkat_id' => $tingkatXi->id, 'jurusan_id' => $jurusan->id, 'indeks_id' => $indeksB->id]),
            Rombel::query()->create(['tahun_masuk' => '2026', 'tingkat_id' => $tingkatX->id, 'jurusan_id' => $jurusan->id, 'indeks_id' => $indeksA->id]),
        ];

        foreach ($rombels as $index => $rombel) {
            $student = $this->createMurid($rombel, "PPLG Student {$index}");
            $this->createAttendance($student, AttendanceStatus::Hadir, '2026-08-27');
            $this->createAttendance($student, AttendanceStatus::Izin, '2026-08-26');
        }

        $response = $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid/summary?jurusan=pplg')
            ->assertOk()
            ->assertJsonCount(3, 'data.rombels')
            ->assertJsonPath('data.grand_total', 6);

        $this->assertEqualsCanonicalizing(
            ['X PPLG A', 'XI PPLG A', 'XI PPLG B'],
            collect($response->json('data.rombels'))->pluck('rombel')->all(),
        );

        $this->getJson('/api/riwayat-absensi-murid/summary?tingkat=X&jurusan=PPLG')
            ->assertOk()
            ->assertJsonCount(1, 'data.rombels')
            ->assertJsonPath('data.rombels.0.rombel', 'X PPLG A')
            ->assertJsonPath('data.rombels.0.total', 2)
            ->assertJsonPath('data.grand_total', 2);
    }

    public function test_summary_uses_the_same_combined_filters_as_the_list(): void
    {
        $user = $this->createAuthorizedUser();
        $matchingRombel = $this->createRombel('summary-matching');
        $otherRombel = $this->createRombel('summary-other');
        $matching = $this->createMurid($matchingRombel, 'Ahmad Matching');
        $other = $this->createMurid($otherRombel, 'Ahmad Other');
        $this->createAttendance($matching, AttendanceStatus::Sakit, '2026-08-15');
        $this->createAttendance($matching, AttendanceStatus::Sakit, '2026-07-15');
        $this->createAttendance($other, AttendanceStatus::Sakit, '2026-08-15');

        $query = http_build_query([
            'search' => 'Ahmad',
            'jurusan' => $matchingRombel->jurusan->nama,
            'indeks' => $matchingRombel->indeks->nama,
            'tingkat' => $matchingRombel->tingkat->nama,
            'date_from' => '2026-08-01',
            'date_to' => '2026-08-31',
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/riwayat-absensi-murid/summary?{$query}")
            ->assertOk()
            ->assertJsonPath('data.grand_total', 1);

        $statuses = collect($response->json('data.rombels.0.statuses'))->keyBy('code');
        $this->assertSame(1, $statuses['sakit']['total']);
    }

    public function test_wali_kelas_cannot_list_count_or_validate_another_rombel_data(): void
    {
        $wali = $this->createUser('Wali Kelas', ['riwayat-murid']);
        $guru = $this->createGuru($wali);
        $ownRombel = $this->createRombel('wali-own', $guru->id);
        $foreignRombel = $this->createRombel('wali-foreign');
        $ownStudent = $this->createMurid($ownRombel, 'Own Student');
        $foreignStudent = $this->createMurid($foreignRombel, 'Foreign Student');
        $ownAttendance = $this->createAttendance($ownStudent, AttendanceStatus::Hadir, '2026-08-27');
        $this->createAttendance($foreignStudent, AttendanceStatus::Alpa, '2026-08-27');

        $this->actingAs($wali)
            ->getJson('/api/riwayat-absensi-murid')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownAttendance->id);

        $this->getJson('/api/riwayat-absensi-murid/summary')
            ->assertOk()
            ->assertJsonPath('data.grand_total', 1);

        $this->getJson('/api/riwayat-absensi-murid?murid_id='.$foreignStudent->id)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('murid_id');

        $this->getJson('/api/riwayat-absensi-murid?'.http_build_query([
            'jurusan' => $foreignRombel->jurusan->nama,
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('jurusan');
    }

    public function test_invalid_reference_filters_are_rejected(): void
    {
        $user = $this->createAuthorizedUser();

        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid?jurusan=tidak-ada&indeks=tidak-ada&tingkat=tidak-ada&murid_id=99999')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['jurusan', 'indeks', 'tingkat', 'murid_id']);

        $this->getJson('/api/riwayat-absensi-murid?jurusan_id=1&indeks_id=1&tingkat_id=1')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['jurusan_id', 'indeks_id', 'tingkat_id']);
    }

    public function test_eager_loading_query_count_does_not_grow_with_page_size(): void
    {
        $user = $this->createAuthorizedUser();
        $rombel = $this->createRombel('query-count');
        $firstStudent = $this->createMurid($rombel, 'Query Student 1');
        $this->createAttendance($firstStudent, AttendanceStatus::Hadir, '2026-08-27');

        DB::enableQueryLog();
        $this->actingAs($user)
            ->getJson('/api/riwayat-absensi-murid?per_page=20')
            ->assertOk();
        $singleItemQueryCount = count(DB::getQueryLog());

        DB::flushQueryLog();

        foreach (range(2, 10) as $number) {
            $student = $this->createMurid($rombel, "Query Student {$number}");
            $this->createAttendance($student, AttendanceStatus::Hadir, '2026-08-27');
        }

        DB::flushQueryLog();
        $this->getJson('/api/riwayat-absensi-murid?per_page=20')
            ->assertOk()
            ->assertJsonCount(10, 'data');
        $tenItemQueryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertLessThanOrEqual($singleItemQueryCount + 1, $tenItemQueryCount);
    }

    public function test_scalar_documentation_is_protected_by_the_same_page_access(): void
    {
        $this->get('/scalar')->assertRedirect('/');

        $withoutAccess = $this->createUser('Operator Docs', []);
        $this->actingAs($withoutAccess)
            ->get('/scalar')
            ->assertForbidden();

        $withAccess = $this->createAuthorizedUser();
        $this->actingAs($withAccess)
            ->get('/scalar')
            ->assertOk()
            ->assertSee('API Riwayat Absensi Murid');
    }

    private function createAuthorizedUser(): User
    {
        return $this->createUser('API Reader '.$this->nextSequence(), ['riwayat-murid']);
    }

    /** @param array<int, string> $permissions */
    private function createUser(string $roleName, array $permissions): User
    {
        $role = Role::query()->create([
            'name' => $roleName,
            'permissions' => $permissions,
        ]);

        return User::query()->create([
            'name' => 'API Test User',
            'email' => 'api-user-'.$this->nextSequence().'@example.test',
            'password' => Hash::make('secret-password'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    private function createGuru(User $user): Guru
    {
        return Guru::query()->create([
            'public_id' => (string) Str::uuid(),
            'nama' => 'Guru '.$this->nextSequence(),
            'jk' => 'L',
            'user_id' => $user->id,
        ]);
    }

    private function createRombel(string $suffix, ?int $waliGuruId = null): Rombel
    {
        $sequence = $this->nextSequence();
        $tingkat = Tingkat::query()->create(['nama' => "Tingkat {$suffix} {$sequence}"]);
        $jurusan = Jurusan::query()->create(['nama' => "Jurusan {$suffix} {$sequence}"]);
        $indeks = Indeks::query()->create(['nama' => "Indeks {$suffix} {$sequence}"]);

        return Rombel::query()->create([
            'tahun_masuk' => '2026',
            'tingkat_id' => $tingkat->id,
            'jurusan_id' => $jurusan->id,
            'indeks_id' => $indeks->id,
            'wali_guru_id' => $waliGuruId,
        ]);
    }

    private function createMurid(
        Rombel $rombel,
        string $nama,
        ?string $nipd = null,
        ?string $nisn = null,
    ): Murid {
        $sequence = $this->nextSequence();

        return Murid::query()->create([
            'uuid' => (string) Str::uuid(),
            'nama' => $nama,
            'nipd' => $nipd ?? str_pad((string) $sequence, 10, '0', STR_PAD_LEFT),
            'jk' => 'L',
            'nisn' => $nisn ?? str_pad((string) ($sequence + 500000), 10, '0', STR_PAD_LEFT),
            'tempat_lahir' => 'Makassar',
            'tanggal_lahir' => '2010-01-01',
            'rombel_id' => $rombel->id,
            'status' => StudentStatus::Aktif->value,
        ]);
    }

    private function createAttendance(
        Murid $murid,
        AttendanceStatus $status,
        string $date,
    ): AbsenMurid {
        return AbsenMurid::query()->create([
            'murid_id' => $murid->id,
            'status' => $status->value,
            'tanggal' => $date,
        ]);
    }

    private function nextSequence(): int
    {
        return ++$this->sequence;
    }
}
