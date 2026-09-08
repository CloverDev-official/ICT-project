import { readFileSync } from 'node:fs';

const files = [
    'scan-success',
    'scan-late',
    'scan-failed',
    'already-recorded',
    'attendance-not-open',
];
for (const name of files) {
    const bytes = readFileSync(
        new URL(`../resources/audio/${name}.mp3`, import.meta.url),
    );
    // This bundled asset is MP3 only. Reject oversized, empty or non-MPEG sources.
    if (!bytes.length || bytes.length > 5 * 1024 * 1024) {
        throw new Error('Scan audio must be between 1 byte and 5 MB');
    }
    const offset =
        bytes.subarray(0, 3).toString() === 'ID3'
            ? 10 +
              (((bytes[6] & 127) << 21) |
                  ((bytes[7] & 127) << 14) |
                  ((bytes[8] & 127) << 7) |
                  (bytes[9] & 127))
            : 0;
    if (bytes[offset] !== 255 || (bytes[offset + 1] & 0xe6) !== 0xe2) {
        throw new Error('Scan audio must contain MPEG Layer III audio');
    }
    console.log(`Scan audio validated: audio/mpeg, ${bytes.length} bytes`);
}
