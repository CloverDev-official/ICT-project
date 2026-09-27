import { prepareZXingModule, writeBarcode } from 'zxing-wasm/writer';

import { Zip, ZipPassThrough } from 'fflate';

window.Zip = Zip;
window.ZipPassThrough = ZipPassThrough;

const writerOptions = {
    format: 'QRCode',
    scale: 5,
    options: "ecLevel=M",    // symbology-specific options string
    // addHRT: true,            // add human readable text
    addQuietZones: false,     // add quiet zones (default)
    // invert: false,           // invert colors
};

// QR values are immutable per student. Keep only the small, ready-to-use
// representations instead of ZXing's complete result (which also contains an
// uncompressed symbol buffer). This avoids repeat WASM work without allowing a
// long batch to retain a large amount of memory.
const CACHE_LIMIT = 256;
const svgCache = new Map();
const pngCache = new Map();
const writeRequests = new Map();

const cacheValue = (cache, key, value) => {
    cache.delete(key);
    cache.set(key, value);

    if (cache.size > CACHE_LIMIT) {
        cache.delete(cache.keys().next().value);
    }

    return value;
};

const getCacheValue = (cache, key) => {
    if (!cache.has(key)) return null;

    return cacheValue(cache, key, cache.get(key));
};

const getWriteOutput = (text) => {
    const key = String(text ?? '');

    if (writeRequests.has(key)) return writeRequests.get(key);

    const request = writeBarcode(key, writerOptions)
        .finally(() => writeRequests.delete(key));

    writeRequests.set(key, request);
    return request;
};

const getPngBytes = async (text) => {
    const key = String(text ?? '');
    const cached = getCacheValue(pngCache, key);

    if (cached) return cached;

    const writeOutput = await getWriteOutput(key);
    const bytes = new Uint8Array(await writeOutput.image.arrayBuffer());

    return cacheValue(pngCache, key, bytes);
};

const getPngBlob = async (text) => new Blob([await getPngBytes(text)], {
    type: 'image/png',
});

window.generateQRPNG = async (text) => {
    // Return a new view, as before, so consumers cannot mutate the cache.
    return (await getPngBytes(text)).slice();
};

window.generateQRSVG = async (text) => {
    const key = String(text ?? '');
    if (!key) return '';
    const cached = getCacheValue(svgCache, key);
    if (cached) return cached;

    const request = getWriteOutput(key)
        .then((writeOutput) => {
            const svg = writeOutput.svg || '';
            return svg ? cacheValue(svgCache, key, svg) : svg;
        });

    return request;
};

window.addEventListener('generateQRPNGDownload', async (event) => {
    const { text, filename } = event.detail;

    const blob = await getPngBlob(text);

    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = filename;

    document.body.appendChild(a);
    a.click();
    a.remove();

    URL.revokeObjectURL(url);
});

// Fetch and compile the WASM while the browser is idle. Actual QR output still
// uses exactly the same writer options above; this only removes first-use delay.
const warmUpWriter = () => {
    prepareZXingModule({ fireImmediately: true }).catch(() => {
        // A later writeBarcode call retains the library's normal error handling.
    });
};

if (typeof window.requestIdleCallback === 'function') {
    window.requestIdleCallback(warmUpWriter, { timeout: 1500 });
} else {
    window.setTimeout(warmUpWriter, 0);
}
