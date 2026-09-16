@props(['version' => ''])

<script>
    (() => {
        window.disposeBrowserRefreshListener?.();
        // Halaman scanner memakai ping yang sudah ada, sehingga tidak membuat request kedua.
        if (document.querySelector('[data-browser-refresh-via-ping]')) return;

        const endpoint = '/browser-refresh-version';
        let version = @json($version);
        let timer = null;
        let checking = false;
        let disposed = false;
        let retryAt = 0;
        let controller = null;
        const lifecycle = new AbortController();
        const dispose = () => {
            disposed = true;
            window.clearTimeout(timer);
            controller?.abort();
            lifecycle.abort();
        };
        window.disposeBrowserRefreshListener = dispose;
        document.addEventListener('livewire:navigating', dispose, { signal: lifecycle.signal });

        const scheduleCheck = (delay = 5000) => {
            window.clearTimeout(timer);
            if (!disposed && !document.hidden) {
                timer = window.setTimeout(checkForRefresh, Math.max(delay, retryAt - Date.now()));
            }
        };

        const checkForRefresh = async () => {
            if (disposed || document.hidden || checking) return;
            if (Date.now() < retryAt) {
                scheduleCheck();
                return;
            }

            checking = true;
            controller = new AbortController();
            const timeout = window.setTimeout(() => controller?.abort(), 4000);
            try {
                const response = await fetch(endpoint, {
                    method: 'HEAD',
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' },
                    signal: controller.signal,
                });
                if (disposed) return;
                if (response.status === 401 || response.status === 403) {
                    dispose();
                    return;
                }
                if (response.status === 429) {
                    const seconds = Number(response.headers.get('Retry-After'));
                    retryAt = Date.now() + (Number.isFinite(seconds) && seconds > 0 ? seconds : 60) * 1000;
                    return;
                }
                const signal = response.headers.get('X-Browser-Refresh-Version');

                if (response.ok && signal && signal !== version) {
                    dispose();
                    window.location.reload();
                    return;
                }

                version = signal || version;
            } catch {
                // Kegagalan jaringan tidak mengganggu halaman yang sedang dipakai.
            } finally {
                window.clearTimeout(timeout);
                controller = null;
                checking = false;
                scheduleCheck();
            }
        };

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                window.clearTimeout(timer);
                controller?.abort();
                return;
            }

            void checkForRefresh();
        }, { signal: lifecycle.signal });

        scheduleCheck();
    })();
</script>
