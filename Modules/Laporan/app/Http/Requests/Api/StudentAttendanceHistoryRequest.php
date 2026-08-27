<?php

namespace Modules\Laporan\Http\Requests\Api;

use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Laporan\Services\StudentAttendance\StudentAttendanceAccessScope;

class StudentAttendanceHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user('web');

        return $user instanceof User && $user->canAccess('riwayat-murid');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'search' => ['nullable', 'string', 'max:150'],
            'murid_id' => ['nullable', 'integer', 'min:1'],
            'jurusan' => ['nullable', 'string', 'max:191'],
            'indeks' => ['nullable', 'string', 'max:191'],
            'tingkat' => ['nullable', 'string', 'max:191'],
            'jurusan_id' => ['prohibited'],
            'indeks_id' => ['prohibited'],
            'tingkat_id' => ['prohibited'],
            'status' => ['prohibited'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'sort_by' => ['nullable', 'string', Rule::in(['tanggal', 'nama', 'status', 'id'])],
            'sort_direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $user = $this->user('web');

            if (! $user instanceof User) {
                return;
            }

            $accessibleRombelIds = app(StudentAttendanceAccessScope::class)
                ->accessibleRombelIds($user);

            $this->validateMurid($validator, $accessibleRombelIds);
            $this->validateScopedRombelFilters($validator, $accessibleRombelIds);
        });
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['search', 'jurusan', 'indeks', 'tingkat', 'sort_by', 'sort_direction'] as $key) {
            if (! $this->exists($key)) {
                continue;
            }

            $value = $this->input($key);

            if (! is_string($value)) {
                continue;
            }

            $value = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);
            $normalized[$key] = $value === '' ? null : $value;
        }

        foreach (['sort_by', 'sort_direction'] as $key) {
            if (isset($normalized[$key])) {
                $normalized[$key] = mb_strtolower($normalized[$key]);
            }
        }

        $this->merge($normalized);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'page.integer' => 'Page harus berupa angka bulat.',
            'page.min' => 'Page minimal bernilai 1.',
            'per_page.integer' => 'Per page harus berupa angka bulat.',
            'per_page.min' => 'Per page minimal bernilai 1.',
            'per_page.max' => 'Per page maksimal bernilai 100.',
            'search.max' => 'Pencarian maksimal 150 karakter.',
            '*.exists' => 'Filter :attribute tidak valid.',
            'jurusan_id.prohibited' => 'Gunakan parameter jurusan dengan nama jurusan.',
            'indeks_id.prohibited' => 'Gunakan parameter indeks dengan nama indeks.',
            'tingkat_id.prohibited' => 'Gunakan parameter tingkat dengan nama tingkat.',
            'status.prohibited' => 'Filter status tidak didukung. Endpoint selalu menampilkan seluruh status.',
            'date_from.date_format' => 'Date from harus menggunakan format Y-m-d.',
            'date_to.date_format' => 'Date to harus menggunakan format Y-m-d.',
            'date_to.after_or_equal' => 'Date to tidak boleh lebih awal daripada date from.',
            'sort_by.in' => 'Kolom sorting tidak valid.',
            'sort_direction.in' => 'Arah sorting hanya boleh asc atau desc.',
        ];
    }

    /** @param array<int, int>|null $accessibleRombelIds */
    private function validateMurid(Validator $validator, ?array $accessibleRombelIds): void
    {
        if (! $this->filled('murid_id') || ! is_numeric($this->input('murid_id'))) {
            return;
        }

        $query = Murid::query()
            ->aktif()
            ->whereKey((int) $this->input('murid_id'));

        if ($accessibleRombelIds !== null) {
            $query->whereIn('rombel_id', $accessibleRombelIds);
        }

        if (! $query->exists()) {
            $validator->errors()->add('murid_id', 'Filter murid_id tidak valid.');
        }
    }

    /** @param array<int, int>|null $accessibleRombelIds */
    private function validateScopedRombelFilters(
        Validator $validator,
        ?array $accessibleRombelIds,
    ): void {
        $filters = [
            'tingkat' => ['relation' => 'tingkat', 'table' => 'tingkat'],
            'jurusan' => ['relation' => 'jurusan', 'table' => 'jurusan'],
            'indeks' => ['relation' => 'indeks', 'table' => 'indeks'],
        ];

        foreach ($filters as $input => $reference) {
            if (! $this->filled($input) || ! is_string($this->input($input))) {
                continue;
            }

            $query = Rombel::query();

            if ($accessibleRombelIds !== null) {
                $query->whereIn('id', $accessibleRombelIds);
            }

            $isAccessible = $query
                ->whereHas($reference['relation'], function ($query) use ($input, $reference) {
                    $query->whereRaw(
                        "LOWER({$reference['table']}.nama) = ?",
                        [mb_strtolower((string) $this->input($input))],
                    );
                })
                ->exists();

            if (! $isAccessible) {
                $validator->errors()->add($input, "Filter {$input} tidak valid.");
            }
        }
    }
}
