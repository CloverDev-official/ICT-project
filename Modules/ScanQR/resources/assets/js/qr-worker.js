import { decodeQRCode, prepareDecoder } from './decoder.js';
import { createFramePlan } from './frame-utils.js';

let canvas;
let context;

function serializePosition(position) {
    if (!position) return null;
    return {
        topLeft: position.topLeft,
        topRight: position.topRight,
        bottomRight: position.bottomRight,
        bottomLeft: position.bottomLeft,
    };
}

function pixelsFromBitmap(bitmap, fullFrame) {
    const frame = createFramePlan(bitmap.width, bitmap.height, fullFrame);
    canvas ??= new OffscreenCanvas(frame.outputWidth, frame.outputHeight);
    canvas.width = frame.outputWidth;
    canvas.height = frame.outputHeight;
    context ??= canvas.getContext('2d', {
        alpha: false,
        willReadFrequently: true,
    });
    const { x, y, width, height } = frame.crop;
    context.drawImage(
        bitmap,
        x,
        y,
        width,
        height,
        0,
        0,
        frame.outputWidth,
        frame.outputHeight,
    );
    return {
        imageData: context.getImageData(0, 0, canvas.width, canvas.height),
        frame,
    };
}

async function initialize() {
    try {
        await prepareDecoder();
        self.postMessage({
            type: 'ready',
            offscreenCanvas: typeof OffscreenCanvas === 'function',
        });
    } catch (error) {
        self.postMessage({
            type: 'fatal',
            message: error?.message || String(error),
        });
    }
}

self.addEventListener('message', async ({ data }) => {
    if (data?.type !== 'decode') return;
    const startedAt = performance.now();
    let bitmap = data.bitmap;

    try {
        let imageData;
        let frame = data.frame;
        if (bitmap) {
            ({ imageData, frame } = pixelsFromBitmap(bitmap, data.fullFrame));
        } else {
            imageData = {
                data: new Uint8ClampedArray(data.pixels),
                width: frame.outputWidth,
                height: frame.outputHeight,
            };
        }

        const results = await decodeQRCode(imageData);
        const code = results?.[0];
        self.postMessage({
            type: 'result',
            duration: performance.now() - startedAt,
            frame,
            code: code
                ? { text: code.text, position: serializePosition(code.position) }
                : null,
        });
    } catch (error) {
        self.postMessage({
            type: 'decode-error',
            duration: performance.now() - startedAt,
            message: error?.message || String(error),
        });
    } finally {
        bitmap?.close?.();
        bitmap = null;
    }
});

void initialize();
