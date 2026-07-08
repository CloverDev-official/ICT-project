import { jsPDF } from "jspdf";
import { domToCanvas } from "modern-screenshot";

const CONFIG = {
    CARD: {
        horizontal: {
            width: 85.6,
            height: 53.98
        }
    },
    PDF: {
        format: "a4",
        unit: "mm",
        orientation: "portrait",
        imageFormat: "JPEG",
        jpegQuality: 0.9,
        backgroundColor: "#ffffff",
        compression: "FAST",
        margin: {
            min: 5,
            max: 12
        },
        gap: {
            min: 3,
            max: 5
        }
    },
    CACHE: {
        canvasLimit: 2
    },
    FONT: {
        stylesheets: [
            "https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        ],
        families: [
            "Arial",
            "Poppins",
            "Inter"
        ],
        weights: [
            400,
            600,
            700,
            800,
            900
        ],
        timeoutMs: 3000
    },
    TEMPLATE: {
        route: (orientation) => `/admin/card-template/${orientation}`,
        selector: ".student-card",
        scale: 1,
        logoUrl: "/assets/img/logo_smkn_2.png",
        frameWidth: 1000,
        frameHeight: 1000,
        transparentPixel: "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=="
    },
    TEXT: {
        studentName: 40,
        className: 28,
        major: 28,
        nisn: 20
    }
};

const templateCache = new Map();
const templateRequestCache = new Map();
const canvasCache = new Map();
const canvasCacheKeys = new WeakMap();
const jpegCache = new WeakMap();
const renderedCanvasRegistry = new Set();
let whiteCanvas = null;
let whiteCanvasContext = null;

const normalizeOrientation = (orientation) => (
    orientation === "vertical" ? "vertical" : "horizontal"
);

const getCardSize = (orientation = "horizontal") => {
    const base = CONFIG.CARD.horizontal;

    if (normalizeOrientation(orientation) === "vertical") {
        return {
            width: base.height,
            height: base.width
        };
    }

    return {
        width: base.width,
        height: base.height
    };
};

const truncateText = (value, maxLength) => {
    const text = String(value ?? "").trim();

    if (text.length <= maxLength) return text;

    return `${text.slice(0, Math.max(0, maxLength - 3))}...`;
};

const sanitizeFileName = (value) => String(value ?? "")
    .trim()
    .replace(/[\\?%*:|"<>]/g, "-")
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-")
    .replace(/^[-.]+|[-.]+$/g, "");

const buildStudentCardFileName = (murid) => {
    const className = murid?.rombel?.nama_lengkap ?? "Kelas";
    const name = murid?.nama ?? "Murid";
    const nisn = murid?.nisn ?? "NISN";
    const sanitized = sanitizeFileName(`${className}-${name}-${nisn}`) || "kartu-pelajar";

    return `${sanitized}.pdf`;
};

const blobToDataUrl = (blob) => new Promise((resolve, reject) => {
    const reader = new FileReader();

    reader.onload = () => resolve(reader.result);
    reader.onerror = () => reject(new Error("Unable to convert blob to data URL."));
    reader.readAsDataURL(blob);
});

const canvasToBlob = (canvas, type, quality) => new Promise((resolve) => {
    if (typeof canvas.toBlob !== "function") {
        resolve(null);
        return;
    }

    canvas.toBlob(resolve, type, quality);
});

const fetchText = async (url, options = {}) => {
    const response = await fetch(url, options);

    if (!response.ok) {
        throw new Error(`Unable to fetch ${url}: ${response.status} ${response.statusText}`);
    }

    return response.text();
};

const toAbsoluteUrl = (url) => {
    if (!url) return null;

    try {
        return new URL(url, window.location.origin).href;
    } catch {
        return url;
    }
};

const getImageSource = (url) => {
    if (!url) return null;
    if (String(url).startsWith("data:")) return url;

    return toAbsoluteUrl(url);
};

// Warms the browser's HTTP cache for a student's photo ahead of time so that,
// by the time renderTemplateCanvas() actually needs it, the image is already
// downloaded and decoding can happen instantly instead of blocking on a
// network round trip. crossOrigin must match what setImageSource() uses below
// (anonymous) or the browser will treat it as a different cache entry and
// fetch it twice.
const prefetchPhoto = (murid) => {
    const src = getImageSource(murid?.image_path ?? null);

    if (!src || src.startsWith("data:")) return;

    const image = new Image();

    image.decoding = "async";
    image.crossOrigin = "anonymous";
    image.src = src;
};

const waitForFrameLoad = (frame) => new Promise((resolve) => {
    frame.addEventListener("load", resolve, { once: true });
});

const waitForImage = async (image) => {
    if (!image) return;

    if (image.complete && image.naturalWidth > 0) return;

    try {
        if (typeof image.decode === "function") {
            await image.decode();
            return;
        }
    } catch {
        // Fall through to load/error events when decode is not reliable.
    }

    await new Promise((resolve) => {
        image.addEventListener("load", resolve, { once: true });
        image.addEventListener("error", resolve, { once: true });
    });
};

const withTimeout = async (promise, timeoutMs) => {
    let timeoutId = null;

    try {
        return await Promise.race([
            promise,
            new Promise((resolve) => {
                timeoutId = setTimeout(resolve, timeoutMs);
            })
        ]);
    } finally {
        clearTimeout(timeoutId);
    }
};

const yieldToBrowser = async () => {
    if (globalThis.scheduler?.yield) {
        await globalThis.scheduler.yield();
        return;
    }

    await new Promise((resolve) => {
        if (document.hidden || typeof requestAnimationFrame !== "function") {
            setTimeout(resolve, 0);
            return;
        }

        requestAnimationFrame(() => resolve());
    });
};

const encodeSvgDataUrl = (svgString) => {
    if (!svgString) return "";

    const encoded = btoa(unescape(encodeURIComponent(svgString)));
    return `data:image/svg+xml;base64,${encoded}`;
};

const getWhiteCanvas = (width, height) => {
    if (!whiteCanvas) {
        whiteCanvas = document.createElement("canvas");
        whiteCanvasContext = whiteCanvas.getContext("2d", {
            alpha: false,
            willReadFrequently: false
        });
    }

    if (!whiteCanvasContext) {
        throw new Error("Unable to create canvas context for PDF export.");
    }

    if (whiteCanvas.width !== width) whiteCanvas.width = width;
    if (whiteCanvas.height !== height) whiteCanvas.height = height;

    return {
        canvas: whiteCanvas,
        context: whiteCanvasContext
    };
};

const canvasToJpegDataUrl = async (canvas, quality = CONFIG.PDF.jpegQuality) => {
    if (!canvas) return null;
    if (jpegCache.has(canvas)) return jpegCache.get(canvas);

    let dataUrl = null;
    const { canvas: flattenedCanvas, context } = getWhiteCanvas(canvas.width, canvas.height);

    context.fillStyle = CONFIG.PDF.backgroundColor;
    context.fillRect(0, 0, flattenedCanvas.width, flattenedCanvas.height);
    context.drawImage(canvas, 0, 0);

    const blob = await canvasToBlob(flattenedCanvas, "image/jpeg", quality);

    if (blob) {
        dataUrl = await blobToDataUrl(blob);
    } else if (typeof flattenedCanvas.toDataURL === "function") {
        dataUrl = flattenedCanvas.toDataURL("image/jpeg", quality);
    } else if (typeof flattenedCanvas.convertToBlob === "function") {
        const convertedBlob = await flattenedCanvas.convertToBlob({
            type: "image/jpeg",
            quality
        });

        dataUrl = await blobToDataUrl(convertedBlob);
    } else {
        throw new Error("Unable to convert canvas to JPEG data URL.");
    }

    jpegCache.set(canvas, dataUrl);

    return dataUrl;
};

const createRenderCacheKey = ({ orientation, murid, qrSvg }) => JSON.stringify({
    orientation: normalizeOrientation(orientation),
    uuid: murid?.uuid ?? "",
    image: murid?.image_path ?? "",
    name: murid?.nama ?? "",
    className: murid?.rombel?.nama_lengkap ?? "",
    major: murid?.rombel?.jurusan?.nama ?? "",
    nisn: murid?.nisn ?? "",
    qrSvg: qrSvg ?? ""
});

const cacheRenderedCanvas = (key, canvas) => {
    if (!key || !canvas) return;

    canvasCache.set(key, canvas);
    canvasCacheKeys.set(canvas, key);
    renderedCanvasRegistry.add(canvas);

    while (canvasCache.size > CONFIG.CACHE.canvasLimit) {
        const oldestKey = canvasCache.keys().next().value;
        const oldestCanvas = canvasCache.get(oldestKey);

        canvasCache.delete(oldestKey);
        renderedCanvasRegistry.delete(oldestCanvas);
    }
};

const releaseRenderedCanvas = (canvas) => {
    const key = canvasCacheKeys.get(canvas);

    if (key) canvasCache.delete(key);

    renderedCanvasRegistry.delete(canvas);
};

const createPdfDocument = () => new jsPDF({
    format: CONFIG.PDF.format,
    orientation: CONFIG.PDF.orientation,
    unit: CONFIG.PDF.unit,
    compress: true
});

const calculateGridAxis = ({ pageLength, cardLength, minMargin, maxMargin, minGap, maxGap }) => {
    let best = null;

    for (let count = 1; count <= 20; count += 1) {
        const remaining = pageLength - (count * cardLength);
        if (remaining < minMargin * 2) break;

        const rawGap = count > 1
            ? Math.min(maxGap, Math.max(minGap, (remaining - minMargin * 2) / (count - 1)))
            : 0;
        const margin = (pageLength - (count * cardLength) - ((count - 1) * rawGap)) / 2;

        if (margin < minMargin) continue;

        best = {
            count,
            gap: rawGap,
            margin: Math.min(maxMargin, margin)
        };
    }

    return best ?? {
        count: 1,
        gap: 0,
        margin: Math.max(minMargin, (pageLength - cardLength) / 2)
    };
};

const getPdfGrid = (doc, orientation = "horizontal") => {
    const size = getCardSize(orientation);
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const columnAxis = calculateGridAxis({
        pageLength: pageWidth,
        cardLength: size.width,
        minMargin: CONFIG.PDF.margin.min,
        maxMargin: CONFIG.PDF.margin.max,
        minGap: CONFIG.PDF.gap.min,
        maxGap: CONFIG.PDF.gap.max
    });
    const rowAxis = calculateGridAxis({
        pageLength: pageHeight,
        cardLength: size.height,
        minMargin: CONFIG.PDF.margin.min,
        maxMargin: CONFIG.PDF.margin.max,
        minGap: CONFIG.PDF.gap.min,
        maxGap: CONFIG.PDF.gap.max
    });

    return {
        rows: rowAxis.count,
        columns: columnAxis.count,
        pageWidth,
        pageHeight,
        card: size,
        margin: {
            x: columnAxis.margin,
            y: rowAxis.margin
        },
        gap: {
            x: columnAxis.gap,
            y: rowAxis.gap
        },
        positions: Array.from({ length: rowAxis.count * columnAxis.count }, (_, index) => {
            const row = Math.floor(index / columnAxis.count);
            const column = index % columnAxis.count;

            return {
                x: columnAxis.margin + (column * (size.width + columnAxis.gap)),
                y: rowAxis.margin + (row * (size.height + rowAxis.gap))
            };
        })
    };
};

const addCardToPdf = async ({ pdf, canvas, orientation = "horizontal", position }) => {
    if (!pdf) throw new Error("PDF document is required.");
    if (!canvas) throw new Error("Canvas is required to add a card to PDF.");

    const size = getCardSize(orientation);
    const dataUrl = await canvasToJpegDataUrl(canvas);

    if (!dataUrl) throw new Error("Unable to create PDF image from canvas.");

    console.log(
        position.x,
        position.y,
        size.width,
        size.height,
    )

    pdf.addImage(
        dataUrl,
        CONFIG.PDF.imageFormat,
        position.x,
        position.y,
        size.width,
        size.height,
        undefined,
        CONFIG.PDF.compression
    );

    releaseRenderedCanvas(canvas);
};

class TemplateLoader {
    constructor({
        templateCacheStore = templateCache
    } = {}) {
        this.templateCache = templateCacheStore;
        this.sessions = new Map();
        this.logoDataUrlPromise = null;
    }

    async fetchTemplate(orientation) {
        const normalizedOrientation = normalizeOrientation(orientation);

        if (this.templateCache.has(normalizedOrientation)) {
            return this.templateCache.get(normalizedOrientation);
        }

        if (templateRequestCache.has(normalizedOrientation)) {
            return templateRequestCache.get(normalizedOrientation);
        }

        const request = fetchText(CONFIG.TEMPLATE.route(normalizedOrientation), {
            cache: "no-store"
        });

        templateRequestCache.set(normalizedOrientation, request);

        try {
            const template = await request;

            this.templateCache.set(normalizedOrientation, template);

            return template;
        } finally {
            templateRequestCache.delete(normalizedOrientation);
        }
    }

    createFontMarkup() {
        const stylesheetLinks = CONFIG.FONT.stylesheets.map((href) => (
            `<link rel="stylesheet" href="${href}">`
        )).join("");

        return [
            '<link rel="preconnect" href="https://fonts.googleapis.com">',
            '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>',
            stylesheetLinks
        ].join("");
    }

    createInitialTemplateHtml(template) {
        const html = template
            .replaceAll("__LOGO__", CONFIG.TEMPLATE.transparentPixel)
            .replaceAll("__PHOTO__", CONFIG.TEMPLATE.transparentPixel)
            .replaceAll("__QR__", CONFIG.TEMPLATE.transparentPixel);
        const fontMarkup = this.createFontMarkup();

        if (html.includes("</head>")) {
            return html.replace("</head>", `${fontMarkup}</head>`);
        }

        return `${fontMarkup}${html}`;
    }

    createFrame() {
        const iframe = document.createElement("iframe");

        iframe.setAttribute("aria-hidden", "true");
        iframe.style.position = "fixed";
        iframe.style.left = "-10000px";
        iframe.style.top = "0";
        iframe.style.width = `${CONFIG.TEMPLATE.frameWidth}px`;
        iframe.style.height = `${CONFIG.TEMPLATE.frameHeight}px`;
        iframe.style.border = "0";
        iframe.style.opacity = "0";
        iframe.style.pointerEvents = "none";
        // Without an explicit z-index, this fixed-position offscreen frame
        // follows default DOM-order stacking and can end up rendered above
        // other fixed/absolute UI (modals, dropdowns, sidebars) in some
        // browsers/layouts, even though it's shifted far off-screen.
        // Pinning it to the bottom of the stack prevents that.
        iframe.style.zIndex = "-1";

        document.body.appendChild(iframe);

        return iframe;
    }

    findTextNode(doc, token) {
        const textFilter = doc.defaultView?.NodeFilter?.SHOW_TEXT ?? 4;
        const walker = doc.createTreeWalker(doc.body, textFilter);
        let node = walker.nextNode();

        while (node) {
            if (node.nodeValue.includes(token)) return node;
            node = walker.nextNode();
        }

        return null;
    }

    findImage(doc, token, altPattern) {
        return doc.querySelector(`img[src="${token}"]`)
            ?? Array.from(doc.querySelectorAll("img")).find((image) => (
                altPattern.test(image.getAttribute("alt") ?? "")
            ))
            ?? null;
    }

    collectSessionRefs(doc) {
        return {
            logoImage: this.findImage(doc, "__LOGO__", /logo/i),
            photoImage: this.findImage(doc, "__PHOTO__", /foto|photo/i),
            qrImage: this.findImage(doc, "__QR__", /qr/i),
            studentNameNode: this.findTextNode(doc, "__STUDENT_NAME__"),
            classNameNode: this.findTextNode(doc, "__KELAS__"),
            majorNode: this.findTextNode(doc, "__JURUSAN__"),
            nisnNode: this.findTextNode(doc, "__NISN__")
        };
    }

    async waitForFonts(doc) {
        // Previously this walked every element in the card with getComputedStyle()
        // to "discover" font families. That forces a synchronous style
        // recalculation over the whole subtree on every session creation, which
        // is pure main-thread cost for no real benefit (the templates only ever
        // use the families already declared in CONFIG.FONT.families). Loading
        // only the known families/weights is just as correct and cheaper.
        const fontSet = doc?.fonts;

        if (!fontSet) return;

        const loads = CONFIG.FONT.families.flatMap((family) => (
            CONFIG.FONT.weights.map((weight) => fontSet.load(`${weight} 16px "${family}"`))
        ));

        await withTimeout(Promise.allSettled(loads), CONFIG.FONT.timeoutMs);
        await withTimeout(fontSet.ready, CONFIG.FONT.timeoutMs);
    }

    async createSession(orientation) {
        const normalizedOrientation = normalizeOrientation(orientation);
        const template = await this.fetchTemplate(normalizedOrientation);
        const frame = this.createFrame();
        const loadPromise = waitForFrameLoad(frame);

        frame.srcdoc = this.createInitialTemplateHtml(template);
        await loadPromise;

        const doc = frame.contentDocument;
        const card = doc?.querySelector(CONFIG.TEMPLATE.selector);

        if (!doc || !card) {
            frame.remove();
            throw new Error(`Card template is missing "${CONFIG.TEMPLATE.selector}".`);
        }

        const session = {
            frame,
            doc,
            card,
            refs: this.collectSessionRefs(doc)
        };

        await this.waitForFonts(doc);

        this.sessions.set(normalizedOrientation, session);

        return session;
    }

    async getSession(orientation) {
        const normalizedOrientation = normalizeOrientation(orientation);
        const session = this.sessions.get(normalizedOrientation);

        if (session?.frame?.isConnected) return session;

        return this.createSession(normalizedOrientation);
    }

    async setImageSource(image, source) {
        if (!image) return;

        const nextSource = source || CONFIG.TEMPLATE.transparentPixel;

        if (image.getAttribute("src") !== nextSource) {
            if (!nextSource.startsWith("data:")) {
                image.crossOrigin = "anonymous";
            } else {
                image.removeAttribute("crossorigin");
            }

            image.decoding = "async";
            image.setAttribute("src", nextSource);
        }

        await waitForImage(image);
    }

    setText(node, value) {
        if (!node) return;

        node.nodeValue = value ?? "";
    }

    getLogoDataUrl() {
        this.logoDataUrlPromise ??= Promise.resolve(getImageSource(CONFIG.TEMPLATE.logoUrl));

        return this.logoDataUrlPromise;
    }

    async applyData(session, { murid, qrSvg }) {
        const [photoSource, logoDataUrl] = await Promise.all([
            Promise.resolve(getImageSource(murid?.image_path ?? null)),
            this.getLogoDataUrl()
        ]);
        const { refs } = session;

        this.setText(refs.studentNameNode, truncateText(murid?.nama ?? "", CONFIG.TEXT.studentName).toUpperCase());
        this.setText(refs.classNameNode, truncateText(murid?.rombel?.nama_lengkap ?? "", CONFIG.TEXT.className));
        this.setText(refs.majorNode, truncateText(murid?.rombel?.jurusan?.nama ?? "", CONFIG.TEXT.major));
        this.setText(refs.nisnNode, truncateText(murid?.nisn ?? "", CONFIG.TEXT.nisn));

        await Promise.all([
            this.setImageSource(refs.logoImage, logoDataUrl),
            this.setImageSource(refs.photoImage, photoSource),
            this.setImageSource(refs.qrImage, encodeSvgDataUrl(qrSvg))
        ]);

        if (session.doc?.fonts?.ready) {
            await session.doc.fonts.ready;
        }
    }

    async renderCanvas({ orientation = "horizontal", murid, qrSvg }) {
        const normalizedOrientation = normalizeOrientation(orientation);
        const cacheKey = createRenderCacheKey({
            orientation: normalizedOrientation,
            murid,
            qrSvg
        });

        if (canvasCache.has(cacheKey)) return canvasCache.get(cacheKey);

        const session = await this.getSession(normalizedOrientation);

        await this.applyData(session, {
            murid,
            qrSvg
        });

        // domToCanvas (modern-screenshot) serializes the card into an SVG
        // <foreignObject> and lets the browser's native renderer draw it, then
        // reads that back into a canvas. Unlike html2canvas — which re-parses
        // CSS and re-implements layout/paint by hand in JS — this leans on
        // rendering the browser already does, which is why it's noticeably
        // faster and more faithful to the real on-screen appearance.
        const canvas = await domToCanvas(session.card, {
            backgroundColor: CONFIG.PDF.backgroundColor,
            scale: CONFIG.TEMPLATE.scale,
            width: session.card.offsetWidth,
            height: session.card.offsetHeight,
            fetch: {
                // Reuse whatever the browser already has cached (including
                // anything warmed up by prefetchPhoto()) instead of re-fetching.
                requestInit: { mode: "cors", cache: "force-cache" },
                placeholderImage: CONFIG.TEMPLATE.transparentPixel
            }
        });

        cacheRenderedCanvas(cacheKey, canvas);

        return canvas;
    }

    async preload(orientations = ["horizontal"]) {
        const uniqueOrientations = [...new Set(orientations.map(normalizeOrientation))];

        await Promise.allSettled([
            this.getLogoDataUrl(),
            ...uniqueOrientations.map((orientation) => this.getSession(orientation))
        ]);
    }

    clear() {
        for (const session of this.sessions.values()) {
            session.frame?.remove();
        }

        this.sessions.clear();
        this.logoDataUrlPromise = null;
    }
}

class PdfManager {
    constructor() {
        this.entries = new Map();
    }

    createPdf() {
        return createPdfDocument();
    }

    createEntry(orientation = "horizontal") {
        const pdf = this.createPdf();

        return {
            pdf,
            orientation: normalizeOrientation(orientation),
            grid: getPdfGrid(pdf, orientation),
            cursor: 0
        };
    }

    resolveEntry(key = "default", orientation = "horizontal") {
        if (!this.entries.has(key)) {
            this.entries.set(key, this.createEntry(orientation));
        }

        return this.entries.get(key);
    }

    nextPosition(entry) {
        if (entry.cursor > 0 && entry.cursor % entry.grid.positions.length === 0) {
            entry.pdf.addPage();
            entry.grid = getPdfGrid(entry.pdf, entry.orientation);
            entry.cursor = 0;
        }

        const position = entry.grid.positions[entry.cursor];
        entry.cursor += 1;

        return position;
    }

    async addCard({ key = "default", canvas, orientation = "horizontal" }) {
        const normalizedOrientation = normalizeOrientation(orientation);
        const entry = this.resolveEntry(key, normalizedOrientation);

        if (entry.orientation !== normalizedOrientation && entry.cursor > 0) {
            entry.pdf.addPage();
            entry.orientation = normalizedOrientation;
            entry.grid = getPdfGrid(entry.pdf, normalizedOrientation);
            entry.cursor = 0;
        }

        await addCardToPdf({
            pdf: entry.pdf,
            canvas,
            orientation: normalizedOrientation,
            position: this.nextPosition(entry)
        });
    }

    export() {
        return Array.from(this.entries.entries()).map(([className, entry]) => ({
            className,
            pdfBytes: new Uint8Array(entry.pdf.output("arraybuffer"))
        }));
    }

    async exportAsync() {
        const results = [];

        for (const [className, entry] of this.entries.entries()) {
            await yieldToBrowser();
            results.push({
                className,
                pdfBytes: new Uint8Array(entry.pdf.output("arraybuffer"))
            });
        }

        return results;
    }

    clear() {
        this.entries.clear();
    }
}

const templateLoader = new TemplateLoader();
const pdfManager = new PdfManager();

/**
 * Render a student card template to canvas.
 *
 * @param {Object} options
 * @param {"horizontal"|"vertical"} options.orientation
 * @param {Object} options.murid
 * @param {string} options.qrSvg
 * @returns {Promise<HTMLCanvasElement>}
 */
const renderTemplateCanvas = async ({ orientation = "horizontal", murid, qrSvg } = {}) => {
    if (!murid) throw new Error("Student data is required to render a card.");

    return templateLoader.renderCanvas({
        orientation: normalizeOrientation(orientation),
        murid,
        qrSvg
    });
};

const downloadPdfBytes = (fileName, bytes) => {
    if (!bytes) throw new Error("PDF bytes are required for download.");

    const blob = new Blob([bytes], { type: "application/pdf" });
    const url = URL.createObjectURL(blob);
    const anchor = document.createElement("a");

    anchor.href = url;
    anchor.download = fileName || "kartu-pelajar.pdf";
    document.body.appendChild(anchor);
    anchor.click();
    anchor.remove();
    URL.revokeObjectURL(url);
};

const getQrSvg = async (murid) => {
    if (!murid?.uuid) return "";
    if (typeof window.generateQRSVG !== "function") return "";

    return window.generateQRSVG(murid.uuid);
};

const renderStudentCanvas = async ({ murid, qrSvg, orientation = "horizontal" }) => renderTemplateCanvas({
    orientation,
    murid,
    qrSvg
});

const createSingleCardPdfBytes = async ({ canvas, orientation = "horizontal" }) => {
    const manager = new PdfManager();

    await manager.addCard({
        key: "single",
        canvas,
        orientation
    });

    return manager.export()[0]?.pdfBytes ?? null;
};

const downloadSingleCardPdf = async ({ murid, orientation = "horizontal", filename }) => {
    if (!murid) throw new Error("Student data is required to generate a PDF.");

    const normalizedOrientation = normalizeOrientation(orientation);
    const qrSvg = await getQrSvg(murid);
    const canvas = await renderStudentCanvas({
        murid,
        qrSvg,
        orientation: normalizedOrientation
    });
    const pdfBytes = await createSingleCardPdfBytes({
        canvas,
        orientation: normalizedOrientation
    });

    if (!pdfBytes) throw new Error("Unable to generate student card PDF.");

    downloadPdfBytes(filename || buildStudentCardFileName(murid), pdfBytes);
};

const downloadGeneratedStudentCardPdf = async ({ murid, orientation = "horizontal", filename }) => {
    if (!murid) throw new Error("Student data is required to generate a PDF.");

    const normalizedOrientation = normalizeOrientation(orientation);
    const qrSvg = await getQrSvg(murid);
    const canvas = await renderStudentCanvas({
        murid,
        qrSvg,
        orientation: normalizedOrientation
    });
    const manager = new PdfManager();

    await manager.addCard({
        key: murid?.rombel?.nama_lengkap ?? "Kelas",
        canvas,
        orientation: normalizedOrientation
    });

    const pdfBytes = manager.export()[0]?.pdfBytes;

    if (!pdfBytes) throw new Error("Unable to export student card PDF.");

    downloadPdfBytes(filename || buildStudentCardFileName(murid), pdfBytes);
};

const groupKeyFor = (murid) => murid?.rombel?.nama_lengkap ?? "Kelas";

/**
 * Render and lay out a batch of student cards into one PDF per class,
 * without freezing the UI.
 *
 * The render itself is still synchronous main-thread work (domToCanvas has
 * to draw into a real canvas), so this can't make a single card render
 * faster by itself — what it does instead is:
 *  1. Prefetch the *next* student's photo while the *current* card is being
 *     rendered/encoded, overlapping network latency with CPU work.
 *  2. Yield back to the browser after every card (rAF/scheduler.yield) so the
 *     page can repaint and handle input, which is what actually removes the
 *     "lag"/freeze feeling during a big batch — total time is similar, but
 *     the tab stays responsive throughout.
 *
 * @param {Object} options
 * @param {Object[]} options.students
 * @param {"horizontal"|"vertical"} [options.orientation]
 * @param {(progress: {index: number, total: number, murid: Object}) => void} [options.onProgress]
 * @returns {Promise<{className: string, pdfBytes: Uint8Array}[]>}
 */
const generateStudentCardsBatch = async ({
    students = [],
    orientation = "horizontal",
    onProgress
} = {}) => {
    if (!Array.isArray(students) || students.length === 0) {
        throw new Error("A non-empty list of students is required.");
    }

    const normalizedOrientation = normalizeOrientation(orientation);
    const manager = new PdfManager();

    await templateLoader.preload([normalizedOrientation]);

    for (let index = 0; index < students.length; index += 1) {
        const murid = students[index];

        prefetchPhoto(students[index + 1]);

        // eslint-disable-next-line no-await-in-loop -- batch must stay sequential; rendering is single-threaded main-thread work
        const qrSvg = await getQrSvg(murid);
        // eslint-disable-next-line no-await-in-loop
        const canvas = await renderTemplateCanvas({
            orientation: normalizedOrientation,
            murid,
            qrSvg
        });

        // eslint-disable-next-line no-await-in-loop
        await manager.addCard({
            key: groupKeyFor(murid),
            canvas,
            orientation: normalizedOrientation
        });

        onProgress?.({ index, total: students.length, murid });

        // eslint-disable-next-line no-await-in-loop
        await yieldToBrowser();
    }

    return manager.exportAsync();
};

/**
 * Render a batch of student cards and immediately download one PDF per
 * class (grouped by rombel).
 *
 * @param {Object} options
 * @param {Object[]} options.students
 * @param {"horizontal"|"vertical"} [options.orientation]
 * @param {(progress: {index: number, total: number, murid: Object}) => void} [options.onProgress]
 * @returns {Promise<{className: string, pdfBytes: Uint8Array}[]>}
 */
const downloadStudentCardsBatch = async ({ students, orientation = "horizontal", onProgress } = {}) => {
    const results = await generateStudentCardsBatch({ students, orientation, onProgress });

    results.forEach(({ className, pdfBytes }) => {
        const sanitized = sanitizeFileName(className);

        downloadPdfBytes(sanitized ? `${sanitized}.pdf` : "kartu-pelajar.pdf", pdfBytes);
    });

    return results;
};

/**
 * Render horizontal student card.
 *
 * @param {Object} payload
 * @returns {Promise<HTMLCanvasElement>}
 */
window.renderStudentCardCanvas = (payload = {}) => renderTemplateCanvas({
    ...payload,
    orientation: "horizontal"
});

/**
 * Render vertical student card.
 *
 * @param {Object} payload
 * @returns {Promise<HTMLCanvasElement>}
 */
window.renderStudentCardVerticalCanvas = (payload = {}) => renderTemplateCanvas({
    ...payload,
    orientation: "vertical"
});

/**
 * Reset accumulated student card PDF documents.
 *
 * @returns {void}
 */
window.resetStudentCardPdf = () => {
    pdfManager.clear();
};

/**
 * Add a rendered student card canvas to its class PDF.
 *
 * @param {Object} options
 * @param {string} options.className
 * @param {HTMLCanvasElement} options.canvas
 * @param {"horizontal"|"vertical"} [options.orientation]
 * @returns {Promise<void>}
 */
window.addStudentCardToPdf = async ({ className, canvas, orientation = "horizontal" } = {}) => {
    if (!className || !canvas) return;

    await pdfManager.addCard({
        key: className,
        canvas,
        orientation
    });
};

/**
 * Export accumulated student card PDFs.
 *
 * @returns {{className: string, pdfBytes: Uint8Array}[]}
 */
window.exportStudentCardPdfs = () => pdfManager.export();

/**
 * Export accumulated student card PDFs without blocking the UI for one long task.
 *
 * @returns {Promise<{className: string, pdfBytes: Uint8Array}[]>}
 */
window.exportStudentCardPdfsAsync = () => pdfManager.exportAsync();

/**
 * Preload card templates, logo, and fonts.
 *
 * @param {("horizontal"|"vertical")[]} [orientations]
 * @returns {Promise<void>}
 */
window.preloadStudentCardAssets = async (orientations = ["horizontal", "vertical"]) => {
    await templateLoader.preload(orientations);
};

/**
 * Clear caches, rendered canvases, and PDF state.
 *
 * @returns {void}
 */
window.clearStudentCardMemory = () => {
    templateCache.clear();
    templateRequestCache.clear();
    canvasCache.clear();
    renderedCanvasRegistry.clear();
    whiteCanvas = null;
    whiteCanvasContext = null;
    templateLoader.clear();
    pdfManager.clear();
};

window.renderTemplateCanvas = renderTemplateCanvas;
window.getStudentCardSize = getCardSize;
window.getStudentCardPdfGrid = getPdfGrid;

/**
 * Render + download a single student card as a PDF (horizontal or vertical).
 *
 * @param {Object} options
 * @param {Object} options.murid
 * @param {"horizontal"|"vertical"} [options.orientation]
 * @param {string} [options.filename]
 * @returns {Promise<void>}
 */
window.downloadStudentCardPdf = downloadSingleCardPdf;

/**
 * Render a batch of student cards into one PDF per class without freezing the UI.
 * See generateStudentCardsBatch above for details.
 *
 * @returns {Promise<{className: string, pdfBytes: Uint8Array}[]>}
 */
window.generateStudentCardsBatch = generateStudentCardsBatch;

/**
 * Render a batch of student cards and download one PDF per class immediately.
 *
 * @returns {Promise<{className: string, pdfBytes: Uint8Array}[]>}
 */
window.downloadStudentCardsBatch = downloadStudentCardsBatch;

const scheduleStudentCardPreload = () => {
    const preload = () => {
        window.preloadStudentCardAssets?.(["horizontal", "vertical"]);
    };

    if (typeof window.requestIdleCallback === "function") {
        window.requestIdleCallback(preload, { timeout: 1500 });
        return;
    }

    window.setTimeout(preload, 0);
};

scheduleStudentCardPreload();

window.addEventListener("generateStudentCardSinglePdf", async (event) => {
    const detail = event?.detail ?? {};

    if (!detail.murid) return;

    try {
        await downloadSingleCardPdf({
            murid: detail.murid,
            orientation: detail.orientation,
            filename: detail.filename
        });
    } finally {
        window.clearStudentCardMemory?.();
    }
});

window.addEventListener("generateStudentCardPdf", async (event) => {
    const detail = event?.detail ?? {};

    if (!detail.murid) return;

    try {
        await downloadGeneratedStudentCardPdf({
            murid: detail.murid,
            orientation: detail.orientation,
            filename: detail.filename
        });
    } finally {
        window.clearStudentCardMemory?.();
    }
});

window.addEventListener("generateStudentCardsBatchPdf", async (event) => {
    const detail = event?.detail ?? {};

    if (!Array.isArray(detail.students) || detail.students.length === 0) return;

    try {
        await downloadStudentCardsBatch({
            students: detail.students,
            orientation: detail.orientation,
            onProgress: detail.onProgress
        });
    } finally {
        window.clearStudentCardMemory?.();
    }
});