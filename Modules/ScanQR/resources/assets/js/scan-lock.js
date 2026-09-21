export function createScanLock({ cooldownMs = 2000, rearmAfterMs = 1000 } = {}) {
    let previous = null;
    let absentSince = null;
    let accepted = null;
    let acceptedAt = Number.NEGATIVE_INFINITY;
    return {
        observe(text, now) {
            if (!text) {
                absentSince ??= now;
                if (now - absentSince >= rearmAfterMs) previous = null;
                return false;
            }
            absentSince = null;
            if (text === previous) return false;
            previous = text;
            if (text === accepted && now - acceptedAt < cooldownMs) return false;
            accepted = text;
            acceptedAt = now;
            return true;
        },
        reset() {
            previous = null;
            absentSince = null;
            accepted = null;
            acceptedAt = Number.NEGATIVE_INFINITY;
        },
    };
}
