import { test } from 'node:test';
import assert from 'node:assert/strict';
import { createScanResultController } from '../../Modules/ScanQR/resources/assets/js/scan-result.js';
function fixture(closeBackend) {
    const calls = [];
    let timer;
    const controller = createScanResultController({
        audio: {
            playScanAudio: (status) => calls.push(status),
            stopCurrentScanAudio: () => calls.push('stop'),
            dispose: () => calls.push('dispose'),
        },
        pause: () => calls.push('pause'),
        resume: () => calls.push('resume'),
        closeBackend: closeBackend || (() => calls.push('close')),
        schedule: (callback) => {
            timer = callback;
            return 1;
        },
        cancel: () => {
            timer = null;
        },
    });
    const element = () => ({
        isConnected: true,
        style: {},
        hidden: false,
        setAttribute() {
            this.hidden = true;
        },
    });
    return { controller, calls, element, timeout: () => timer?.() };
}
test('one result opens once, rerender does not replay, repeated close is safe', async () => {
    const f = fixture();
    const el = f.element();
    const result = { id: '1', status: 'success', autoClose: true };
    f.controller.openScanResultModal(result, el);
    f.controller.openScanResultModal(result, el);
    assert.equal(f.calls.filter((x) => x === 'success').length, 1);
    f.controller.openScanResultModal(result, f.element());
    assert.equal(f.calls.filter((x) => x === 'success').length, 1);
    await Promise.all([
        f.controller.closeScanResultModal(),
        f.controller.closeScanResultModal(),
    ]);
    assert.equal(f.calls.filter((x) => x === 'close').length, 1);
    assert.equal(f.calls.filter((x) => x === 'resume').length, 1);
    f.controller.openScanResultModal(result, el);
    assert.equal(f.controller.isOpen(), false);
});
test('new modal stops old sound and auto-close stops current sound immediately', async () => {
    const f = fixture();
    f.controller.openScanResultModal(
        { id: '1', status: 'success' },
        f.element(),
    );
    f.controller.openScanResultModal(
        { id: '2', status: 'late', autoClose: true },
        f.element(),
    );
    assert.ok(f.calls.lastIndexOf('stop') > f.calls.indexOf('success'));
    f.timeout();
    assert.deepEqual(f.calls.slice(-2), ['stop', 'resume']);
    assert.equal(f.controller.isOpen(), false);
    await Promise.resolve();
});
test('slow backend close resumes capture immediately but keeps next request waiting', async () => {
    let finishClose;
    const f = fixture(
        () =>
            new Promise((resolve) => {
                finishClose = resolve;
            }),
    );
    const el = f.element();
    f.controller.openScanResultModal({ id: '1', status: 'success' }, el);
    const closing = f.controller.closeScanResultModal();
    assert.equal(el.hidden, true);
    assert.equal(f.calls.at(-1), 'resume');
    let nextRequest = false;
    const next = f.controller.whenClosed().then(() => {
        nextRequest = true;
    });
    await Promise.resolve();
    assert.equal(nextRequest, false);
    f.controller.openScanResultModal(
        { id: '2', status: 'success' },
        f.element(),
    );
    finishClose();
    await closing;
    await next;
    assert.equal(nextRequest, true);
    assert.equal(f.controller.isOpen(), true);
    assert.equal(f.calls.filter((x) => x === 'resume').length, 1);
});
test('removed/hidden modal and SPA disposal stop sound', () => {
    for (const reason of ['removed', 'hidden', 'spa']) {
        const f = fixture();
        const el = f.element();
        f.controller.openScanResultModal({ id: reason, status: 'failed' }, el);
        if (reason === 'spa') f.controller.dispose();
        else {
            if (reason === 'removed') el.isConnected = false;
            else el.hidden = true;
            f.controller.syncVisibility();
        }
        assert.equal(f.controller.isOpen(), false);
        assert.ok(f.calls.lastIndexOf('stop') > f.calls.indexOf('failed'));
    }
});

for (const status of ['success', 'permission_success', 'late']) {
    test(`${status} audio plays once per result and stops on manual close`, async () => {
        const f = fixture();
        const element = f.element();
        const result = { id: status, status, autoClose: true };
        f.controller.openScanResultModal(result, element);
        f.controller.openScanResultModal(result, element);
        assert.equal(f.calls.filter((call) => call === status).length, 1);
        await f.controller.closeScanResultModal();
        assert.ok(f.calls.lastIndexOf('stop') > f.calls.indexOf(status));
        assert.equal(element.hidden, true);
    });
}
