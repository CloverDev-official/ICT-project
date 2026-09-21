import { createFramePlan } from './frame-utils.js';

const WORKER_TIMEOUT = 5000;

export function createDecoderPipeline({ onResult, onError }) {
    let worker = null;
    let workerReady = false;
    let workerOffscreenCanvas = false;
    let workerErrors = 0;
    let bitmapSupported = typeof globalThis.createImageBitmap === 'function';
    let busy = false;
    let disposed = false;
    let timeout = null;
    let fallbackCanvas = null;
    let fallbackContext = null;

    function clearDecodeTimeout() {
        clearTimeout(timeout);
        timeout = null;
    }

    function failWorker(error) {
        clearDecodeTimeout();
        worker?.terminate();
        worker = null;
        workerReady = false;
        workerOffscreenCanvas = false;
        busy = false;
        if (!disposed) onError?.(error, { fallback: true });
    }

    if (typeof globalThis.Worker === 'function') {
        try {
            worker = new Worker(new URL('./qr-worker.js', import.meta.url), {
                type: 'module',
                name: 'scanqr-decoder',
            });
            worker.addEventListener('message', ({ data }) => {
                if (disposed) return;
                if (data?.type === 'ready') {
                    clearDecodeTimeout();
                    workerReady = true;
                    workerOffscreenCanvas = data.offscreenCanvas;
                    workerErrors = 0;
                    return;
                }
                if (data?.type === 'fatal') {
                    failWorker(new Error(data.message));
                    return;
                }
                if (data?.type === 'result' || data?.type === 'decode-error') {
                    clearDecodeTimeout();
                    busy = false;
                    if (data.type === 'decode-error') {
                        workerErrors++;
                        onError?.(new Error(data.message), { fallback: false });
                        if (workerErrors >= 3) {
                            failWorker(
                                new Error(
                                    'QR worker gagal melakukan decode berulang kali.',
                                ),
                            );
                        }
                    } else {
                        workerErrors = 0;
                        onResult(data);
                    }
                }
            });
            worker.addEventListener('error', (event) => {
                event.preventDefault?.();
                failWorker(event.error || new Error(event.message));
            });
            worker.addEventListener('messageerror', () => {
                failWorker(new Error('Worker tidak dapat membaca frame kamera.'));
            });
            timeout = setTimeout(
                () => failWorker(new Error('QR worker gagal dimulai.')),
                WORKER_TIMEOUT * 2,
            );
        } catch (error) {
            failWorker(error);
        }
    }

    function capturePixels(video, fullFrame) {
        const frame = createFramePlan(
            video.videoWidth || video.width,
            video.videoHeight || video.height,
            fullFrame,
        );
        fallbackCanvas ??= document.createElement('canvas');
        fallbackCanvas.width = frame.outputWidth;
        fallbackCanvas.height = frame.outputHeight;
        fallbackContext ??= fallbackCanvas.getContext('2d', {
            alpha: false,
            willReadFrequently: true,
        });
        const { x, y, width, height } = frame.crop;
        fallbackContext.drawImage(
            video,
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
            frame,
            imageData: fallbackContext.getImageData(
                0,
                0,
                frame.outputWidth,
                frame.outputHeight,
            ),
        };
    }

    function armWorkerTimeout() {
        clearDecodeTimeout();
        timeout = setTimeout(
            () => failWorker(new Error('QR worker melewati batas waktu decode.')),
            WORKER_TIMEOUT,
        );
    }

    async function submitToWorker(video, fullFrame) {
        if (bitmapSupported && workerOffscreenCanvas) {
            let bitmap;
            try {
                bitmap = await createImageBitmap(video);
                if (disposed || !worker) {
                    bitmap.close?.();
                    busy = false;
                    return;
                }
                worker.postMessage(
                    { type: 'decode', bitmap, fullFrame },
                    [bitmap],
                );
                bitmap = null;
                armWorkerTimeout();
                return;
            } catch {
                bitmap?.close?.();
                bitmapSupported = false;
            }
        }

        const { frame, imageData } = capturePixels(video, fullFrame);
        worker.postMessage(
            { type: 'decode', frame, pixels: imageData.data.buffer },
            [imageData.data.buffer],
        );
        armWorkerTimeout();
    }

    async function submitToMainThread(video, fullFrame) {
        const startedAt = performance.now();
        try {
            const { frame, imageData } = capturePixels(video, fullFrame);
            const { decodeQRCode } = await import('./decoder.js');
            const results = await decodeQRCode(imageData);
            const code = results?.[0];
            onResult({
                duration: performance.now() - startedAt,
                frame,
                code: code
                    ? { text: code.text, position: code.position }
                    : null,
            });
        } catch (error) {
            onError?.(error, { fallback: false });
        } finally {
            busy = false;
        }
    }

    return {
        isBusy: () => busy,
        isReady: () => !worker || workerReady,
        usesWorker: () => !!worker,
        submit(video, fullFrame = false) {
            if (disposed || busy || (worker && !workerReady)) return false;
            busy = true;
            if (worker) {
                void submitToWorker(video, fullFrame).catch((error) => {
                    failWorker(error);
                });
            } else {
                void submitToMainThread(video, fullFrame);
            }
            return true;
        },
        dispose() {
            disposed = true;
            clearDecodeTimeout();
            worker?.terminate();
            worker = null;
            busy = false;
            fallbackCanvas = null;
            fallbackContext = null;
        },
    };
}
