// One lifecycle controls the visible result, camera lock, timer and audio.
export function createScanResultController({
    audio,
    pause,
    resume,
    closeBackend,
    schedule = setTimeout,
    cancel = clearTimeout,
}) {
    let active = null;
    let closedId = null;
    let timer;
    let disposed = false;
    let closing = null;

    function stop() {
        cancel(timer);
        timer = null;
        audio.stopCurrentScanAudio();
    }

    function openScanResultModal(result, element) {
        if (
            disposed ||
            !result ||
            result.id === closedId ||
            !element?.isConnected
        )
            return;
        if (active?.result.id === result.id) {
            // A DOM replacement must not restart the same result's audio.
            if (active.element !== element) audio.stopCurrentScanAudio();
            active.element = element;
            return;
        }
        stop();
        if (active?.element !== element)
            active?.element.setAttribute('hidden', '');
        active = { result, element };
        pause();
        void audio.playScanAudio(result.status);
        if (result.autoClose)
            timer = schedule(() => {
                void closeScanResultModal();
            }, 2000);
    }

    function closeScanResultModal() {
        if (disposed || !active) return closing || Promise.resolve();
        stop();
        const previous = active;
        closedId = previous.result.id;
        active = null;
        previous.element.setAttribute('hidden', '');
        // Resume capture immediately; callers await this before the next scan request.
        closing = Promise.resolve(closing)
            .then(() => closeBackend(previous.result))
            .catch(() => {});
        resume();
        return closing;
    }

    function syncVisibility() {
        if (
            active &&
            (!active.element.isConnected ||
                active.element.hidden ||
                active.element.style.display === 'none')
        ) {
            void closeScanResultModal();
        }
    }

    return {
        openScanResultModal,
        closeScanResultModal,
        syncVisibility,
        isOpen: () => !!active,
        whenClosed: () => closing || Promise.resolve(),
        dispose() {
            disposed = true;
            stop();
            active = null;
            audio.dispose();
        },
    };
}
