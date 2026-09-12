<section
    x-data="{
        mobile: /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
        active: false,
        open: false,
        error: '',
        supported: false,
        sync() {
            this.active = !!(document.fullscreenElement || document.webkitFullscreenElement);
        },
        init() {
            const root = document.documentElement;
            this.supported = !!((root.requestFullscreen && document.fullscreenEnabled !== false) || (root.webkitRequestFullscreen && document.webkitFullscreenEnabled !== false));
            this.sync();
        },
        toggle() {
            this.error = '';
            this.sync();
            const target = this.active ? document : document.documentElement;
            const action = this.active
                ? (document.exitFullscreen || document.webkitExitFullscreen)
                : (target.requestFullscreen || target.webkitRequestFullscreen);
            if (!action) {
                this.error = 'Browser ini tidak mendukung fullscreen.';
                return;
            }
            try {
                const result = action.call(target);
                if (result && typeof result.catch === 'function') {
                    result.catch(() => { this.error = 'Fullscreen ditolak oleh browser. Silakan coba lagi.'; });
                }
            } catch (error) {
                this.error = 'Fullscreen tidak dapat diaktifkan pada browser ini.';
            }
        }
    }"
    x-on:fullscreenchange.document="sync()"
    x-on:webkitfullscreenchange.document="sync()"
    x-on:fullscreenerror.document="error = 'Fullscreen ditolak oleh browser. Silakan coba lagi.'"
    x-on:webkitfullscreenerror.document="error = 'Fullscreen ditolak oleh browser. Silakan coba lagi.'"
    x-on:click.outside="open = false"
    x-on:keydown.escape.stop="open = false; $refs.trigger.focus()"
    aria-label="Kontrol halaman scan"
    class="fixed bottom-5 right-5 z-40 flex max-w-[calc(100%-2.5rem)] flex-col items-end gap-3">
    <p x-show="error" style="display: none;" x-text="error" role="alert" class="max-w-64 rounded-lg bg-white p-3 text-sm text-rose-600 shadow-lg"></p>
    <div id="scan-floating-options" x-show="open" style="display: none;" class="w-40 max-w-full overflow-hidden rounded-full border border-gray-200 bg-white p-1 shadow-xl">
        <button
            type="button"
            x-show="mobile"
            x-on:click="toggle(); open = false; $refs.trigger.focus()"
            x-bind:disabled="!supported"
            x-bind:aria-pressed="active"
            x-bind:title="active ? 'Keluar fullscreen' : 'Aktifkan fullscreen'"
            x-bind:aria-label="!supported ? 'Browser ini tidak mendukung fullscreen' : (active ? 'Keluar Fullscreen' : 'Fullscreen')"
            class="flex items-center justify-center gap-3 rounded-full px-4 py-2 text-left text-sm font-semibold text-gray-800 hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-blue-600 disabled:cursor-not-allowed disabled:opacity-50">
            <iconify-icon x-bind:icon="active ? 'solar:quit-full-screen-linear' : 'solar:full-screen-linear'" width="24" height="24" aria-hidden="true"></iconify-icon>
            <span x-text="!supported ? 'Fullscreen tidak tersedia' : (active ? 'Keluar Fullscreen' : 'Fullscreen Mode')"></span>
        </button>
        <a href="{{ route('pilih-absen') }}"
            aria-label="Kembali"
            title="Kembali"
            class="flex items-center justify-center gap-3 rounded-full px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-blue-600">
            <iconify-icon icon="solar:arrow-left-linear" width="24" height="24" aria-hidden="true"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>
    <button
        x-ref="trigger"
        type="button"
        x-on:click="open = !open"
        x-bind:aria-expanded="open"
        aria-controls="scan-floating-options"
        x-bind:aria-label="open ? 'Tutup opsi scan' : 'Buka opsi scan'"
        x-bind:title="open ? 'Tutup opsi scan' : 'Buka opsi scan'"
        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
        <iconify-icon x-bind:icon="open ? 'solar:close-circle-linear' : 'solar:menu-dots-bold'" width="26" height="26" aria-hidden="true"></iconify-icon>
    </button>
</section>
