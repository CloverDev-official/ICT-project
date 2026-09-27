<?php

namespace Tests\Feature;

use App\Livewire\Components\SearchableSelect;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilterSelectTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_render_with_the_existing_searchable_select(): void
    {
        foreach ([
            \Modules\DataMaster\Livewire\DataGuru\Index::class,
            \Modules\Manajemen\Livewire\User\Manajemen::class,
            \Modules\Laporan\Livewire\Laporan\RiwayatAbsen\Murid\Riwayat::class,
            \Modules\Laporan\Livewire\Laporan\RekapAbsen\Murid\Rekap::class,
            \Modules\Laporan\Livewire\Laporan\Pengawas\IzinKeluar\Cetak::class,
            \Modules\DataMaster\Livewire\DataMurid\Create::class,
        ] as $component) {
            Livewire::test($component)->assertSeeLivewire(SearchableSelect::class);
        }
    }

    public function test_class_form_keeps_load_more_and_optional_selections(): void
    {
        Livewire::test(\Modules\DataMaster\Livewire\DataKelas\Create::class)
            ->assertSeeLivewire(SearchableSelect::class)
            ->assertSee('Angkatan / Tahun Masuk')
            ->call('loadMoreTingkat')
            ->assertSet('tingkatAdditionalOptions', 1)
            ->call('loadMoreIndeks')
            ->assertSet('indeksAdditionalOptions', 3)
            ->set('tingkat_id', 'X')
            ->set('indeks_id', null)
            ->set('guru_id', null)
            ->set('tahun_masuk', 2026)
            ->assertHasNoErrors();
    }

    public function test_edit_class_preselects_saved_values(): void
    {
        $rombel = Rombel::create([
            'tahun_masuk' => '2026',
            'tingkat_id' => Tingkat::create(['nama' => 'X'])->id,
            'jurusan_id' => Jurusan::create(['nama' => 'RPL'])->id,
            'indeks_id' => Indeks::create(['nama' => 'A'])->id,
        ]);
        Livewire::test(\Modules\DataMaster\Livewire\DataKelas\Edit::class, ['rombelId' => $rombel->id])
            ->assertSeeLivewire(SearchableSelect::class)
            ->assertSet('tingkat_id', $rombel->tingkat_id)
            ->assertSet('jurusan_id', $rombel->jurusan_id)
            ->assertSet('indeks_id', $rombel->indeks_id)
            ->assertSet('tahun_masuk', 2026);
    }

    public function test_letter_class_filter_still_clears_student_selection(): void
    {
        Livewire::test(\Modules\Laporan\Livewire\Laporan\Pengawas\IzinKeluar\Cetak::class)
            ->set('muridSearch', 'Ahmad')
            ->set('tingkatId', 1)
            ->assertSet('muridSearch', '')
            ->assertSet('muridId', null)
            ->set('tingkatId', null)
            ->assertHasNoErrors();
    }
}
