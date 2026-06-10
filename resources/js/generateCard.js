import { jsPDF } from "jspdf";

const BASE_WIDTH = 900;
const BASE_HEIGHT = 540;
const CARD_MM_WIDTH = 85.6;
const CARD_MM_HEIGHT = 53.98;
const CARD_DPI = 300;
const PDF_IMAGE_FORMAT = "JPEG";
const PDF_JPEG_QUALITY = 0.9;
const CARD_WIDTH = Math.round((CARD_MM_WIDTH / 25.4) * CARD_DPI);
const CARD_HEIGHT = Math.round((CARD_MM_HEIGHT / 25.4) * CARD_DPI);
const TAU = Math.PI * 2;

const imageCache = new Map();
const imageElementCache = new Map();
const logoUrl = "/assets/img/logo_smkn_2.png";

const truncateText = (value, maxLength) => {
    const text = String(value ?? "").trim();

    if (text.length <= maxLength) return text;

    const sliceLength = Math.max(0, maxLength - 3);
    return `${text.slice(0, sliceLength)}...`;
};

const blobToDataUrl = (blob) => {
    return new Promise((resolve) => {
        const reader = new FileReader();

        reader.onload = () => resolve(reader.result);
        reader.onerror = () => resolve(null);
        reader.readAsDataURL(blob);
    });
};

const fetchAsDataUrl = async (url, useCache = true) => {
    if (!url) return null;

    if (useCache && imageCache.has(url)) {
        return imageCache.get(url);
    }

    try {
        const response = await fetch(url, {
            cache: "force-cache"
        });

        if (!response.ok) {
            return null;
        }

        const blob = await response.blob();
        const dataUrl = await blobToDataUrl(blob);

        if (useCache) {
            imageCache.set(url, dataUrl);
        }
        return dataUrl;
    } catch (error) {
        console.error("Failed to load image:", url, error);
        return null;
    }
};

const encodeSvgDataUrl = (svgString) => {
    if (!svgString) return null;

    const encoded = btoa(unescape(encodeURIComponent(svgString)));
    return `data:image/svg+xml;base64,${encoded}`;
};

const loadImageFromUrl = (url, useCache = true) => {
    if (!url) return Promise.resolve(null);

    if (useCache && imageElementCache.has(url)) {
        return Promise.resolve(imageElementCache.get(url));
    }

    return new Promise((resolve) => {
        const image = new Image();
        image.onload = () => {
            if (useCache) {
                imageElementCache.set(url, image);
            }
            resolve(image);
        };
        image.onerror = () => resolve(null);
        image.src = url;
    });
};

const createHtmlCanvas = () => {
    const canvas = document.createElement("canvas");
    canvas.width = CARD_WIDTH;
    canvas.height = CARD_HEIGHT;
    return canvas;
};

let sharedCanvas = null;
let sharedContext = null;

const getSharedCanvas = () => {
    if (!sharedCanvas) {
        sharedCanvas = createHtmlCanvas();
        sharedContext = sharedCanvas.getContext("2d");
    }

    return {
        canvas: sharedCanvas,
        ctx: sharedContext
    };
};

const roundRectPath = (ctx, x, y, width, height, radius) => {
    const r = Math.min(radius, width / 2, height / 2);
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + width - r, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + r);
    ctx.lineTo(x + width, y + height - r);
    ctx.quadraticCurveTo(x + width, y + height, x + width - r, y + height);
    ctx.lineTo(x + r, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - r);
    ctx.lineTo(x, y + r);
    ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath();
};

const applyObjectFitCover = (ctx, image, x, y, width, height) => {
    if (!image) return;
    const sourceWidth = image.naturalWidth || image.width;
    const sourceHeight = image.naturalHeight || image.height;
    if (!sourceWidth || !sourceHeight) return;

    const scale = Math.max(width / sourceWidth, height / sourceHeight);
    const drawWidth = sourceWidth * scale;
    const drawHeight = sourceHeight * scale;
    const drawX = x + (width - drawWidth) / 2;
    const drawY = y + (height - drawHeight) / 2;
    ctx.drawImage(image, drawX, drawY, drawWidth, drawHeight);
};

const setFont = (ctx, weight, size, style = "normal") => {
    ctx.font = `${style} ${weight} ${size}px Arial, Helvetica, sans-serif`;
};

const measureLetterSpacing = (ctx, text, letterSpacing) => {
    if (!letterSpacing) return ctx.measureText(text).width;
    return ctx.measureText(text).width + Math.max(0, text.length - 1) * letterSpacing;
};

const drawLetterSpacingText = (ctx, text, x, y, letterSpacing = 0, options = {}) => {
    const upperText = options.uppercase ? text.toUpperCase() : text;
    let drawX = x;
    const measuredWidth = measureLetterSpacing(ctx, upperText, letterSpacing);

    if (options.align === "center") drawX -= measuredWidth / 2;
    if (options.align === "right") drawX -= measuredWidth;

    for (const char of upperText) {
        ctx.fillText(char, drawX, y);
        drawX += ctx.measureText(char).width + letterSpacing;
    }
};

const wrapTextByWords = (ctx, text, maxWidth) => {
    const words = String(text ?? "").trim().split(/\s+/);
    const lines = [];
    let line = "";

    for (const word of words) {
        const candidate = line ? `${line} ${word}` : word;
        if (ctx.measureText(candidate).width <= maxWidth || !line) {
            line = candidate;
        } else {
            lines.push(line);
            line = word;
        }
    }

    if (line) lines.push(line);
    return lines;
};

const drawWrappedText = (ctx, text, x, y, maxWidth, lineHeight, maxLines = Infinity) => {
    const lines = wrapTextByWords(ctx, text, maxWidth);
    const visibleLines = lines.slice(0, maxLines);

    visibleLines.forEach((line, index) => {
        ctx.fillText(line, x, y + index * lineHeight);
    });

    return visibleLines.length;
};

const drawSkewedRectangle = (ctx, x, y, width, height, skewDeg, fillStyle) => {
    const skew = Math.tan((skewDeg * Math.PI) / 180);
    const cx = x + width / 2;
    const cy = y + height / 2;
    const points = [
        [x, y],
        [x + width, y],
        [x + width, y + height],
        [x, y + height]
    ].map(([px, py]) => [cx + (px - cx) + skew * (py - cy), py]);

    ctx.fillStyle = fillStyle;
    ctx.beginPath();
    ctx.moveTo(points[0][0], points[0][1]);
    points.slice(1).forEach(([px, py]) => ctx.lineTo(px, py));
    ctx.closePath();
    ctx.fill();
};

const iconPaths = {
    kelas: new Path2D("M12 3L1 9l11 6l9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17z"),
    jurusan: new Path2D("M12 21.5c-1.35-.85-3.8-1.5-5.5-1.5c-1.65 0-3.35.3-4.75 1.05c-.1.05-.15.05-.25.05c-.25 0-.5-.25-.5-.5V6c.6-.45 1.25-.75 2-1c1.11-.35 2.33-.5 3.5-.5c1.95 0 4.05.4 5.5 1.5c1.45-1.1 3.55-1.5 5.5-1.5c1.17 0 2.39.15 3.5.5c.75.25 1.4.55 2 1v14.6c0 .25-.25.5-.5.5c-.1 0-.15 0-.25-.05c-1.4-.75-3.1-1.05-4.75-1.05c-1.7 0-4.15.65-5.5 1.5M12 8v11.5c1.35-.85 3.8-1.5 5.5-1.5c1.2 0 2.4.15 3.5.5V7c-1.1-.35-2.3-.5-3.5-.5c-1.7 0-4.15.65-5.5 1.5m1 3.5c1.11-.68 2.6-1 4.5-1c.91 0 1.76.09 2.5.28V9.23c-.87-.15-1.71-.23-2.5-.23q-2.655 0-4.5.84zm4.5.17c-1.71 0-3.21.26-4.5.79v1.69c1.11-.65 2.6-.99 4.5-.99c1.04 0 1.88.08 2.5.24v-1.5c-.87-.16-1.71-.23-2.5-.23m2.5 2.9c-.87-.16-1.71-.24-2.5-.24c-1.83 0-3.33.27-4.5.8v1.69c1.11-.66 2.6-.99 4.5-.99c1.04 0 1.88.08 2.5.24z"),
    nisn: new Path2D("M2 3h20c1.05 0 2 .95 2 2v14c0 1.05-.95 2-2 2H2c-1.05 0-2-.95-2-2V5c0-1.05.95-2 2-2m12 3v1h8V6zm0 2v1h8V8zm0 2v1h7v-1zm-6 3.91C6 13.91 2 15 2 17v1h12v-1c0-2-4-3.09-6-3.09M8 6a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3")
};

const drawIcon = (ctx, centerX, centerY, iconKey) => {
    const pathData = iconPaths[iconKey];
    if (!pathData) return;
    ctx.save();
    ctx.fillStyle = "#0b4fa8";
    ctx.beginPath();
    ctx.arc(centerX, centerY, 15, 0, TAU);
    ctx.fill();

    ctx.fillStyle = "#ffffff";
    ctx.translate(centerX - 11, centerY - 11);
    ctx.scale(22 / 24, 22 / 24);
    ctx.fill(pathData);
    ctx.restore();
};

const drawBackgroundLayer = (ctx) => {
    const baseGradient = ctx.createLinearGradient(0, 0, BASE_WIDTH, BASE_HEIGHT);
    baseGradient.addColorStop(0, "#ffffff");
    baseGradient.addColorStop(0.58, "#eef5ff");
    baseGradient.addColorStop(1, "#d7e9ff");
    ctx.fillStyle = baseGradient;
    ctx.fillRect(0, 0, BASE_WIDTH, BASE_HEIGHT);

    ctx.fillStyle = "rgba(255, 255, 255, 0.78)";
    ctx.fillRect(0, 0, BASE_WIDTH, BASE_HEIGHT);
    ctx.globalAlpha = 0.45;
    ctx.fillRect(0, 0, BASE_WIDTH, BASE_HEIGHT);
    ctx.globalAlpha = 1;
};

let staticLayerCanvas = null;
let staticLayerLogoSrc = null;

const drawStaticLayer = (ctx, logoImage) => {
    ctx.clearRect(0, 0, BASE_WIDTH, BASE_HEIGHT);
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = "high";

    ctx.save();
    roundRectPath(ctx, 1.5, 1.5, BASE_WIDTH - 3, BASE_HEIGHT - 3, 24);
    ctx.clip();

    drawBackgroundLayer(ctx);

    const topShapeGradient = ctx.createLinearGradient(720, -80, 1020, 160);
    topShapeGradient.addColorStop(0, "#0b6fdc");
    topShapeGradient.addColorStop(1, "#08245c");
    drawSkewedRectangle(ctx, 720, -80, 300, 240, 28, topShapeGradient);

    ctx.fillStyle = "#05275c";
    ctx.fillRect(0, 460, BASE_WIDTH, 80);

    if (logoImage) {
        applyObjectFitCover(ctx, logoImage, 36, 28, 108, 108);
    }

    ctx.fillStyle = "#06275e";
    setFont(ctx, 900, 42);
    ctx.textBaseline = "alphabetic";
    drawLetterSpacingText(ctx, "SMKN 2 BANJARMASIN", 168, 82, 2, {
        uppercase: false
    });

    ctx.fillStyle = "#1761ae";
    setFont(ctx, 700, 14);
    drawLetterSpacingText(ctx, "Jl. Brigjend H. Hasan Basri No. 6, Banjarmasin - Kalimantan Selatan 70123", 168, 118, 0, {
        uppercase: false
    });

    ctx.textAlign = "right";
    ctx.textBaseline = "top";
    setFont(ctx, 400, 18, "italic");
    ctx.fillStyle = "#ffffff";
    ctx.fillText("Berilmu", 870, 40);
    ctx.fillText("Berkarakter", 870, 61.6);
    ctx.fillStyle = "#f8cf28";
    setFont(ctx, 800, 18, "italic");
    ctx.fillText("Berprestasi", 870, 83.2);
    ctx.textAlign = "left";

    const labelText = "KARTU PELAJAR";
    setFont(ctx, 900, 27);
    const labelX = 272;
    const labelY = 178;
    const labelH = 59;
    const labelW = measureLetterSpacing(ctx, labelText, 5) + 96;

    ctx.save();
    ctx.shadowColor = "rgba(0, 35, 90, 0.18)";
    ctx.shadowBlur = 18;
    ctx.shadowOffsetY = 10;
    ctx.fillStyle = "#08275d";
    roundRectPath(ctx, labelX, labelY, labelW, labelH, 4);
    ctx.fill();
    ctx.restore();

    const ribbonGradient = ctx.createLinearGradient(labelX + labelW - 28, labelY, labelX + labelW + 42, labelY);
    ribbonGradient.addColorStop(0, "#0a86e9");
    ribbonGradient.addColorStop(1, "#5fc7ff");
    ctx.fillStyle = ribbonGradient;
    ctx.beginPath();
    ctx.moveTo(labelX + labelW - 8.4, labelY);
    ctx.lineTo(labelX + labelW + 42, labelY);
    ctx.lineTo(labelX + labelW + 22.4, labelY + labelH);
    ctx.lineTo(labelX + labelW - 28, labelY + labelH);
    ctx.closePath();
    ctx.fill();

    ctx.fillStyle = "#ffffff";
    ctx.textBaseline = "middle";
    setFont(ctx, 900, 27);
    drawLetterSpacingText(ctx, labelText, labelX + 48, labelY + labelH / 2 + 1, 5);

    ctx.save();
    ctx.shadowColor = "rgba(0, 0, 0, 0.2)";
    ctx.shadowBlur = 28;
    ctx.shadowOffsetY = 14;
    ctx.fillStyle = "#ffffff";
    roundRectPath(ctx, 36, 178, 230, 305, 18);
    ctx.fill();
    ctx.restore();

    ctx.save();
    roundRectPath(ctx, 43, 185, 216, 291, 11);
    ctx.clip();
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(43, 185, 216, 291);
    ctx.restore();

    ctx.save();
    ctx.shadowColor = "rgba(0, 0, 0, 0.14)";
    ctx.shadowBlur = 24;
    ctx.shadowOffsetY = 10;
    ctx.fillStyle = "#ffffff";
    roundRectPath(ctx, 630, 250, 230, 230, 14);
    ctx.fill();
    ctx.restore();

    ctx.restore();
};

const getStaticLayer = (logoImage) => {
    const logoSrc = logoImage?.src ?? "";

    if (!staticLayerCanvas || staticLayerLogoSrc !== logoSrc) {
        const canvas = document.createElement("canvas");
        canvas.width = BASE_WIDTH;
        canvas.height = BASE_HEIGHT;
        const ctx = canvas.getContext("2d");

        if (!ctx) {
            return null;
        }

        drawStaticLayer(ctx, logoImage);
        staticLayerCanvas = canvas;
        staticLayerLogoSrc = logoSrc;
    }

    return staticLayerCanvas;
};

const drawCard = (ctx, payload, images) => {
    ctx.clearRect(0, 0, CARD_WIDTH, CARD_HEIGHT);
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, CARD_WIDTH, CARD_HEIGHT);
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = "medium";

    const scaleX = CARD_WIDTH / BASE_WIDTH;
    const scaleY = CARD_HEIGHT / BASE_HEIGHT;

    ctx.save();
    ctx.scale(scaleX, scaleY);

    roundRectPath(ctx, 1.5, 1.5, BASE_WIDTH - 3, BASE_HEIGHT - 3, 24);
    ctx.clip();
    const staticLayer = getStaticLayer(images.logo);
    if (staticLayer) {
        ctx.drawImage(staticLayer, 0, 0);
    } else {
        drawBackgroundLayer(ctx);
    }

    ctx.save();
    roundRectPath(ctx, 43, 185, 216, 291, 11);
    ctx.clip();
    if (images.photo) {
        applyObjectFitCover(ctx, images.photo, 43, 185, 216, 291);
    }
    ctx.restore();

    ctx.fillStyle = "#111827";
    setFont(ctx, 900, 16);
    ctx.textBaseline = "top";
    const nameLineHeight = 19;
    const nameLines = drawWrappedText(
        ctx,
        payload.studentName.toUpperCase(),
        300,
        252,
        300,
        nameLineHeight,
        2
    );

    const rowStartY = 252 + nameLines * nameLineHeight + 10;
    payload.rows.forEach((row, index) => {
        const rowY = rowStartY + index * 50;
        const rowCenterY = rowY + 25;

        ctx.strokeStyle = "rgba(5, 39, 92, 0.18)";
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(300, rowY + 50.5);
        ctx.lineTo(600, rowY + 50.5);
        ctx.stroke();

        drawIcon(ctx, 315, rowCenterY, row.iconKey);

        ctx.fillStyle = "#111827";
        ctx.textBaseline = "middle";
        setFont(ctx, 800, 14);
        drawLetterSpacingText(ctx, row.label, 344, rowCenterY + 0.5, 2, {
            uppercase: true
        });

        ctx.fillStyle = "#05275c";
        setFont(ctx, 900, 14);
        ctx.fillText(":", 462, rowCenterY + 0.5);

        ctx.fillStyle = "#111827";
        setFont(ctx, 900, 12);
        drawLetterSpacingText(ctx, row.value, 480, rowCenterY + 0.5, 1, {
            uppercase: true
        });
    });

    if (images.qr) {
        applyObjectFitCover(ctx, images.qr, 640, 260, 210, 210);
    }

    ctx.strokeStyle = "#082d63";
    ctx.lineWidth = 3;
    roundRectPath(ctx, 1.5, 1.5, BASE_WIDTH - 3, BASE_HEIGHT - 3, 24);
    ctx.stroke();
    ctx.restore();
};


const buildPayload = (murid) => {
    return {
        schoolName: "SMKN 2 Banjarmasin",
        schoolAddress: "Jl. Brigjend H. Hasan Basri No. 6, Banjarmasin - Kalimantan Selatan 70123",
        motto: ["Berilmu", "Berkarakter", "Berprestasi"],
        cardTitle: "Kartu Pelajar",
        studentName: truncateText(murid?.nama ?? "", 40),
        rows: [
            {
                label: "Kelas",
                value: truncateText(murid?.rombel?.nama_lengkap ?? "", 28),
                iconKey: "kelas"
            },
            {
                label: "Jurusan",
                value: truncateText(murid?.rombel?.jurusan?.nama ?? "", 28),
                iconKey: "jurusan"
            },
            {
                label: "NISN",
                value: truncateText(murid?.nisn ?? "", 20),
                iconKey: "nisn"
            }
        ]
    };
};

window.renderStudentCardCanvas = async ({ murid, qrSvg }) => {
    const payload = buildPayload(murid);
    const [photoDataUrl, logoDataUrl] = await Promise.all([
        fetchAsDataUrl(murid?.image_path ?? null, false),
        fetchAsDataUrl(logoUrl, true)
    ]);

    const [photo, logo, qr] = await Promise.all([
        loadImageFromUrl(photoDataUrl, false),
        loadImageFromUrl(logoDataUrl, true),
        loadImageFromUrl(encodeSvgDataUrl(qrSvg), false)
    ]);

    const { canvas, ctx } = getSharedCanvas();
    if (!ctx) return null;

    drawCard(ctx, payload, {
        photo,
        logo,
        qr
    });

    return canvas;
};

const canvasToJpegDataUrl = async (canvas, quality) => {
    if (canvas && typeof canvas.toDataURL === "function") {
        return canvas.toDataURL("image/jpeg", quality);
    }

    if (canvas && typeof canvas.convertToBlob === "function") {
        const blob = await canvas.convertToBlob({
            type: "image/jpeg",
            quality
        });
        return blobToDataUrl(blob);
    }

    return null;
};

const buildPdfDoc = () => {
    return new jsPDF({
        format: "a4",
        orientation: "portrait",
        unit: "mm",
        compress: true
    });
};

const getPdfGrid = (doc) => {
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const margin = 6;
    const gap = 4;
    const availableWidth = pageWidth - margin * 2;
    const availableHeight = pageHeight - margin * 2;
    const cols = Math.max(1, Math.floor((availableWidth + gap) / (CARD_MM_WIDTH + gap)));
    const rows = Math.max(1, Math.floor((availableHeight + gap) / (CARD_MM_HEIGHT + gap)));

    return {
        margin,
        gap,
        cols,
        rows,
        pageWidth,
        pageHeight
    };
};

const pdfState = new Map();

window.resetStudentCardPdf = () => {
    pdfState.clear();
};

window.addStudentCardToPdf = async ({ className, canvas }) => {
    if (!className || !canvas) return;

    let entry = pdfState.get(className);

    if (!entry) {
        const doc = buildPdfDoc();
        const grid = getPdfGrid(doc);

        entry = {
            doc,
            grid,
            col: 0,
            row: 0
        };

        pdfState.set(className, entry);
    }

    const { doc, grid } = entry;
    const x = grid.margin + entry.col * (CARD_MM_WIDTH + grid.gap);
    const y = grid.margin + entry.row * (CARD_MM_HEIGHT + grid.gap);

    const dataUrl = await canvasToJpegDataUrl(canvas, PDF_JPEG_QUALITY);
    if (!dataUrl) return;

    doc.addImage(dataUrl, PDF_IMAGE_FORMAT, x, y, CARD_MM_WIDTH, CARD_MM_HEIGHT, undefined, "FAST");

    entry.col += 1;
    if (entry.col >= grid.cols) {
        entry.col = 0;
        entry.row += 1;
    }

    if (entry.row >= grid.rows) {
        doc.addPage();
        entry.row = 0;
    }
};

window.exportStudentCardPdfs = () => {
    const results = [];

    for (const [className, entry] of pdfState.entries()) {
        const arrayBuffer = entry.doc.output("arraybuffer");
        results.push({
            className,
            pdfBytes: new Uint8Array(arrayBuffer)
        });
    }

    return results;
};

window.clearStudentCardMemory = () => {
    imageCache.clear();
    imageElementCache.clear();

    staticLayerCanvas = null;
    staticLayerLogoSrc = null;

    sharedCanvas = null;
    sharedContext = null;

    pdfState.clear();
};

const sanitizeFileName = (value) => {
    return String(value ?? "")
        .trim()
        .replace(/[\\?%*:|"<>]/g, "-")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-")
        .replace(/^[-.]+|[-.]+$/g, "");
};

const buildStudentCardFileName = (murid) => {
    const className = murid?.rombel?.nama_lengkap ?? "Kelas";
    const name = murid?.nama ?? "Murid";
    const nisn = murid?.nisn ?? "NISN";
    const base = `${className}-${name}-${nisn}`;
    const sanitized = sanitizeFileName(base) || "kartu-pelajar";
    return `${sanitized}.pdf`;
};

const downloadPdfBytes = (fileName, bytes) => {
    const blob = new Blob([bytes], { type: "application/pdf" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
};

window.addEventListener("generateStudentCardPdf", async (event) => {
    const detail = event?.detail ?? {};
    const murid = detail.murid;
    if (!murid) return;

    const qrSvg = await window.generateQRSVG?.(murid.uuid);
    const cardCanvas = await window.renderStudentCardCanvas({
        murid,
        qrSvg
    });

    if (!cardCanvas) return;

    window.resetStudentCardPdf?.();

    const className = murid?.rombel?.nama_lengkap ?? "Kelas";
    await window.addStudentCardToPdf?.({
        className,
        canvas: cardCanvas
    });

    const pdfFiles = window.exportStudentCardPdfs?.() ?? [];
    if (pdfFiles.length === 0) return;

    const pdfName = detail.filename || buildStudentCardFileName(murid);
    downloadPdfBytes(pdfName, pdfFiles[0].pdfBytes);

    window.clearStudentCardMemory?.();
});