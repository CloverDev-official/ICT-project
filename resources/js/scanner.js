// resources/js/scanner.js

import jsQR from "jsqr";

// =========================
// MODULE STATE
// =========================

let animationFrame = null;
let stream         = null;
let videoEl        = null;
let overlayCanvas  = null;
let overlayCtx     = null;
let scanCanvas     = null;
let scanCtx        = null;
let resizeObserver = null;
let isRunning      = false;
let lastScanTime   = 0;

const SCAN_WIDTH  = 640;
const SCAN_HEIGHT = 480;

const INTERVAL_IDLE   = 100;
const INTERVAL_ACTIVE = 50;
let   scanInterval    = INTERVAL_IDLE;

// =========================
// INTERNAL CLEANUP
// =========================

function cleanup() {

    isRunning = false;

    if (animationFrame !== null) {
        cancelAnimationFrame(animationFrame);
        animationFrame = null;
    }

    if (videoEl !== null) {
        videoEl.pause();
        videoEl.srcObject = null;
        videoEl = null;
    }

    if (stream !== null) {
        stream.getTracks().forEach(t => t.stop());
        stream = null;
    }

    if (resizeObserver !== null) {
        resizeObserver.disconnect();
        resizeObserver = null;
    }

    overlayCtx    = null;
    overlayCanvas = null;
    scanCtx       = null;
    scanCanvas    = null;
    lastScanTime  = 0;
    scanInterval  = INTERVAL_IDLE;
}

// =========================
// DESTROY (dipanggil blade)
// Hanya cleanup + kosongkan DOM
// =========================

window.destroyScanner = () => {
    cleanup();

    const reader = document.getElementById("reader");
    if (reader) reader.innerHTML = "";
};

// =========================
// INIT (dipanggil blade)
// TIDAK memanggil destroyScanner —
// blade sudah atur urutannya sendiri
// =========================

window.initScanner = async () => {

    const reader = document.getElementById("reader");
    if (!reader) return;

    // =========================
    // READER CONTAINER
    // =========================

    Object.assign(reader.style, {
        position  : "relative",
        width     : "100%",
        height    : "100%",
        minHeight : "300px",
        overflow  : "hidden",
        background: "#000"
    });

    // =========================
    // VIDEO
    // =========================

    videoEl = document.createElement("video");
    videoEl.autoplay    = true;
    videoEl.playsInline = true;
    videoEl.muted       = true;

    Object.assign(videoEl.style, {
        position : "absolute",
        top      : "0",
        left     : "0",
        width    : "100%",
        height   : "100%",
        objectFit: "cover"
    });

    // =========================
    // OVERLAY CANVAS
    // =========================

    overlayCanvas = document.createElement("canvas");

    Object.assign(overlayCanvas.style, {
        position     : "absolute",
        top          : "0",
        left         : "0",
        width        : "100%",
        height       : "100%",
        pointerEvents: "none",
        zIndex       : "10"
    });

    reader.appendChild(videoEl);
    reader.appendChild(overlayCanvas);

    // =========================
    // SCAN CANVAS (hidden)
    // =========================

    scanCanvas        = document.createElement("canvas");
    scanCanvas.width  = SCAN_WIDTH;
    scanCanvas.height = SCAN_HEIGHT;

    scanCtx = scanCanvas.getContext("2d", {
        willReadFrequently: true,
        alpha             : false
    });

    overlayCtx = overlayCanvas.getContext("2d");

    // =========================
    // RESIZE OVERLAY
    // =========================

    function syncOverlaySize() {
        if (!overlayCanvas || !reader) return;
        const rect           = reader.getBoundingClientRect();
        overlayCanvas.width  = rect.width  || reader.offsetWidth;
        overlayCanvas.height = rect.height || reader.offsetHeight;
    }

    syncOverlaySize();

    resizeObserver = new ResizeObserver(syncOverlaySize);
    resizeObserver.observe(reader);

    // =========================
    // CAMERA
    // =========================

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "environment",
                width     : { ideal: SCAN_WIDTH },
                height    : { ideal: SCAN_HEIGHT }
            },
            audio: false
        });
    } catch (err) {
        console.error("[scanner] Gagal akses kamera:", err);
        return;
    }

    videoEl.srcObject = stream;

    try {
        await videoEl.play();
    } catch (err) {
        console.error("[scanner] Gagal play video:", err);
        return;
    }

    // =========================
    // SCALE POINT
    // =========================

    function scalePoint(point) {
        const vRatio = SCAN_WIDTH  / SCAN_HEIGHT;
        const cRatio = overlayCanvas.width / overlayCanvas.height;

        let dw, dh, ox = 0, oy = 0;

        if (cRatio > vRatio) {
            dw = overlayCanvas.width;
            dh = overlayCanvas.width / vRatio;
            oy = (overlayCanvas.height - dh) / 2;
        } else {
            dh = overlayCanvas.height;
            dw = overlayCanvas.height * vRatio;
            ox = (overlayCanvas.width - dw) / 2;
        }

        return {
            x: (point.x / SCAN_WIDTH)  * dw + ox,
            y: (point.y / SCAN_HEIGHT) * dh + oy
        };
    }

    // =========================
    // DRAW BOX
    // =========================

    function drawBox(location) {
        const pts = [
            scalePoint(location.topLeftCorner),
            scalePoint(location.topRightCorner),
            scalePoint(location.bottomRightCorner),
            scalePoint(location.bottomLeftCorner)
        ];

        overlayCtx.strokeStyle = "#00FF66";
        overlayCtx.lineWidth   = 3;
        overlayCtx.lineJoin    = "round";

        overlayCtx.beginPath();
        overlayCtx.moveTo(pts[0].x, pts[0].y);
        pts.slice(1).forEach(p => overlayCtx.lineTo(p.x, p.y));
        overlayCtx.closePath();
        overlayCtx.stroke();
    }

    // =========================
    // SCAN LOOP
    // =========================

    isRunning = true;

    function scan(timestamp) {
        if (!isRunning) return;

        animationFrame = requestAnimationFrame(scan);

        if (timestamp - lastScanTime < scanInterval) return;
        if (!videoEl || videoEl.readyState < 4) return;

        lastScanTime = timestamp;

        scanCtx.drawImage(videoEl, 0, 0, SCAN_WIDTH, SCAN_HEIGHT);

        const imageData = scanCtx.getImageData(0, 0, SCAN_WIDTH, SCAN_HEIGHT);

        const code = jsQR(
            imageData.data,
            imageData.width,
            imageData.height,
            { inversionAttempts: "attemptBoth" }
        );

        overlayCtx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);

        if (!code) {
            scanInterval = INTERVAL_IDLE;
            return;
        }

        scanInterval = INTERVAL_ACTIVE;
        drawBox(code.location);

        if (!window.scanned) {
            window.scanned = true;
            Livewire.dispatch("verifiedQRCode", code.data);
        }
    }

    animationFrame = requestAnimationFrame(scan);
};