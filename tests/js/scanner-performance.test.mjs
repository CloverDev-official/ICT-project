import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
    createAdaptiveScanScheduler,
    intervalForDecode,
    isLowPowerDevice,
} from '../../Modules/ScanQR/resources/assets/js/scan-scheduler.js';
import {
    createFramePlan,
    mapPointToSource,
} from '../../Modules/ScanQR/resources/assets/js/frame-utils.js';
import { createScanLock } from '../../Modules/ScanQR/resources/assets/js/scan-lock.js';
import { createDecoderPipeline } from '../../Modules/ScanQR/resources/assets/js/decoder-pipeline.js';

test('adaptive scan interval reduces decode rate as devices get slower', () => {
    assert.equal(intervalForDecode(50), 125);
    assert.equal(intervalForDecode(100), 167);
    assert.equal(intervalForDecode(200), 250);
    assert.equal(intervalForDecode(300), 400);

    let now = 0;
    const scheduler = createAdaptiveScanScheduler(() => now);
    for (let index = 0; index < 12; index++) {
        now += 300;
        scheduler.recordDecode(300);
    }
    assert.ok(scheduler.getInterval() >= 390);
    scheduler.recordSkipped();
    assert.equal(scheduler.snapshot().skippedFrames, 1);
});

test('low-power profile limits decode duty cycle on slow quad-core devices', () => {
    assert.equal(
        isLowPowerDevice({ hardwareConcurrency: 4, deviceMemory: 4 }),
        true,
    );
    assert.equal(
        isLowPowerDevice({ hardwareConcurrency: 8, deviceMemory: 8 }),
        false,
    );

    let now = 0;
    const scheduler = createAdaptiveScanScheduler(() => now, {
        lowPower: true,
    });
    assert.equal(scheduler.getInterval(), 200);
    for (let index = 0; index < 12; index++) {
        now += 200;
        scheduler.recordDecode(100);
    }
    assert.ok(scheduler.getInterval() >= 195);
    for (let index = 0; index < 12; index++) {
        now += 400;
        scheduler.recordDecode(300);
    }
    assert.ok(scheduler.getInterval() >= 490);
    assert.equal(scheduler.snapshot().lowPower, true);
});

test('center crop reduces pixel work without enlarging and maps points back', () => {
    const plan = createFramePlan(640, 480);
    assert.deepEqual(plan.crop, {
        x: 147,
        y: 67,
        width: 346,
        height: 346,
    });
    assert.equal(plan.outputWidth, 346);
    assert.equal(plan.outputHeight, 346);
    assert.ok(plan.outputWidth * plan.outputHeight < 640 * 480 * 0.4);
    assert.deepEqual(mapPointToSource({ x: 0, y: 0 }, plan), {
        x: 147,
        y: 67,
    });

    const large = createFramePlan(1920, 1080);
    assert.equal(Math.max(large.outputWidth, large.outputHeight), 640);
    const full = createFramePlan(320, 240, true);
    assert.equal(full.outputWidth, 320);
    assert.equal(full.outputHeight, 240);
});

test('higher-resolution camera crop preserves more detail for distant QR', () => {
    const plan = createFramePlan(960, 720);
    assert.deepEqual(plan.crop, {
        x: 221,
        y: 101,
        width: 518,
        height: 518,
    });
    assert.equal(plan.outputWidth, 518);
    assert.equal(plan.outputHeight, 518);
});

test('duplicate QR is locked while present and during cooldown', () => {
    const lock = createScanLock({ cooldownMs: 2000, rearmAfterMs: 500 });
    assert.equal(lock.observe('student-a', 0), true);
    assert.equal(lock.observe('student-a', 1000), false);
    lock.observe(null, 1100);
    lock.observe(null, 1700);
    assert.equal(lock.observe('student-a', 1800), false);
    assert.equal(lock.observe('student-b', 1801), true);
    lock.observe(null, 2400);
    assert.equal(lock.observe('student-a', 2401), true);
});

test('decoder pipeline allows one frame only and transfers pixels to worker fallback', () => {
    const listeners = new Map();
    let terminated = false;
    let posted;
    class FakeWorker {
        addEventListener(name, handler) {
            listeners.set(name, handler);
        }
        postMessage(message, transfers) {
            posted = { message, transfers };
        }
        terminate() {
            terminated = true;
        }
    }
    const originalWorker = globalThis.Worker;
    const originalDocument = globalThis.document;
    globalThis.Worker = FakeWorker;
    globalThis.document = {
        createElement: () => ({
            getContext: () => ({
                drawImage() {},
                getImageData: (_x, _y, width, height) => ({
                    data: new Uint8ClampedArray(width * height * 4),
                }),
            }),
        }),
    };

    try {
        let results = 0;
        const pipeline = createDecoderPipeline({
            onResult: () => results++,
            onError: () => {},
        });
        listeners.get('message')({
            data: { type: 'ready', offscreenCanvas: false },
        });
        const video = { videoWidth: 640, videoHeight: 480 };
        assert.equal(pipeline.submit(video), true);
        assert.equal(pipeline.submit(video), false);
        assert.equal(posted.message.type, 'decode');
        assert.ok(posted.message.pixels instanceof ArrayBuffer);
        assert.equal(posted.transfers.length, 1);

        listeners.get('message')({
            data: {
                type: 'result',
                duration: 20,
                frame: posted.message.frame,
                code: null,
            },
        });
        assert.equal(results, 1);
        assert.equal(pipeline.submit(video), true);
        pipeline.dispose();
        assert.equal(terminated, true);
    } finally {
        globalThis.Worker = originalWorker;
        globalThis.document = originalDocument;
    }
});

test('worker startup error switches the pipeline to main-thread fallback', () => {
    const listeners = new Map();
    const originalWorker = globalThis.Worker;
    globalThis.Worker = class {
        addEventListener(name, handler) {
            listeners.set(name, handler);
        }
        terminate() {}
    };

    try {
        let fallback = false;
        const pipeline = createDecoderPipeline({
            onResult: () => {},
            onError: (_error, detail) => {
                fallback = detail.fallback;
            },
        });
        listeners.get('error')({
            message: 'worker failed',
            preventDefault() {},
        });
        assert.equal(fallback, true);
        assert.equal(pipeline.usesWorker(), false);
        assert.equal(pipeline.isReady(), true);
        pipeline.dispose();
    } finally {
        globalThis.Worker = originalWorker;
    }
});
