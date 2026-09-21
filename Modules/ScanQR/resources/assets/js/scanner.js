import { createDecoderPipeline } from './decoder-pipeline.js';
import { mapPointToSource } from './frame-utils.js';
import { createScanAudio } from './scan-audio.js';
import { createScanLock } from './scan-lock.js';
import {
    createAdaptiveScanScheduler,
    isLowPowerDevice,
} from './scan-scheduler.js';

export { createScanAudio };
export { createScanResultController } from './scan-result.js';

const FULL_FRAME_EVERY = 4;
const DEBUG_REPORT_INTERVAL = 5000;
const qrLock = createScanLock();

let generation = 0;
let scanner = null;
let isStarting = false;

function debugEnabled() {
    return import.meta.env.DEV && globalThis.SCAN_DEBUG === true;
}

function stopStream(stream) {
    stream?.getTracks().forEach((track) => {
        track.onended = null;
        track.stop();
    });
}

async function enableContinuousFocus(stream) {
    const track = stream?.getVideoTracks()[0];
    if (!track?.getCapabilities || !track.applyConstraints) return;
    try {
        const capabilities = track.getCapabilities();
        const continuous = {};
        if (capabilities.focusMode?.includes('continuous')) {
            continuous.focusMode = 'continuous';
        }
        if (capabilities.exposureMode?.includes('continuous')) {
            continuous.exposureMode = 'continuous';
        }
        if (capabilities.whiteBalanceMode?.includes('continuous')) {
            continuous.whiteBalanceMode = 'continuous';
        }
        if (!Object.keys(continuous).length) return;
        await track.applyConstraints({ advanced: [continuous] });
    } catch {
        // Unsupported focus constraints must never prevent camera startup.
    }
}

function teardown({ clearReader = true } = {}) {
    const current = scanner;
    scanner = null;
    isStarting = false;
    generation++;
    if (!current) {
        if (clearReader) document.getElementById('reader')?.replaceChildren();
        return;
    }

    current.running = false;
    clearTimeout(current.animationFrame);
    current.lifecycle.abort();
    current.resizeObserver?.disconnect();
    current.pipeline?.dispose();
    current.video.pause();
    current.video.srcObject = null;
    stopStream(current.stream);
    current.overlayContext = null;
    if (clearReader) current.reader.replaceChildren();
}

function showCameraError(reader, message) {
    reader.replaceChildren();
    const notice = document.createElement('div');
    notice.className =
        'flex h-full min-h-64 items-center justify-center p-6 text-center text-sm text-red-300';
    notice.textContent = message;
    reader.appendChild(notice);
    reader.dispatchEvent(
        new CustomEvent('scanError', {
            bubbles: true,
            detail: { message },
        }),
    );
}

function cameraErrorMessage(error) {
    if (error?.name === 'NotAllowedError') {
        return 'Izin kamera ditolak. Izinkan akses kamera lalu muat ulang halaman.';
    }
    if (error?.name === 'NotFoundError') {
        return 'Kamera tidak ditemukan pada perangkat ini.';
    }
    return 'Kamera tidak dapat digunakan. Periksa kamera lalu coba lagi.';
}

function scalePoint(point, frame, overlay) {
    const source = mapPointToSource(point, frame);
    const videoRatio = frame.sourceWidth / frame.sourceHeight;
    const canvasRatio = overlay.width / overlay.height;
    let drawWidth;
    let drawHeight;
    let offsetX = 0;
    let offsetY = 0;

    if (canvasRatio > videoRatio) {
        drawWidth = overlay.width;
        drawHeight = overlay.width / videoRatio;
        offsetY = (overlay.height - drawHeight) / 2;
    } else {
        drawHeight = overlay.height;
        drawWidth = overlay.height * videoRatio;
        offsetX = (overlay.width - drawWidth) / 2;
    }

    return {
        x: (source.x / frame.sourceWidth) * drawWidth + offsetX,
        y: (source.y / frame.sourceHeight) * drawHeight + offsetY,
    };
}

function drawBox(state, position, frame) {
    const points = [
        position.topLeft,
        position.topRight,
        position.bottomRight,
        position.bottomLeft,
    ].map((point) => scalePoint(point, frame, state.overlay));
    const context = state.overlayContext;
    context.strokeStyle = '#00FF66';
    context.lineWidth = 3;
    context.lineJoin = 'round';
    context.beginPath();
    context.moveTo(points[0].x, points[0].y);
    points.slice(1).forEach((point) => context.lineTo(point.x, point.y));
    context.closePath();
    context.stroke();
}

function syncOverlaySize(state) {
    const rect = state.reader.getBoundingClientRect();
    state.overlay.width = Math.max(
        1,
        Math.round(rect.width || state.reader.offsetWidth),
    );
    state.overlay.height = Math.max(
        1,
        Math.round(rect.height || state.reader.offsetHeight),
    );
}

function reportDebug(state) {
    if (!debugEnabled()) return;
    const timestamp = performance.now();
    if (timestamp - state.lastDebugReport < DEBUG_REPORT_INTERVAL) return;
    state.lastDebugReport = timestamp;
    console.debug('[ScanQR]', {
        decoder: state.pipeline.usesWorker()
            ? 'worker'
            : 'main-thread fallback',
        profile: state.lowPower ? 'low-power' : 'standard',
        ...state.scheduler.snapshot(true),
    });
}

function startLoop(state, currentGeneration) {
    const onDecode = ({ duration, frame, code }) => {
        if (
            !state.running ||
            scanner !== state ||
            currentGeneration !== generation
        ) {
            return;
        }
        state.scheduler.recordDecode(duration);
        state.nextScanAt =
            performance.now() +
            state.scheduler.getRest(duration, !state.pipeline.usesWorker());
        clearTimeout(state.animationFrame);
        if (!document.hidden && !window.scanned) {
            state.animationFrame = setTimeout(
                scan,
                Math.max(0, state.nextScanAt - performance.now()),
            );
        }
        if (document.hidden || window.scanned) return;
        state.overlayContext.clearRect(
            0,
            0,
            state.overlay.width,
            state.overlay.height,
        );
        reportDebug(state);

        if (!code?.text || !code.position) {
            qrLock.observe(null, performance.now());
            return;
        }

        drawBox(state, code.position, frame);
        const timestamp = performance.now();
        if (window.scanned || !qrLock.observe(code.text, timestamp)) return;

        window.scanned = true;
        state.reader.dispatchEvent(
            new CustomEvent('scanStarted', {
                bubbles: true,
                detail: { qr: code.text },
            }),
        );
    };

    state.pipeline = createDecoderPipeline({
        onResult: onDecode,
        onError: (error, { fallback }) => {
            if (!state.running || scanner !== state) return;
            state.nextScanAt = performance.now() + 1000;
            clearTimeout(state.animationFrame);
            if (!document.hidden && !window.scanned) {
                state.animationFrame = setTimeout(scan, 1000);
            }
            if (debugEnabled()) {
                console.warn(
                    fallback
                        ? '[ScanQR] Worker gagal; memakai fallback.'
                        : '[ScanQR] Decode gagal.',
                    error,
                );
            }
        },
    });

    function scan() {
        state.animationFrame = null;
        if (!state.running || scanner !== state) return;
        if (document.hidden || window.scanned) return;
        const timestamp = performance.now();
        state.animationFrame = setTimeout(
            scan,
            Math.max(
                50,
                state.nextScanAt - timestamp,
                state.scheduler.getInterval(),
            ),
        );

        if (
            document.hidden ||
            window.scanned ||
            state.video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA ||
            !state.pipeline.isReady()
        ) {
            return;
        }

        if (timestamp < state.nextScanAt) return;

        if (state.pipeline.isBusy()) {
            state.scheduler.recordSkipped();
            clearTimeout(state.animationFrame);
            state.animationFrame = null;
            return;
        }

        // A stalled/slow camera can expose the same frame across several ticks.
        const videoTime = state.video.currentTime;
        if (Number.isFinite(videoTime) && videoTime === state.lastVideoTime)
            return;
        const fullFrame = (state.submittedFrames + 1) % FULL_FRAME_EVERY === 0;
        if (state.pipeline.submit(state.video, fullFrame)) {
            state.submittedFrames++;
            state.lastVideoTime = videoTime;
            // Result/error callbacks re-arm the loop; no polling during decoding.
            clearTimeout(state.animationFrame);
            state.animationFrame = null;
        }
    }

    state.wake = () => {
        clearTimeout(state.animationFrame);
        state.nextScanAt = 0;
        state.lastVideoTime = null;
        scan();
    };
    state.wake();
}

window.destroyScanner = () => teardown();
window.resetScannerQrLock = () => qrLock.reset();
window.pauseScanner = () => {
    window.scanned = true;
    if (scanner) clearTimeout(scanner.animationFrame);
};
window.resumeScanner = () => {
    window.scanned = false;
    scanner?.wake?.();
};

window.initScanner = async () => {
    if (scanner?.running || isStarting) return;
    const reader = document.getElementById('reader');
    if (!reader || !navigator.mediaDevices?.getUserMedia) {
        if (reader) {
            showCameraError(
                reader,
                'Browser ini tidak mendukung akses kamera.',
            );
        }
        return;
    }

    isStarting = true;
    const currentGeneration = ++generation;
    const lowPower = isLowPowerDevice();
    const camera = lowPower
        ? {
              width: { ideal: 800, max: 960 },
              height: { ideal: 600, max: 720 },
              frameRate: { ideal: 12, max: 15 },
          }
        : {
              width: { ideal: 960, max: 1280 },
              height: { ideal: 720, max: 960 },
              frameRate: { ideal: 15, max: 20 },
          };
    reader.replaceChildren();
    Object.assign(reader.style, {
        position: 'relative',
        width: '100%',
        height: '100%',
        overflow: 'hidden',
        background: '#000',
    });

    const video = document.createElement('video');
    video.autoplay = true;
    video.playsInline = true;
    video.muted = true;
    Object.assign(video.style, {
        position: 'absolute',
        inset: '0',
        width: '100%',
        height: '100%',
        objectFit: 'cover',
    });

    const overlay = document.createElement('canvas');
    Object.assign(overlay.style, {
        position: 'absolute',
        inset: '0',
        width: '100%',
        height: '100%',
        pointerEvents: 'none',
        zIndex: '10',
    });
    reader.append(video, overlay);

    let stream;
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: 'environment' },
                ...camera,
            },
            audio: false,
        });
    } catch (error) {
        isStarting = false;
        if (currentGeneration === generation) {
            showCameraError(reader, cameraErrorMessage(error));
        }
        return;
    }

    if (currentGeneration !== generation || !reader.isConnected) {
        stopStream(stream);
        isStarting = false;
        return;
    }

    await enableContinuousFocus(stream);

    if (currentGeneration !== generation || !reader.isConnected) {
        stopStream(stream);
        isStarting = false;
        return;
    }

    const lifecycle = new AbortController();
    const state = {
        reader,
        video,
        overlay,
        overlayContext: overlay.getContext('2d'),
        stream,
        lifecycle,
        resizeObserver: null,
        pipeline: null,
        scheduler: createAdaptiveScanScheduler(undefined, { lowPower }),
        lowPower,
        animationFrame: null,
        nextScanAt: 0,
        lastVideoTime: null,
        wake: null,
        running: false,
        submittedFrames: 0,
        lastDebugReport: performance.now(),
    };
    scanner = state;
    syncOverlaySize(state);
    if (typeof ResizeObserver === 'function') {
        state.resizeObserver = new ResizeObserver(() => syncOverlaySize(state));
        state.resizeObserver.observe(reader);
    } else {
        window.addEventListener('resize', () => syncOverlaySize(state), {
            signal: lifecycle.signal,
        });
    }

    const handleTrackEnded = () => {
        if (!state.running || scanner !== state) return;
        teardown({ clearReader: false });
        showCameraError(
            reader,
            'Kamera terputus. Sambungkan kamera lalu muat ulang halaman.',
        );
    };
    stream.getVideoTracks().forEach((track) => {
        track.onended = handleTrackEnded;
    });

    video.srcObject = stream;
    try {
        await video.play();
    } catch (error) {
        if (scanner === state) {
            teardown({ clearReader: false });
            showCameraError(reader, cameraErrorMessage(error));
        }
        return;
    }

    if (currentGeneration !== generation || scanner !== state) {
        stopStream(stream);
        return;
    }

    isStarting = false;
    state.running = true;
    document.addEventListener(
        'visibilitychange',
        () => {
            if (document.hidden) clearTimeout(state.animationFrame);
            else state.wake?.();
        },
        { signal: lifecycle.signal },
    );
    startLoop(state, currentGeneration);
};
