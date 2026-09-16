<?php

namespace Modules\General\Livewire\RefreshAllBrowsers;

use App\Helpers\ToastMagic;
use App\Models\Setting;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public bool $showConfirmation = false;

    public function requestRefresh(): void
    {
        $this->ensureSuperAdmin();

        $this->showConfirmation = true;
    }

    public function cancelRefresh(): void
    {
        $this->showConfirmation = false;
    }

    public function confirmRefresh(): void
    {
        $this->ensureSuperAdmin();

        // UUID memastikan setiap konfirmasi selalu menghasilkan sinyal baru.
        Setting::upsertValue('system.browser_refresh_version', (string) Str::uuid());
        $this->showConfirmation = false;

        ToastMagic::success('Perintah refresh dikirim', 'Semua browser yang sedang membuka website akan dimuat ulang dalam beberapa detik.');
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless((int) auth()->user()?->role_id === 1, 403);
    }

    public function render()
    {
        return view('general::livewire.refresh-all-browsers.index');
    }
}
