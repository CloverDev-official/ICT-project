import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';
import { createAdaptiveScanScheduler } from '../../Modules/ScanQR/resources/assets/js/scan-scheduler.js';
import { createScanLock } from '../../Modules/ScanQR/resources/assets/js/scan-lock.js';

const source = readFileSync(
    new URL(
        '../../Modules/ScanQR/resources/assets/js/scanner.js',
        import.meta.url,
    ),
    'utf8',
);
const loop = source.slice(
    source.indexOf('function startLoop('),
    source.indexOf('window.initScanner ='),
);

function fixture() {
    let now = 0;
    let id = 0;
    let busy = false;
    let submits = 0;
    let callback;
    let errorCallback;
    const timers = new Map();
    const window = { scanned: false };
    const state = {
        running: true,
        nextScanAt: 0,
        submittedFrames: 0,
        scheduler: createAdaptiveScanScheduler(() => now, { lowPower: true }),
        video: { readyState: 2 },
        overlay: { width: 100, height: 100 },
        overlayContext: { clearRect() {} },
        reader: {
            dispatchEvent() {
                window.pauseScanner();
            },
        },
    };
    const context = {
        window,
        document: { hidden: false },
        scanner: state,
        generation: 1,
        performance: { now: () => now },
        FULL_FRAME_EVERY: 4,
        HTMLMediaElement: { HAVE_CURRENT_DATA: 2 },
        qrLock: createScanLock(),
        reportDebug() {},
        drawBox() {},
        teardown() {},
        debugEnabled: () => false,
        CustomEvent: class {},
        setTimeout(fn, delay) {
            timers.set(++id, { fn, at: now + delay });
            return id;
        },
        clearTimeout(timer) {
            timers.delete(timer);
        },
        createDecoderPipeline(options) {
            callback = options.onResult;
            errorCallback = options.onError;
            return {
                usesWorker: () => true,
                isReady: () => true,
                isBusy: () => busy,
                submit() {
                    assert.equal(busy, false);
                    busy = true;
                    submits++;
                    return true;
                },
            };
        },
    };
    runInNewContext(loop + '\nstartLoop(scanner, generation);', context);
    return {
        window,
        state,
        timers,
        document: context.document,
        fail() {
            busy = false;
            errorCallback(new Error('decode failed'), { fallback: false });
        },
        submits: () => submits,
        result(duration, code = null) {
            now += duration;
            busy = false;
            callback({ duration, code });
        },
        tick() {
            const [timer, entry] = [...timers].sort(
                (a, b) => a[1].at - b[1].at,
            )[0];
            timers.delete(timer);
            now = entry.at;
            entry.fn();
        },
        now: () => now,
    };
}

test('slow worker frame gets full rest with one pending scan timer', () => {
    const f = fixture();
    assert.equal(f.submits(), 1);
    assert.equal(f.timers.size, 0, 'no scan polling while worker is busy');
    f.result(900);
    assert.equal(f.timers.size, 1);
    assert.equal([...f.timers.values()][0].at - f.now(), 900);
    f.tick();
    assert.equal(f.submits(), 2);
});

test('unchanged camera frame is skipped and resume allows same frame again', () => {
    const f = fixture();
    f.state.video.currentTime = 1;
    f.result(80);
    f.tick();
    assert.equal(f.submits(), 2);
    f.result(80);
    f.tick();
    assert.equal(f.submits(), 2);
    f.state.video.currentTime = 2;
    f.tick();
    assert.equal(f.submits(), 3);
    f.result(80);
    f.window.pauseScanner();
    f.window.resumeScanner();
    assert.equal(f.submits(), 4);
});

test('worker errors rearm scanning without a polling loop', () => {
    const f = fixture();
    f.fail();
    assert.equal(f.timers.size, 1);
    assert.equal([...f.timers.values()][0].at - f.now(), 1000);
    f.tick();
    assert.equal(f.submits(), 2);
    assert.equal(f.timers.size, 0);
});

test('late result while hidden cannot start attendance or retain scan timers', () => {
    const f = fixture();
    f.document.hidden = true;
    f.result(80, { text: 'student-a', position: {} });
    assert.equal(f.window.scanned, false);
    assert.equal(f.timers.size, 0);
    f.document.hidden = false;
    f.state.wake();
    assert.equal(f.submits(), 2);
});

test('modal pause stops timers and closing rearms the same QR immediately', () => {
    const f = fixture();
    const code = { text: 'student-a', position: {} };
    f.result(80, code);
    assert.equal(f.window.scanned, true);
    assert.equal(f.timers.size, 0);
    f.window.resetScannerQrLock();
    f.window.resumeScanner();
    assert.equal(f.submits(), 2);
    f.result(80, code);
    assert.equal(f.window.scanned, true);
    assert.equal(f.timers.size, 0);
});
