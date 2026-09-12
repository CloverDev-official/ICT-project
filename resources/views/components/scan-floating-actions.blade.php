<section
    x-data="{
        mobile: /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
        active: false,
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
    aria-label="Kontrol halaman scan"
    class="fixed bottom-5 right-5 z-40 flex max-w-[calc(100%-2.5rem)] flex-col items-end gap-3">
    <p x-show="error" style="display: none;" x-text="error" role="alert" class="max-w-64 rounded-lg bg-white p-3 text-sm text-rose-600 shadow-lg"></p>
    <div x-show="mobile" style="display: none;" class="group relative">
        <button
            type="button"
            x-on:click="toggle()"
            x-bind:disabled="!supported"
            x-bind:aria-pressed="active"
            x-bind:title="active ? 'Keluar fullscreen' : 'Aktifkan fullscreen'"
            x-bind:aria-label="!supported ? 'Browser ini tidak mendukung fullscreen' : (active ? 'Keluar Fullscreen' : 'Fullscreen')"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white disabled:cursor-not-allowed disabled:opacity-50">
            <iconify-icon x-bind:icon="active ? 'solar:quit-full-screen-linear' : 'solar:full-screen-linear'" width="24" height="24" aria-hidden="true"></iconify-icon>
        </button>
        <span class="pointer-events-none absolute right-full top-1/2 mr-3 -translate-y-1/2 whitespace-nowrap rounded bg-white px-3 py-2 text-sm text-gray-800 opacity-0 shadow transition-opacity group-hover:opacity-100 group-focus-within:opacity-100" x-text="!supported ? 'Fullscreen tidak tersedia' : (active ? 'Keluar Fullscreen' : 'Fullscreen')"></span>
    </div>
    <div class="group relative">
        <a href="{{ route('pilih-absen') }}"
            aria-label="Kembali"
            title="Kembali"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-gray-800 shadow-lg hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
            <iconify-icon icon="solar:arrow-left-linear" width="24" height="24" aria-hidden="true"></iconify-icon>
        </a>
        <span class="pointer-events-none absolute right-full top-1/2 mr-3 -translate-y-1/2 whitespace-nowrap rounded bg-white px-3 py-2 text-sm text-gray-800 opacity-0 shadow transition-opacity group-hover:opacity-100 group-focus-within:opacity-100">Kembali</span>
    </div>
</section>
