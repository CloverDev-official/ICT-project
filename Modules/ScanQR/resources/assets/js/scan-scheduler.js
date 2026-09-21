const MIN_INTERVAL = 125;
const MAX_INTERVAL = 400;
const LOW_POWER_MIN_INTERVAL = 167;
const LOW_POWER_MAX_INTERVAL = 500;

export function isLowPowerDevice(device = globalThis.navigator) {
    const cores = Number(device?.hardwareConcurrency) || 0;
    const memory = Number(device?.deviceMemory) || 0;
    return (cores > 0 && cores <= 4) || (memory > 0 && memory <= 4);
}

export function intervalForDecode(duration) {
    if (duration < 80) return 125;
    if (duration < 150) return 167;
    if (duration < 250) return 250;
    return 400;
}

export function createAdaptiveScanScheduler(
    now = () => performance.now(),
    { lowPower = false } = {},
) {
    let averageDecodeMs = null;
    let interval = lowPower ? 200 : 167;
    let decodedFrames = 0;
    let skippedFrames = 0;
    let startedAt = now();

    return {
        recordDecode(duration) {
            const safeDuration = Math.max(0, Number(duration) || 0);
            averageDecodeMs =
                averageDecodeMs === null
                    ? safeDuration
                    : averageDecodeMs * 0.7 + safeDuration * 0.3;
            let target = intervalForDecode(averageDecodeMs);
            if (lowPower) {
                // Keep worker duty cycle near/below 50% on slow quad-core CPUs.
                target = Math.max(
                    LOW_POWER_MIN_INTERVAL,
                    target,
                    Math.min(
                        LOW_POWER_MAX_INTERVAL,
                        Math.round(averageDecodeMs * 2),
                    ),
                );
            }
            interval = Math.min(
                lowPower ? LOW_POWER_MAX_INTERVAL : MAX_INTERVAL,
                Math.max(
                    lowPower ? LOW_POWER_MIN_INTERVAL : MIN_INTERVAL,
                    Math.round(interval * 0.6 + target * 0.4),
                ),
            );
            decodedFrames++;
            return interval;
        },
        recordSkipped() {
            skippedFrames++;
        },
        getInterval: () => interval,
        // Rest after completion, even if one frame exceeds the interval ceiling.
        getRest: (duration, mainThread = false) =>
            Math.max(
                0,
                interval - duration,
                lowPower || mainThread ? duration : 0,
                mainThread ? 250 : 0,
            ),
        snapshot(reset = false) {
            const timestamp = now();
            const elapsed = Math.max(1, timestamp - startedAt);
            const result = {
                averageDecodeMs: averageDecodeMs ?? 0,
                effectiveFps: (decodedFrames * 1000) / elapsed,
                decodedFrames,
                skippedFrames,
                interval,
                lowPower,
            };
            if (reset) {
                decodedFrames = 0;
                skippedFrames = 0;
                startedAt = timestamp;
            }
            return result;
        },
    };
}
