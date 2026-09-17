<section
    x-data="{
        mobile: /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
        active: false,
        appFullscreen: false,
        open: false,
        error: '',
        supported: false,
        autoFullscreenRequest: false,
        sync() {
            this.active = !!(document.fullscreenElement || document.webkitFullscreenElement);
            // Fullscreen aplikasi terpasang tidak mengisi document.fullscreenElement.
            this.appFullscreen = window.matchMedia('(display-mode: fullscreen)').matches;
        },
        init() {
            const root = document.documentElement;
            this.supported = !!((root.requestFullscreen && document.fullscreenEnabled !== false) || (root.webkitRequestFullscreen && document.webkitFullscreenEnabled !== false));
            this.sync();
            this.enterFullscreen(false);
        },
        enterFullscreen(showError = true) {
            this.sync();
            if (!this.supported || this.active || this.appFullscreen) {
                return;
            }

            const target = document.documentElement;
            const action = target.requestFullscreen || target.webkitRequestFullscreen;

            if (!action) {
                if (showError) this.error = 'Browser ini tidak mendukung fullscreen.';
                return;
            }

            try {
                this.autoFullscreenRequest = !showError;
                const result = action.call(target);
                if (result && typeof result.catch === 'function') {
                    result
                        .catch(() => {
                            if (showError) this.error = 'Fullscreen ditolak oleh browser. Silakan coba lagi.';
                        })
                        .finally(() => { this.autoFullscreenRequest = false; });
                } else {
                    this.autoFullscreenRequest = false;
                }
            } catch (error) {
                this.autoFullscreenRequest = false;
                if (showError) this.error = 'Fullscreen tidak dapat diaktifkan pada browser ini.';
            }
        },
        toggle() {
            this.error = '';
            this.sync();
            if (!this.active) {
                this.enterFullscreen();
                return;
            }

            const action = document.exitFullscreen || document.webkitExitFullscreen;
            if (!action) {
                this.error = 'Browser ini tidak mendukung fullscreen.';
                return;
            }
            try {
                const result = action.call(document);
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
    x-on:fullscreenerror.document="if (!autoFullscreenRequest) error = 'Fullscreen ditolak oleh browser. Silakan coba lagi.'"
    x-on:webkitfullscreenerror.document="if (!autoFullscreenRequest) error = 'Fullscreen ditolak oleh browser. Silakan coba lagi.'"
    x-on:click.window.once="if (!$el.contains($event.target)) enterFullscreen(false)"
    x-on:click.outside="open = false"
    x-on:keydown.escape.stop="open = false; $refs.trigger.focus()"
    aria-label="Kontrol halaman scan"
    class="fixed bottom-[calc(1.25rem+env(safe-area-inset-bottom,0px))] right-[calc(1.25rem+env(safe-area-inset-right,0px))] z-40 flex max-w-[calc(100vw-2.5rem-env(safe-area-inset-left,0px)-env(safe-area-inset-right,0px))] flex-col items-end gap-3">
    <p
        x-show="error"
        style="display: none;"
        x-text="error"
        x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        role="alert"
        aria-atomic="true"
        class="max-w-72 rounded-2xl border border-rose-100 bg-white/90 px-4 py-3 text-sm leading-relaxed text-rose-600 shadow-lg backdrop-blur-xl dark:border-rose-400/20 dark:bg-gray-900/90 dark:text-rose-300"></p>

    <div
        id="scan-floating-options"
        x-show="open"
        style="display: none;"
        x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        class="flex max-w-full origin-bottom-right flex-col items-end gap-2.5">
        <button
            type="button"
            x-show="mobile && open && !appFullscreen"
            x-transition:enter="transition ease-out duration-200 delay-75 motion-reduce:transition-none"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100 motion-reduce:transition-none"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            x-on:click="toggle(); open = false; $refs.trigger.focus()"
            x-bind:disabled="!supported"
            x-bind:aria-pressed="active"
            x-bind:title="!supported ? 'Browser ini tidak mendukung fullscreen' : (active ? 'Keluar fullscreen' : 'Aktifkan fullscreen')"
            x-bind:aria-label="!supported ? 'Browser ini tidak mendukung fullscreen' : (active ? 'Keluar Fullscreen' : 'Fullscreen')"
            class="flex min-h-11 max-w-full items-center gap-2.5 rounded-full border border-white/60 bg-white/85 px-4 py-2.5 text-sm font-medium text-gray-800 shadow-lg shadow-gray-900/10 backdrop-blur-xl transition duration-200 enabled:hover:bg-white/95 enabled:active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none dark:border-white/10 dark:bg-gray-900/85 dark:text-gray-100 dark:enabled:hover:bg-gray-800/95 dark:focus-visible:ring-offset-gray-900">
            <iconify-icon x-bind:icon="active ? 'solar:quit-full-screen-linear' : 'solar:full-screen-linear'" width="20" height="20" aria-hidden="true" class="shrink-0 text-blue-600 dark:text-blue-300"></iconify-icon>
            <span x-text="active ? 'Keluar Fullscreen' : 'Fullscreen'"></span>
        </button>

        <button
            type="button"
            x-on:click="window.location.reload()"
            class="flex min-h-11 max-w-full items-center gap-2.5 rounded-full border border-white/60 bg-white/85 px-4 py-2.5 text-sm font-medium text-gray-800 shadow-lg backdrop-blur-xl hover:bg-white/95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-white/10 dark:bg-gray-900/85 dark:text-gray-100">
            <iconify-icon icon="solar:refresh-linear" width="20" height="20" aria-hidden="true" class="shrink-0 text-blue-600 dark:text-blue-300"></iconify-icon>
            <span>Muat ulang</span>
        </button>

        <a href="{{ route('pilih-absen') }}"
            aria-label="Kembali"
            title="Kembali"
            class="flex min-h-11 max-w-full items-center gap-2.5 rounded-full border border-white/60 bg-white/85 px-4 py-2.5 text-sm font-medium text-gray-800 shadow-lg shadow-gray-900/10 backdrop-blur-xl transition duration-200 hover:bg-white/95 active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 motion-reduce:transition-none dark:border-white/10 dark:bg-gray-900/85 dark:text-gray-100 dark:hover:bg-gray-800/95 dark:focus-visible:ring-offset-gray-900">
            <iconify-icon icon="solar:arrow-left-linear" width="20" height="20" aria-hidden="true" class="shrink-0 text-blue-600 dark:text-blue-300"></iconify-icon>
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
        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-white/30 bg-blue-600/90 text-white shadow-lg shadow-blue-900/20 backdrop-blur-xl transition duration-200 hover:scale-105 hover:bg-blue-600 active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 motion-reduce:transition-none dark:border-white/15 dark:focus-visible:ring-offset-gray-900">
        <iconify-icon
            x-bind:icon="open ? 'solar:close-circle-linear' : 'solar:menu-dots-bold'"
            x-bind:class="open ? 'rotate-90' : 'rotate-0'"
            width="24"
            height="24"
            aria-hidden="true"
            class="transition-transform duration-200 motion-reduce:transition-none"></iconify-icon>
    </button>
</section>
