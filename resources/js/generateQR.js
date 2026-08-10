import { writeBarcode } from 'zxing-wasm/writer';

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

// QR values are immutable per student. Reusing both completed and in-flight
// generations prevents duplicate WASM work when the same card is requested by
// a table action and by the batch exporter.
const svgCache = new Map();
const svgRequests = new Map();

window.generateQRPNG = async (text) => {
    const writeOutput = await writeBarcode(text, writerOptions);
    
    return new Uint8Array(
        await writeOutput.image.arrayBuffer()
    );
}

window.generateQRSVG = async (text) => {
    const key = String(text ?? '');
    if (!key) return '';
    if (svgCache.has(key)) return svgCache.get(key);
    if (svgRequests.has(key)) return svgRequests.get(key);

    const request = writeBarcode(key, writerOptions)
        .then((writeOutput) => {
            const svg = writeOutput.svg || '';
            svgCache.set(key, svg);
            return svg;
        })
        .finally(() => svgRequests.delete(key));

    svgRequests.set(key, request);
    return request;
}

window.addEventListener('generateQRPNGDownload', async (event) => {
    const { text, filename } = event.detail;

    const writeOutput = await writeBarcode(text, writerOptions);

    const blob = await writeOutput.image;

    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = filename;

    document.body.appendChild(a);
    a.click();
    a.remove();

    URL.revokeObjectURL(url);
});
