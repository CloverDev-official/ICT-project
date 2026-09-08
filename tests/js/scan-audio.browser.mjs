// Optional: QR_PLAYWRIGHT_MODULE=/path/to/playwright/index.mjs node tests/js/scan-audio.browser.mjs
import { createServer } from 'node:http';
import { readFile } from 'node:fs/promises';
import assert from 'node:assert/strict';
const { chromium } = await import(
    process.env.QR_PLAYWRIGHT_MODULE || 'playwright'
);
const server = createServer(async (req, res) => {
    const pathname = new URL(req.url, 'http://localhost').pathname;
    if (pathname === '/scanner-fixture.js') {
        res.setHeader('Content-Type', 'text/javascript');
        res.end(`export {createScanAudio} from '/resources/js/scan-audio.js';
        export {createScanResultController} from '/resources/js/scan-result.js';
        window.initScanner = () => {window.cameraStarts = (window.cameraStarts || 0) + 1};
        window.destroyScanner = () => {};
        window.resetScannerQrLock = () => {};`);
        return;
    }
    if (pathname === '/integration') {
        const blade = await readFile(
            new URL(
                '../../Modules/ScanQR/resources/views/livewire/murid/scan.blade.php',
                import.meta.url,
            ),
            'utf8',
        );
        const script = blade
            .split('@script')[1]
            .split('<script>')[1]
            .split('</script>')[0]
            .replace('@json($serverPingUrl)', "'/ping'")
            .replace('@json(now()->timestamp * 1000)', 'Date.now()')
            .replace(
                /import\('.*Vite::asset.*'\)/,
                "import('/scanner-fixture.js')",
            );
        res.setHeader('Content-Type', 'text/html');
        res.end(`<div id="root"><div id="reader"></div><div data-scan-transport-host></div>
        <template data-scan-transport-template><div><button data-close-scan>Close</button></div></template></div>
        <script>
        window.plays = 0; window.stops = 0;
        const originalStart = AudioBufferSourceNode.prototype.start, originalStop = AudioBufferSourceNode.prototype.stop;
        AudioBufferSourceNode.prototype.start = function(...args) {plays++;return originalStart.apply(this,args)};
        AudioBufferSourceNode.prototype.stop = function(...args) {stops++;return originalStop.apply(this,args)};
        const root = document.querySelector('#root');
        window.cleanups = [];
        window.$wire = {$el: root, __instance:{addCleanup: fn => cleanups.push(fn)},
        $interceptRequest: () => () => {},
        closeModal: async () => {root.querySelector('[data-scan-result]')?.remove()},
        verifiedQRCode: async status => {
            if (status === 'transport') throw Error('network failed');
            const result = {id:crypto.randomUUID(),status,autoClose: status !== 'late_pending'};
            root.querySelector('[data-scan-result]')?.remove();
            const element = document.createElement('div');
            element.dataset.scanResult = JSON.stringify(result);
            element.innerHTML = '<button wire:click="closeModal" id="close">Close</button><div wire:click="closeModal" id="backdrop">Backdrop</div>';
            root.append(element);
            root.dispatchEvent(new CustomEvent('scanResult', {detail:{result}}));
        }};
        </script><script type="module">${script}</script>`);
        return;
    }
    if (pathname === '/') {
        res.setHeader('Content-Type', 'text/html');
        res.end(`<button id="start">Activate audio</button><main></main><script type="module">
        import {createScanAudio} from '/resources/js/scan-audio.js';
        import {createScanResultController} from '/resources/js/scan-result.js';
        window.audio = createScanAudio();
        window.plays = 0; window.stops = 0; window.pauses = 0; window.resumes = 0;
        const start = AudioBufferSourceNode.prototype.start;
        const stop = AudioBufferSourceNode.prototype.stop;
        AudioBufferSourceNode.prototype.start = function(...args) {window.plays++; return start.apply(this,args)};
        AudioBufferSourceNode.prototype.stop = function(...args) {window.stops++; return stop.apply(this,args)};
        window.modal = createScanResultController({audio,
            pause: () => pauses++, resume: () => resumes++, closeBackend: async () => {},
        });
        window.openResult = (status, id = crypto.randomUUID(), autoClose = false) => {
            document.querySelector('main').innerHTML = '<div id="modal"><button id="close">Tutup</button><button id="ok">OK</button><button id="backdrop">Backdrop</button></div>';
            const el = document.querySelector('#modal');
            for (const button of el.querySelectorAll('button')) button.onclick = () => modal.closeScanResultModal();
            modal.openScanResultModal({id, status, autoClose}, el);
        };
        document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.closeScanResultModal(); });
        document.querySelector('#start').onclick = () => window.ready = audio.preloadScanAudios();
        </script>`);
        return;
    }
    if (
        !/^\/resources\/(js\/(scan-audio|scan-result)\.js|audio\/(scan-success|scan-late|scan-failed|already-recorded|attendance-not-open)\.mp3)$/.test(
            pathname,
        )
    ) {
        res.writeHead(404).end();
        return;
    }
    res.setHeader(
        'Content-Type',
        pathname.endsWith('.mp3') ? 'audio/mpeg' : 'text/javascript',
    );
    res.end(await readFile(new URL('../..' + pathname, import.meta.url)));
});
await new Promise((resolve) => server.listen(0, '127.0.0.1', resolve));
let browser;
try {
    browser = await chromium.launch({ channel: 'chromium', headless: true });
    const page = await browser.newPage();
    const requests = [];
    const session = await page.context().newCDPSession(page);
    await session.send('Network.enable');
    session.on('Network.requestWillBeSent', (e) => {
        if (e.request.url.includes('.mp3')) requests.push(e.request.url);
    });
    const origin = `http://127.0.0.1:${server.address().port}`;
    await page.goto(origin);
    assert.equal(requests.length, 0);
    await page.click('#start');
    await page.evaluate(() => ready);
    assert.equal(requests.length, 5);
    assert.equal(
        await page.evaluate(
            async () =>
                (await (await caches.open('attendance-scan-audio-v2')).keys())
                    .length,
        ),
        5,
    );
    for (const [status, close] of [
        ['success', '#close'],
        ['late', '#ok'],
        ['failed', '#backdrop'],
        ['already_recorded', 'Escape'],
        ['attendance_not_open', 'automatic'],
    ]) {
        const before = await page.evaluate(() => ({ plays, stops }));
        await page.evaluate(
            ({ status, automatic }) =>
                openResult(status, crypto.randomUUID(), automatic),
            { status, automatic: close === 'automatic' },
        );
        await page.waitForFunction(
            (expected) => plays === expected,
            before.plays + 1,
        );
        if (close === 'Escape') await page.keyboard.press('Escape');
        else if (close !== 'automatic') await page.click(close);
        await page.waitForFunction(() => !modal.isOpen());
        assert.equal(await page.evaluate(() => stops), before.stops + 1);
    }
    await page.evaluate(() => openResult('success', 'same'));
    await page.waitForFunction(() => plays === 6);
    await page.evaluate(() =>
        modal.openScanResultModal(
            { id: 'same', status: 'success' },
            document.querySelector('#modal'),
        ),
    );
    assert.equal(await page.evaluate(() => plays), 6);
    await page.evaluate(() => openResult('late', 'next'));
    await page.waitForFunction(() => plays === 7);
    await page.evaluate(() => modal.dispose());
    assert.equal(await page.evaluate(() => stops), 7);
    await page.reload();
    await page.click('#start');
    await page.evaluate(() => ready);
    assert.equal(
        requests.length,
        5,
        'Warm cache produces zero Network requests',
    );
    await page.evaluate(() => caches.delete('attendance-scan-audio-v2'));
    await page.reload();
    await page.click('#start');
    await page.evaluate(() => ready);
    assert.equal(requests.length, 10);
    await page.route('**/resources/js/scan-audio.js', async (route) => {
        const body = (
            await readFile(
                new URL('../../resources/js/scan-audio.js', import.meta.url),
                'utf8',
            )
        ).replace('attendance-scan-audio-v2', 'attendance-scan-audio-v3');
        await route.fulfill({ contentType: 'text/javascript', body });
    });
    await page.reload();
    await page.click('#start');
    await page.evaluate(() => ready);
    assert.equal(
        requests.length,
        15,
        'New cache version fetches each file once',
    );
    await page.unroute('**/resources/js/scan-audio.js');

    // Real browser rejects failed and oversized audio without caching them.
    await page.evaluate(() => caches.delete('attendance-scan-audio-v2'));
    await page.route('**/scan-failed.mp3*', (route) =>
        route.fulfill({ status: 503, body: 'Unavailable' }),
    );
    await page.route('**/scan-late.mp3*', (route) =>
        route.fulfill({
            contentType: 'audio/mpeg',
            body: Buffer.alloc(5 * 1024 * 1024 + 1),
        }),
    );
    await page.reload();
    await page.click('#start');
    await page.evaluate(() => ready);
    assert.equal(
        await page.evaluate(
            async () =>
                (await (await caches.open('attendance-scan-audio-v2')).keys())
                    .length,
        ),
        3,
    );
    await page.evaluate(async () => {
        await audio.playScanAudio('failed');
        await audio.playScanAudio('late');
    });
    assert.equal(await page.evaluate(() => plays), 0);
    await page.unroute('**/scan-failed.mp3*');
    await page.unroute('**/scan-late.mp3*');
    await page.goto(origin + '/integration');
    await page.waitForFunction(() => window.cameraStarts === 1);
    await page.keyboard.press('Space');
    await page.waitForFunction(
        async () =>
            (await (await caches.open('attendance-scan-audio-v2')).keys())
                .length === 5,
    );
    // Wait until all cached bytes have decoded in the actual Blade integration.
    await page.waitForTimeout(150);
    const scan = async (status) => {
        await page.evaluate(
            (status) =>
                document.querySelector('#reader').dispatchEvent(
                    new CustomEvent('scanStarted', {
                        bubbles: true,
                        detail: { qr: status },
                    }),
                ),
            status,
        );
    };
    await scan('success');
    await page.waitForFunction(() => plays === 1);
    await page.click('#close');
    await page.waitForFunction(
        () => !document.querySelector('[data-scan-result]'),
    );
    assert.equal(await page.evaluate(() => stops), 1);
    await scan('late');
    await page.waitForFunction(() => plays === 2);
    await page.keyboard.press('Escape');
    await page.waitForFunction(
        () => !document.querySelector('[data-scan-result]'),
    );
    assert.equal(await page.evaluate(() => stops), 2);
    await scan('failed');
    await page.waitForFunction(() => plays === 3);
    await page.click('#backdrop');
    await page.waitForFunction(
        () => !document.querySelector('[data-scan-result]'),
    );
    await scan('already_recorded');
    await page.waitForFunction(() => plays === 4);
    await page.waitForFunction(
        () => !document.querySelector('[data-scan-result]'),
    );
    await scan('transport');
    await page.waitForFunction(() => plays === 5);
    await page.click('[data-close-scan]');
    await page.waitForFunction(
        () => !document.querySelector('[data-scan-result]'),
    );
    await scan('success');
    await page.waitForFunction(() => plays === 6);
    await page.evaluate(() =>
        document.dispatchEvent(new Event('livewire:navigating')),
    );
    assert.equal(await page.evaluate(() => stops), 6);
    await page.evaluate(() => cleanups.forEach((fn) => fn()));
    console.log(
        'Actual Blade script integration: close button, backdrop, Escape, auto-close, transport failure and SPA cleanup passed.',
    );
    console.log(
        'Chromium DevTools Network: cold=5, warm reload=0 additional, deleted cache=5 additional. All MP3s decoded. Five statuses, button/OK/backdrop/Escape/automatic close, duplicate result, replacement, disposal passed.',
    );
} finally {
    await browser?.close();
    server.close();
}
