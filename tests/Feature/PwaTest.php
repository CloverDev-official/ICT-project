<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

class PwaTest extends TestCase
{
    public function test_guest_can_load_installable_manifest_with_site_name_and_valid_icons(): void
    {
        View::share('siteSettings', ['nama_website' => 'Absensi Sekolah']);

        $response = $this->get('/manifest.webmanifest');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json')
            ->assertJsonPath('name', 'Absensi Sekolah')
            ->assertJsonPath('display', 'standalone')
            ->assertJsonPath('start_url', './')
            ->assertJsonPath('scope', './');

        foreach ($response->json('icons') as $icon) {
            $dimensions = getimagesize(public_path($icon['src']));
            $this->assertSame($icon['sizes'], $dimensions[0].'x'.$dimensions[1]);
            $this->assertSame('image/png', $dimensions['mime']);
        }
    }
}
