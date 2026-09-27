import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { createContext, runInContext } from 'node:vm';

const listener = readFileSync(new URL('../../resources/views/components/browser-refresh-listener.blade.php', import.meta.url), 'utf8')
    .split('<script>')[1].split('</script>')[0].replace('@json($version)', '"initial"');
const scanner = readFileSync(new URL('../../Modules/ScanQR/resources/views/livewire/murid/scan.blade.php', import.meta.url), 'utf8')
    .split('@script')[1].split('<script>')[1].split("import('{{ Vite::asset")[0]
    .replace('@json($serverPingUrl)', '"/ping"')
    .replace("@json(($siteSettings['system.browser_refresh_version'] ?? ''))", '"initial"')
    .replace('@json(now()->timestamp * 1000)', 'Date.now()');

function fixture() {
    const timers = new Map();
    let nextId = 0;
    let requests = 0;
    let reloads = 0;
    let response = new Response(null, { status: 204 });
    const window = new EventTarget();
    const document = new EventTarget();
    document.hidden = false;
    document.querySelector = () => null;
    document.getElementById = () => null;
    window.location = { reload: () => reloads++ };
    window.setTimeout = (callback, delay) => {
        timers.set(++nextId, { callback, delay });
        return nextId;
    };
    window.clearTimeout = (id) => timers.delete(id);
    const context = createContext({
        window, document, AbortController, navigator: { onLine: true },
        performance: { now: () => 0 },
        setTimeout: window.setTimeout, clearTimeout: window.clearTimeout,
        setInterval: () => 0, clearInterval: () => {},
        $wire: { __instance: { addCleanup() {} } },
        fetch: async () => { requests++; return response; },
    });
    return {
        context, window, document, timers,
        requests: () => requests, reloads: () => reloads,
        respond: (value) => { response = value; },
        run: (code = listener) => runInContext(code, context),
        tick: async () => {
            const [id, timer] = timers.entries().next().value;
            timers.delete(id);
            await timer.callback();
        },
    };
}

test('repeated layout initialization and Livewire navigation retain only one refresh timer', async () => {
    const f = fixture();
    for (let i = 0; i < 20; i++) f.run();
    assert.equal(f.timers.size, 1);
    await f.tick();
    assert.equal(f.requests(), 1);
    f.document.dispatchEvent(new Event('livewire:navigating'));
    assert.equal(f.timers.size, 0);
    f.run();
    assert.equal(f.timers.size, 1);
    await f.tick();
    assert.equal(f.requests(), 2);
});

test('429 respects Retry-After even when the tab becomes visible again', async () => {
    const f = fixture();
    f.respond(new Response(null, { status: 429, headers: { 'Retry-After': '60' } }));
    f.run();
    await f.tick();
    assert.ok([...f.timers.values()][0].delay > 59000);
    f.document.dispatchEvent(new Event('visibilitychange'));
    assert.equal(f.requests(), 1);
});

test('scanner layout stops old general listener and does not create a second poller', () => {
    const f = fixture();
    f.run();
    f.document.querySelector = () => ({});
    f.run();
    assert.equal(f.timers.size, 0);
});

test('new refresh command reloads once and expired login stops polling', async () => {
    for (const status of [204, 401]) {
        const f = fixture();
        f.respond(new Response(null, { status, headers: { 'X-Browser-Refresh-Version': 'new' } }));
        f.run();
        await f.tick();
        assert.equal(f.reloads(), status === 204 ? 1 : 0);
        assert.equal(f.timers.size, 0);
    }
});

test('ScanQR ping and refresh run while scan result modal pauses the camera', async () => {
    const f = fixture();
    f.window.scanned = true;
    f.document.querySelector = () => ({ hidden: false });
    f.run(scanner);
    await f.tick();
    assert.equal(f.requests(), 1);
    assert.equal(f.reloads(), 0);
    f.respond(new Response(null, { status: 204, headers: { 'X-Browser-Refresh-Version': 'new' } }));
    await f.tick();
    assert.equal(f.requests(), 2);
    assert.equal(f.reloads(), 1);
});
