const toastMagic = new ToastMagic();

function showToast(toast) {
    if (!toast || !window.toastMagic) return;

    const { status, title, message, confirm, linkText, linkUrl, duration } = toast;

    switch (status) {
        case 'success':
            toastMagic.success(title || 'Success!', message);
            break;
        case 'error':
            toastMagic.error(title || 'Error!', message);
            break;
        case 'warning':
            toastMagic.warning(title || 'Warning!', message, confirm ?? true);
            break;
        case 'info':
            toastMagic.info(
                title || 'Info!',
                message,
                confirm ?? false,
                linkText ?? '',
                linkUrl ?? '',
            );
            break;
    }
}

document.addEventListener('livewire:navigated', () => {
    if (window.toastFlash) {
        showToast(window.toastFlash);
        window.toastFlash = null;
    }
});