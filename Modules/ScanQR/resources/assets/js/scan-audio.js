export const AUDIO_CACHE = 'attendance-scan-audio-v2';
export const MAX_AUDIO_BYTES = 5 * 1024 * 1024;
export const scanAudioMap = {
    success: new URL('../audio/scan-success.mp3?no-inline', import.meta.url)
        .href,
    permission_success: new URL(
        '../audio/permission_success.mp3?no-inline',
        import.meta.url,
    ).href,
    late: new URL('../audio/scan-late.mp3?no-inline', import.meta.url).href,
    failed: new URL('../audio/scan-failed.mp3?no-inline', import.meta.url).href,
    already_recorded: new URL(
        '../audio/already-recorded.mp3?no-inline',
        import.meta.url,
    ).href,
    attendance_not_open: new URL(
        '../audio/attendance-not-open.mp3?no-inline',
        import.meta.url,
    ).href,
};

async function validateAudioBytes(bytes) {
    const context = new (window.AudioContext || window.webkitAudioContext)();
    try {
        await context.decodeAudioData(bytes);
    } finally {
        await context.close();
    }
}

// A cache hit never reaches fetch, including when the cached file is corrupt.
export async function getCachedAudio(
    status = 'success',
    decode = validateAudioBytes,
) {
    const url = scanAudioMap[status];
    if (!url) throw new Error('Unknown scan audio status');
    let cache, cached;
    try {
        cache = await globalThis.caches?.open(AUDIO_CACHE);
        cached = await cache?.match(url);
    } catch {
        // HTTP/IP and restricted browsers can lack usable Cache Storage.
        cache = undefined;
    }
    const response = cached || (await fetch(url, { cache: 'no-store' }));
    if (
        !response.ok ||
        !['audio/mpeg', 'audio/ogg'].includes(
            (response.headers.get('content-type') || '')
                .split(';')[0]
                .trim()
                .toLowerCase(),
        )
    )
        throw new Error('Invalid scan audio response');
    const blob = await response.blob();
    if (!blob.size || blob.size > MAX_AUDIO_BYTES)
        throw new Error('Invalid scan audio size');
    await decode(await blob.arrayBuffer());
    if (!cached && cache) {
        try {
            await cache.put(
                url,
                new Response(blob, {
                    headers: { 'Content-Type': blob.type },
                }),
            );
        } catch {
            // Decoded audio remains usable even if persistent storage is full.
        }
    }
    return blob;
}

export function createScanAudio() {
    let context, source;
    const buffers = new Map();
    const loading = new Map();
    let disposed = false;
    let generation = 0;

    function preloadScanAudios() {
        if (disposed) return Promise.resolve();
        try {
            context ||= new (
                window.AudioContext || window.webkitAudioContext
            )();
            // Kiosk autoplay can resume immediately; gestures retry on regular browsers.
            void context.resume().catch(() => {});
            for (const status of Object.keys(scanAudioMap)) {
                if (loading.has(status)) continue;
                loading.set(
                    status,
                    getCachedAudio(status, async (bytes) => {
                        const buffer = await context.decodeAudioData(bytes);
                        if (!disposed) buffers.set(status, buffer);
                    }).catch(() => {}),
                );
            }
            return Promise.allSettled(loading.values());
        } catch {
            return Promise.resolve();
        }
    }

    function stopCurrentScanAudio() {
        generation++;
        try {
            source?.stop();
            source?.disconnect();
        } catch {}
        source = null;
        // BufferSource is one-shot: the next source always starts at offset 0.
    }

    async function playScanAudio(status) {
        stopCurrentScanAudio();
        const current = generation;
        if (disposed || !scanAudioMap[status]) return;
        await loading.get(status);
        if (disposed || current !== generation || context?.state !== 'running')
            return;
        const buffer = buffers.get(status);
        if (!buffer) return;
        try {
            source = context.createBufferSource();
            source.buffer = buffer;
            source.connect(context.destination);
            source.start(0, 0);
        } catch {
            /* Attendance is independent of audio. */
        }
    }

    return {
        preloadScanAudios,
        playScanAudio,
        stopCurrentScanAudio,
        dispose() {
            disposed = true;
            stopCurrentScanAudio();
            void context?.close().catch(() => {});
            buffers.clear();
        },
    };
}
