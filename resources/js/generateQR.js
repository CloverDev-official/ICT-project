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

window.generateQRPNG = async (text) => {
    const writeOutput = await writeBarcode(text, writerOptions);
    
    return new Uint8Array(
        await writeOutput.image.arrayBuffer()
    );
}

window.generateQRSVG = async (text) => {
    const writeOutput = await writeBarcode(text, writerOptions);
    
    return writeOutput.svg;
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