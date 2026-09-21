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
    let submittedAt = 0;
    let submissions = 0;

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
            const activeWorker = worker;
            worker.addEventListener('message', ({ data }) => {
                if (disposed || worker !== activeWorker) return;
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
                        onResult({
                            ...data,
                            duration: performance.now() - submittedAt,
                        });
                    }
                }
            });
            worker.addEventListener('error', (event) => {
                event.preventDefault?.();
                if (disposed || worker !== activeWorker) return;
                failWorker(event.error || new Error(event.message));
            });
            worker.addEventListener('messageerror', () => {
                if (disposed || worker !== activeWorker) return;
                failWorker(
                    new Error('Worker tidak dapat membaca frame kamera.'),
                );
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
            worker ? 640 : 480,
        );
        fallbackCanvas ??= document.createElement('canvas');
        if (fallbackCanvas.width !== frame.outputWidth)
            fallbackCanvas.width = frame.outputWidth;
        if (fallbackCanvas.height !== frame.outputHeight)
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
            () =>
                failWorker(new Error('QR worker melewati batas waktu decode.')),
            WORKER_TIMEOUT,
        );
    }

    async function submitToWorker(video, fullFrame, intensive) {
        const activeWorker = worker;
        // Cover capture as well as decoding, so a stuck bitmap cannot lock scanning.
        armWorkerTimeout();
        if (bitmapSupported && workerOffscreenCanvas) {
            let bitmap;
            try {
                bitmap = await createImageBitmap(video);
                if (disposed || worker !== activeWorker) {
                    bitmap.close?.();
                    return;
                }
                worker.postMessage(
                    { type: 'decode', bitmap, fullFrame, intensive },
                    [bitmap],
                );
                bitmap = null;
                return;
            } catch {
                bitmap?.close?.();
                if (disposed || worker !== activeWorker) return;
                bitmapSupported = false;
            }
        }

        const { frame, imageData } = capturePixels(video, fullFrame);
        worker.postMessage(
            { type: 'decode', frame, pixels: imageData.data.buffer, intensive },
            [imageData.data.buffer],
        );
    }

    async function submitToMainThread(video, fullFrame, intensive) {
        const startedAt = performance.now();
        try {
            const { frame, imageData } = capturePixels(video, fullFrame);
            const { decodeQRCode } = await import('./decoder.js');
            if (disposed) return;
            const results = await decodeQRCode(imageData, intensive);
            if (disposed) return;
            const code = results?.[0];
            onResult({
                duration: performance.now() - startedAt,
                frame,
                code: code
                    ? { text: code.text, position: code.position }
                    : null,
            });
        } catch (error) {
            if (!disposed) onError?.(error, { fallback: false });
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
            submittedAt = performance.now();
            // Alternate light and intensive center scans; retain intensive full scans.
            submissions++;
            const intensive = fullFrame || submissions % 3 === 0;
            if (worker) {
                void submitToWorker(video, fullFrame, intensive).catch(
                    (error) => {
                        failWorker(error);
                    },
                );
            } else {
                void submitToMainThread(video, fullFrame, intensive);
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
