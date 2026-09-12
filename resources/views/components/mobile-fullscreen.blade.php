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
    x-show="mobile"
    style="display: none;"
    class="border-y border-gray-200 py-5">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-lg font-semibold text-gray-800">Tampilan Perangkat</h2>
        <button
            type="button"
            x-on:click="toggle()"
            x-bind:disabled="!supported"
            x-bind:aria-pressed="active"
            x-bind:title="active ? 'Keluar fullscreen' : 'Aktifkan fullscreen'"
            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-main disabled:cursor-not-allowed disabled:opacity-50">
            <iconify-icon x-bind:icon="active ? 'solar:quit-full-screen-linear' : 'solar:full-screen-linear'" width="20" height="20" class="shrink-0" aria-hidden="true"></iconify-icon>
            <span x-text="active ? 'Keluar Fullscreen' : 'Fullscreen'"></span>
        </button>
    </div>
    <p x-show="!supported" class="mt-3 text-sm text-gray-600">Browser ini tidak mendukung fullscreen.</p>
    <p x-show="error" x-text="error" role="alert" class="mt-3 text-sm text-rose-600"></p>
</section>
