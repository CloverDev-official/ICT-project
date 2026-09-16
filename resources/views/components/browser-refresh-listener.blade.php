<script>
    (() => {
        // Halaman scanner memakai ping yang sudah ada, sehingga tidak membuat request kedua.
        if (document.querySelector('[data-browser-refresh-via-ping]')) return;

        const endpoint = '/browser-refresh-version';
        let version = @json($version);
        let timer = null;
        let checking = false;

        const scheduleCheck = (delay = 5000) => {
            window.clearTimeout(timer);
            if (!document.hidden) timer = window.setTimeout(checkForRefresh, delay);
        };

        const checkForRefresh = async () => {
            if (document.hidden || checking) return;

            checking = true;
            try {
                const response = await fetch(endpoint, {
                    method: 'HEAD',
                    cache: 'no-store',
                    credentials: 'same-origin',
                });
                const signal = response.headers.get('X-Browser-Refresh-Version');

                if (response.ok && signal && signal !== version) {
                    window.location.reload();
                    return;
                }

                version = signal || version;
            } catch {
                // Kegagalan jaringan tidak mengganggu halaman yang sedang dipakai.
            } finally {
                checking = false;
                scheduleCheck();
            }
        };

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                window.clearTimeout(timer);
                return;
            }

            void checkForRefresh();
        });

        scheduleCheck();
    })();
</script>
