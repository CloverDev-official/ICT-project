import { test, afterEach } from 'node:test';
import assert from 'node:assert/strict';
import {
    getCachedAudio,
    createScanAudio,
    AUDIO_CACHE,
    scanAudioMap,
    MAX_AUDIO_BYTES,
} from '../../resources/js/scan-audio.js';
import { createScanLock } from '../../resources/js/scan-lock.js';

const originalFetch = globalThis.fetch;
afterEach(() => {
    globalThis.fetch = originalFetch;
    delete globalThis.caches;
    delete globalThis.window;
});
function fixture(
    response = new Response('mp3', {
        headers: { 'Content-Type': 'audio/mpeg' },
    }),
) {
    globalThis.window = {
        AudioContext: class {
            decodeAudioData() {
                return Promise.resolve({});
            }
            close() {
                return Promise.resolve();
            }
        },
    };
    const entries = new Map();
    let requests = 0,
        writes = 0;
    globalThis.caches = {
        open: async (name) => {
            assert.equal(name, AUDIO_CACHE);
            return {
                match: async (key) => entries.get(key)?.clone(),
                put: async (key, value) => {
                    writes++;
                    entries.set(key, value);
                },
            };
        },
    };
    globalThis.fetch = async () => {
        requests++;
        return response.clone();
    };
    return { entries, requests: () => requests, writes: () => writes };
}
test('empty cache downloads once; warm cache makes zero further requests', async () => {
    const f = fixture();
    await getCachedAudio('success');
    await getCachedAudio('success');
    assert.equal(f.requests(), 1);
    assert.equal(f.writes(), 1);
});
test('invalid status, MIME, size and undecodable audio never enter cache', async () => {
    for (const response of [
        new Response('error', { status: 500 }),
        new Response('html', { headers: { 'Content-Type': 'text/html' } }),
        new Response(new Uint8Array(MAX_AUDIO_BYTES + 1), {
            headers: { 'Content-Type': 'audio/mpeg' },
        }),
        new Response('', { headers: { 'Content-Type': 'audio/ogg' } }),
    ]) {
        const f = fixture(response);
        await assert.rejects(getCachedAudio('success'));
        assert.equal(f.writes(), 0);
    }
    const f = fixture();
    await assert.rejects(
        getCachedAudio('success', async () => {
            throw Error('decode');
        }),
    );
    assert.equal(f.writes(), 0);
});
test('corrupt cache is not refetched or revalidated over network', async () => {
    const f = fixture();
    f.entries.set(scanAudioMap.success, new Response('bad'));
    await assert.rejects(getCachedAudio('success'));
    assert.equal(f.requests(), 0);
});
test('network failure propagates to safe audio boundary', async () => {
    fixture();
    globalThis.fetch = async () => {
        throw Error('offline');
    };
    globalThis.window = {
        AudioContext: class {
            resume() {
                return Promise.resolve();
            }
            close() {
                return Promise.resolve();
            }
        },
    };
    const audio = createScanAudio();
    await audio.preloadScanAudios();
    await audio.playScanAudio();
    audio.dispose();
});
test('all statuses preload once; only one source active, stop resets and disposal prevents playback', async () => {
    const f = fixture();
    let plays = 0,
        stops = 0,
        closes = 0,
        contexts = 0;
    globalThis.window = {
        AudioContext: class {
            constructor() {
                contexts++;
                this.state = 'running';
            }
            resume() {
                return Promise.resolve();
            }
            decodeAudioData() {
                return Promise.resolve({});
            }
            createBufferSource() {
                return {
                    connect() {},
                    disconnect() {},
                    start(when, offset) {
                        assert.equal(offset, 0);
                        plays++;
                    },
                    stop() {
                        stops++;
                    },
                };
            }
            close() {
                closes++;
                return Promise.resolve();
            }
        },
    };
    const audio = createScanAudio();
    await Promise.all([audio.preloadScanAudios(), audio.preloadScanAudios()]);
    assert.equal(f.requests(), 5);
    assert.equal(plays, 0);
    for (const status of Object.keys(scanAudioMap))
        await audio.playScanAudio(status);
    assert.equal(plays, 5);
    assert.equal(stops, 4);
    audio.stopCurrentScanAudio();
    assert.equal(stops, 5);
    await audio.playScanAudio('late_pending');
    assert.equal(plays, 5);
    assert.equal(contexts, 1);
    audio.dispose();
    await audio.playScanAudio('success');
    assert.equal(plays, 5);
    assert.equal(closes, 1);
});
test('same QR stays locked across scanner restarts, different QR and removal rearm', () => {
    const lock = createScanLock();
    assert.equal(lock.observe('A', 0), true);
    for (let t = 100; t < 10000; t += 100)
        assert.equal(lock.observe('A', t), false);
    assert.equal(lock.observe('B', 10000), true);
    lock.observe(null, 11000);
    lock.observe(null, 12000);
    assert.equal(lock.observe('B', 12100), true);
    lock.reset();
    assert.equal(lock.observe('B', 12200), true);
});

test('closing during preload cancels deferred playback', async () => {
    fixture();
    const decodes = [];
    let plays = 0;
    globalThis.window = {
        AudioContext: class {
            state = 'running';
            resume() {
                return Promise.resolve();
            }
            decodeAudioData() {
                return new Promise((resolve) => decodes.push(resolve));
            }
            createBufferSource() {
                return {
                    connect() {},
                    start() {
                        plays++;
                    },
                };
            }
            close() {
                return Promise.resolve();
            }
        },
    };
    const audio = createScanAudio();
    const preload = audio.preloadScanAudios();
    const play = audio.playScanAudio('success');
    audio.stopCurrentScanAudio();
    while (decodes.length < 5)
        await new Promise((resolve) => setImmediate(resolve));
    decodes.forEach((resolve) => resolve({}));
    await preload;
    await play;
    assert.equal(plays, 0);
    audio.dispose();
});
