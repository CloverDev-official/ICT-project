<div class="fixed inset-0 bg-blue-dark flex flex-col overflow-y-auto scroll-hidden font-sans" data-browser-refresh-via-ping>
    @php
        $serverPingUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'scan-qrcode.ping',
            now()->addMinutes(10),
            [],
            false,
        );
    @endphp
    <header
        class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/80 px-4 py-4 shadow-2xl backdrop-blur-xl">

        <div class="mx-auto flex items-center max-w-7xl justify-between gap-4">

            <!-- brand -->
            <div class="flex items-center gap-4">

                <img
                    src="{{ asset('assets/img/logo_smkn_2.png') }}"
                    class="w-10"
                    alt="Logo SMKN 2 Banjarmasin">

                <div>
                    <h1 class="text-sm font-bold uppercase tracking-wide text-white">
                        Absensi QR
                    </h1>

                    <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400">
                        SMKN 2 Banjarmasin
                    </p>
                </div>

            </div>

            <!-- title -->
            <div class="hidden text-center md:block">

                <div
                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-5 py-2 text-sm font-semibold text-blue-100 backdrop-blur">

                    <div class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></div>
                    <span class="text-white">
                        {{ $dateNow ?? 'Tanggal sekarang' }},
                    </span>
                    <span class="text-white" id="clock">
                        {{ now()->format('H:i:s') ?? 'Waktu sekarang' }}
                    </span>

                </div>

            </div>

        </div>

    </header>

    @include('components.scan-floating-actions')

    <main class="flex items-center justify-center p-4 pb-40">
        <!-- SCANNER AREA -->
        <div class="w-full max-w-2xl">

            <!-- mobile label -->
            {{-- <div class="mb-5 block text-center md:hidden">

                <div
                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-5 py-2 text-sm font-semibold text-blue-100 backdrop-blur">

                    <div class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></div>

                    Absen Masuk

                </div>

                <p class="mt-2 text-xs text-slate-400">
                    {{ $dateNow ?? 'Tanggal sekarang' }}
                </p>

            </div> --}}

            <!-- scanner card -->
            <div
                class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 p-4 shadow-2xl backdrop-blur-xl">
                <!-- reader wrapper -->
                <div class="relative aspect-square w-full overflow-hidden rounded-[1.6rem] bg-black shadow-2xl">

                    <!-- corner frame -->
                    <div class="pointer-events-none absolute inset-0 z-20">
                        <div class="absolute left-4 top-4 h-12 w-12 rounded-tl-2xl border-l-4 border-t-4 border-blue-400"></div>
                        <div class="absolute right-4 top-4 h-12 w-12 rounded-tr-2xl border-r-4 border-t-4 border-blue-400"></div>
                        <div class="absolute bottom-4 left-4 h-12 w-12 rounded-bl-2xl border-b-4 border-l-4 border-blue-400"></div>
                        <div class="absolute bottom-4 right-4 h-12 w-12 rounded-br-2xl border-b-4 border-r-4 border-blue-400"></div>
                    </div>

                    <!-- center helper -->
                    <div
                        class="pointer-events-none absolute left-1/2 top-1/2 z-20 h-[58%] w-[58%] -translate-x-1/2 -translate-y-1/2 rounded-3xl border border-blue-400/40 bg-blue-400/5">
                    </div>

                    <!-- scan line -->
                    <div
                        class="pointer-events-none absolute left-[22%] top-1/2 z-20 h-0.5 w-[56%] -translate-y-1/2 animate-pulse rounded-full bg-blue-400 shadow-[0_0_25px_rgba(96,165,250,0.9)]">
                    </div>

                    <!-- camera -->
                    <div
                        wire:ignore
                        id="reader"
                        class="min-h-full overflow-hidden rounded-[1.6rem] bg-black text-white">
                    </div>

                </div>

                <!-- helper text -->
                <div class="mt-4 text-center">

                    <p class="text-sm font-medium text-slate-200">
                        Posisikan QR Code di tengah kotak scanner
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Sistem akan memproses otomatis setelah QR terbaca.
                    </p>

                    <p id="server-status" class="mt-2 hidden text-xs font-semibold text-red-400">
                        App tidak terhubung ke server
                    </p>

                </div>

            </div>

        </div>
    </main>

    <div wire:ignore data-scan-transport-host></div>
    @include('scanqr::components.modal.success')
    @include('scanqr::components.modal.message')
    @include('scanqr::components.modal.error')
    @include('scanqr::components.modal.transport-error')

</div>


@script
    <script>
        const serverPingUrl = @json($serverPingUrl);
        let browserRefreshVersion = @json(($siteSettings['system.browser_refresh_version'] ?? ''));
        const connectionCheckInterval = 5000;
        const connectionTimeout = 4000;
        const serverTimeAtLoad = @json(now()->timestamp * 1000);
        const clientTimeAtLoad = Date.now();
        const clockFormatter = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        });

        const lifecycle = new AbortController();
        let disposed = false;
        let scanAudio;
        let scanResults;
        const listen = (target, event, handler) => target.addEventListener(event, handler, {
            signal: lifecycle.signal
        });
        $wire.__instance.addCleanup(() => {
            disposed = true;
            lifecycle.abort();
            clearInterval(clockTimer);
            clearTimeout(connectionCheckTimer);
            connectionRequestController?.abort();
            scanResults?.dispose();
            window.destroyScanner?.();
            window.resetScannerQrLock?.();
            window.scanned = false;
        });
        let isServerConnected = true;
        let connectionCheckTimer = null;
        let connectionRequestController = null;
        let connectionRetryAt = 0;
        let clockTimer = null;

        function renderServerStatus() {
            const statusElement = document.getElementById('server-status');

            if (!statusElement) {
                return;
            }

            if (isServerConnected) {
                statusElement.classList.add('hidden');
                return;
            }

            statusElement.classList.remove('hidden');
        }

        function updateClock() {
            const clockElement = document.getElementById('clock');

            if (clockElement) {
                const elapsedTime = Date.now() - clientTimeAtLoad;
                clockElement.textContent = clockFormatter.format(
                    new Date(serverTimeAtLoad + elapsedTime),
                );
            }
        }

        function startClock() {
            clearInterval(clockTimer);
            updateClock();
            clockTimer = setInterval(updateClock, 1000);
        }

        function scheduleServerCheck(delay = connectionCheckInterval) {
            clearTimeout(connectionCheckTimer);

            if (disposed || document.hidden || connectionRequestController) {
                return;
            }

            connectionCheckTimer = setTimeout(checkServerConnection, Math.max(delay, connectionRetryAt - Date.now()));
        }

        async function checkServerConnection() {
            connectionCheckTimer = null;

            if (disposed || document.hidden || connectionRequestController) {
                return;
            }

            if (Date.now() < connectionRetryAt) {
                scheduleServerCheck();
                return;
            }

            if (!navigator.onLine) {
                isServerConnected = false;
                renderServerStatus();
                return;
            }

            const checkStartedAt = performance.now();
            connectionRequestController = new AbortController();
            const timeout = setTimeout(
                () => connectionRequestController?.abort(),
                connectionTimeout,
            );

        try {
            const response = await fetch(serverPingUrl, {
                method: 'HEAD',
                cache: 'no-store',
                credentials: 'same-origin',
                signal: connectionRequestController.signal,
            });

            if (response.status === 403) {
                window.location.reload();
                return;
            }

            if (response.status === 429) {
                const seconds = Number(response.headers.get('Retry-After'));
                connectionRetryAt = Date.now() + (Number.isFinite(seconds) && seconds > 0 ? seconds : 60) * 1000;
                isServerConnected = true;
                return;
            }

            const refreshVersion = response.headers.get('X-Browser-Refresh-Version');
            if (response.ok && refreshVersion && refreshVersion !== browserRefreshVersion) {
                window.location.reload();
                return;
            }
            browserRefreshVersion = refreshVersion || browserRefreshVersion;

                isServerConnected = response.ok;
            } catch {
                isServerConnected = false;
            } finally {
                clearTimeout(timeout);
                connectionRequestController = null;
                renderServerStatus();

                const elapsedTime = performance.now() - checkStartedAt;
                scheduleServerCheck(Math.max(0, connectionCheckInterval - elapsedTime));
            }
        }

        function handleVisibilityChange() {
            if (document.hidden) {
                clearInterval(clockTimer);
                clearTimeout(connectionCheckTimer);
                connectionRequestController?.abort();
                return;
            }

            startClock();
            scheduleServerCheck(0);
        }

        listen(window, 'offline', () => {
            clearTimeout(connectionCheckTimer);
            connectionRequestController?.abort();
            isServerConnected = false;
            renderServerStatus();
        });

        listen(window, 'online', () => scheduleServerCheck(0));
        listen(document, 'visibilitychange', handleVisibilityChange);

        startClock();
        scheduleServerCheck(0);


        import('{{ Vite::asset('Modules/ScanQR/resources/assets/js/scanner.js') }}')
            .then(({
                createScanAudio,
                createScanResultController
            }) => {
                if (disposed) return;
                const root = $wire.$el;
                scanAudio = createScanAudio();
                const pause = () => {
                    window.scanned = true;
                };
                scanResults = createScanResultController({
                    audio: scanAudio,
                    pause,
                    resume: () => {
                        // Allow the same stationary QR again after each modal closes.
                        window.resetScannerQrLock();
                        window.scanned = false;
                        window.initScanner();
                    },
                    closeBackend: async result => {
                        if (result.transport) {
                            root.querySelector('[data-scan-transport-host]').replaceChildren();
                        } else {
                            try {
                                await $wire.closeModal(result.id);
                            } finally {
                                root.querySelector('[data-scan-transport-host]').replaceChildren();
                            }
                        }
                    },
                });
                // Kiosk browsers with autoplay permission can start without a gesture.
                void scanAudio.preloadScanAudios();
                const preload = () => {
                    void scanAudio.preloadScanAudios();
                };
                listen(document, 'pointerdown', preload);
                listen(document, 'keydown', preload);
                const setField = (element, name, value) => {
                    const field = element.querySelector(`[data-scan-field="${name}"]`);
                    if (field) field.textContent = value || '-';
                };
                const renderScanModal = result => {
                    const modal = result.modal || {};
                    const template = root.querySelector(
                        `[data-scan-modal-template="${modal.type || 'error'}"]`,
                    );
                    const element = template?.content.firstElementChild.cloneNode(true);

                    if (!element) return null;

                    const successNotice = element.querySelector('[data-scan-success-notice]');
                    if (successNotice && result.status === 'late') {
                        successNotice.classList.remove('border-green-200', 'bg-green-50', 'text-green-800');
                        successNotice.classList.add('border-yellow-200', 'bg-yellow-50', 'text-yellow-800');
                        element.querySelector('[data-scan-field="message"]')?.classList.add('hidden');
                        element.querySelector('[data-scan-late-message]')?.classList.remove('hidden');
                    }

                    const murid = modal.murid || {};
                    const jadwal = modal.jadwal || {};
                    element.dataset.scanResult = JSON.stringify(result);
                    for (const [name, value] of Object.entries({
                        message: modal.message,
                        title: modal.title,
                        ...murid,
                        'jadwal-label': jadwal.label,
                        'jadwal-nama-acara': jadwal.nama_acara,
                        'jadwal-keterangan': jadwal.keterangan,
                        lahir: [murid.tempatLahir, murid.tanggalLahir].filter(Boolean).join(', '),
                    })) setField(element, name, value);

                    const avatarInitial = element.querySelector('[data-scan-avatar-initial]');
                    if (avatarInitial) avatarInitial.textContent = (murid.nama || '-').trim().charAt(0).toUpperCase();
                    const avatarImage = element.querySelector('[data-scan-avatar-image]');
                    if (avatarImage && murid.imagePath) {
                        avatarImage.src = murid.imagePath;
                        avatarImage.classList.remove('hidden');
                    }

                    const schedule = element.querySelector('[data-scan-schedule]');
                    if (schedule && Object.keys(jadwal).length) {
                        schedule.classList.remove('hidden');
                        const time = [jadwal.jam_masuk, jadwal.jam_pulang].filter(Boolean).join(' - ');
                        if (time) element.querySelector('[data-scan-schedule-time]').textContent = ` · ${time}`;
                    }
                    const muridDetail = element.querySelector('[data-scan-murid]');
                    if (muridDetail && modal.murid) muridDetail.classList.remove('hidden');
                    const jadwalDetail = element.querySelector('[data-scan-jadwal]');
                    if (jadwalDetail && Object.keys(jadwal).length) {
                        jadwalDetail.classList.remove('hidden');
                        for (const name of ['jadwal-nama-acara', 'jadwal-keterangan']) {
                            if (jadwal[name.replace('jadwal-', '').replaceAll('-', '_')]) {
                                element.querySelector(`[data-scan-field="${name}"]`).classList.remove('hidden');
                            }
                        }
                    }
                    const date = element.querySelector('[data-scan-date]');
                    if (date) date.textContent = new Intl.DateTimeFormat('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric',
                    }).format(new Date());

                    root.querySelector('[data-scan-transport-host]').replaceChildren(element);
                    return element;
                };
                listen(root, 'scanResult', event => {
                    const result = event.detail.result;
                    const element = renderScanModal(result);
                    if (element) scanResults.openScanResultModal(result, element);
                });
                const transportFailure = () => {
                    if (disposed || root.querySelector('[data-scan-transport-host]').firstElementChild) return;
                    const result = {
                        id: crypto.randomUUID(),
                        status: 'failed',
                        autoClose: true,
                        transport: true,
                        modal: { type: 'transport' },
                    };
                    const element = renderScanModal(result);
                    if (element) scanResults.openScanResultModal(result, element);
                };
                for (const action of ['verifiedQRCode']) {
                    const removeInterceptor = $wire.$interceptRequest(action, ({
                        onError,
                        onFailure
                    }) => {
                        onError(({
                            preventDefault
                        }) => {
                            preventDefault();
                            transportFailure();
                        });
                        onFailure(transportFailure);
                    });
                    $wire.__instance.addCleanup(removeInterceptor);
                }
                listen(root, 'scanStarted', event => {
                    pause();
                    // Capture now, but serialize server state changes after modal close.
                    scanResults.whenClosed().then(() => {
                        if (!disposed) return $wire.verifiedQRCode(event.detail.qr);
                    }).catch(transportFailure);
                });
                // Capture close actions before Livewire sends its own duplicate request.
                root.addEventListener('click', event => {
                    const closeTarget = event.composedPath().find(element => element?.getAttribute && (
                        element.hasAttribute('data-close-scan') ||
                        element.getAttribute('wire:click') === 'closeModal' ||
                        (element.getAttribute('wire:click.self') === 'closeModal' && element === event
                            .target)
                    ));
                    if (!closeTarget) return;
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    void scanResults.closeScanResultModal();
                }, {
                    capture: true,
                    signal: lifecycle.signal
                });
                listen(document, 'keydown', event => {
                    if (event.key === 'Escape' && scanResults.isOpen()) {
                        event.preventDefault();
                        void scanResults.closeScanResultModal();
                    }
                });
                listen(document, 'livewire:navigating', () => scanResults.dispose());
                if (!scanResults.isOpen()) window.initScanner();
            });
    </script>
@endscript
