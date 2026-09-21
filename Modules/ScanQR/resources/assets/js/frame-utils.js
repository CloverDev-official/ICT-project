export const CENTER_CROP_RATIO = 0.72;
export const MAX_DECODE_DIMENSION = 640;

export function createFramePlan(
    sourceWidth,
    sourceHeight,
    fullFrame = false,
    maxDimension = MAX_DECODE_DIMENSION,
) {
    const centerSize = Math.max(
        1,
        Math.round(Math.min(sourceWidth, sourceHeight) * CENTER_CROP_RATIO),
    );
    const width = fullFrame ? sourceWidth : centerSize;
    const height = fullFrame ? sourceHeight : centerSize;
    const scale = Math.min(1, maxDimension / Math.max(width, height));

    return {
        crop: {
            x: Math.round((sourceWidth - width) / 2),
            y: Math.round((sourceHeight - height) / 2),
            width,
            height,
        },
        outputWidth: Math.max(1, Math.round(width * scale)),
        outputHeight: Math.max(1, Math.round(height * scale)),
        sourceWidth,
        sourceHeight,
    };
}

export function mapPointToSource(point, frame) {
    return {
        x: frame.crop.x + (point.x / frame.outputWidth) * frame.crop.width,
        y: frame.crop.y + (point.y / frame.outputHeight) * frame.crop.height,
    };
}
