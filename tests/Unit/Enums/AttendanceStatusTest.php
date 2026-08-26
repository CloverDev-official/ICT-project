<?php

namespace Tests\Unit\Enums;

use App\Enums\AttendanceStatus;
use App\Enums\StudentStatus;
use PHPUnit\Framework\TestCase;

class AttendanceStatusTest extends TestCase
{
    public function test_status_values_match_the_existing_database_values(): void
    {
        $this->assertSame('Hadir', AttendanceStatus::Hadir->value);
        $this->assertSame('Izin', AttendanceStatus::Izin->value);
        $this->assertSame('Sakit', AttendanceStatus::Sakit->value);
        $this->assertSame('Alpa', AttendanceStatus::Alpa->value);
        $this->assertSame('Terlambat', AttendanceStatus::Terlambat->value);
        $this->assertSame('Selesai', AttendanceStatus::Selesai->value);
        $this->assertSame('aktif', StudentStatus::Aktif->value);
    }

    public function test_rekap_filter_values_keep_the_existing_order_and_casing(): void
    {
        $this->assertSame(
            ['sakit', 'izin', 'alpa', 'selesai'],
            AttendanceStatus::rekapLowercase(),
        );
    }

    public function test_edit_form_options_keep_the_existing_labels_and_values(): void
    {
        $options = AttendanceStatus::editFormOptions();

        $this->assertSame(
            ['hadir', 'sakit', 'izin', 'alpa', 'terlambat'],
            array_column($options, 'value'),
        );
        $this->assertSame(
            ['Hadir', 'Sakit', 'Izin', 'Alpa', 'Terlambat'],
            array_column($options, 'label'),
        );
    }
}
