export function createScanLock() {
    let previous = null;
    let absentSince = null;
    return {
        observe(text, now) {
            if (!text) {
                absentSince ??= now;
                if (now - absentSince >= 1000) previous = null;
                return false;
            }
            absentSince = null;
            if (text === previous) return false;
            previous = text;
            return true;
        },
        reset() {
            previous = null;
            absentSince = null;
        },
    };
}
