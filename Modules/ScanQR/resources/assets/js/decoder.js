import zxingWasmUrl from 'zxing-wasm/reader/zxing_reader.wasm?url';
import { prepareZXingModule, readBarcodes } from 'zxing-wasm/reader';

let decoderReady;

export function prepareDecoder() {
    decoderReady ??= Promise.resolve(
        prepareZXingModule({
            overrides: {
                // Keep the runtime decoder local and let Vite fingerprint the file.
                locateFile: (path, prefix) =>
                    path.endsWith('.wasm') ? zxingWasmUrl : prefix + path,
            },
            fireImmediately: true,
        }),
    );
    return decoderReady;
}

export async function decodeQRCode(imageData, intensive = true) {
    await prepareDecoder();
    return readBarcodes(imageData, {
        formats: ['QRCode'],
        maxNumberOfSymbols: 1,
        tryHarder: intensive,
        tryRotate: true,
        tryInvert: intensive,
        // Crop/resize is already handled before ZXing receives the pixels.
        tryDownscale: false,
        tryDenoise: false,
    });
}
