/**
 * Entrance kiosk: keep the QR reader input focused, download the label after a registration
 * and go back to the start screen when nobody is using it.
 */
export function initKiosk(root = document) {
    const kiosk = root.querySelector('[data-kiosk]');

    if (!kiosk) {
        return;
    }

    // The USB reader works as a keyboard: it types the code and presses Enter wherever the focus is.
    const scannerInput = kiosk.querySelector('[data-kiosk-scanner]');

    if (scannerInput) {
        const refocus = () => {
            if (document.activeElement === document.body || document.activeElement === null) {
                scannerInput.focus();
            }
        };

        scannerInput.addEventListener('blur', () => setTimeout(refocus, 100));
        document.addEventListener('pointerup', () => setTimeout(refocus, 100));
        scannerInput.focus();
    }

    // The kiosk browser saves each label in C:\Etiketak, where the print script picks it up.
    const labelUrls = kiosk.dataset.labelUrls ? JSON.parse(kiosk.dataset.labelUrls) : [];

    if (labelUrls.length > 0) {
        downloadLabels(labelUrls);

        kiosk.querySelectorAll('[data-kiosk-print-labels]').forEach((button) => {
            button.addEventListener('click', () => downloadLabels(labelUrls));
        });
    }

    const idleSeconds = Number(kiosk.dataset.idleSeconds);

    if (idleSeconds > 0) {
        let timer;

        const restartTimer = () => {
            clearTimeout(timer);
            timer = setTimeout(() => window.location.assign(kiosk.dataset.homeUrl), idleSeconds * 1000);
        };

        ['pointerdown', 'keydown', 'input'].forEach((type) => document.addEventListener(type, restartTimer));
        restartTimer();
    }
}

/**
 * One label navigates to its download, as before. Several are saved one second apart: browsers drop
 * downloads that start at the same time, and the print script handles the files one by one.
 */
function downloadLabels(urls) {
    if (urls.length === 1) {
        window.location.assign(urls[0]);

        return;
    }

    urls.forEach((url, index) => {
        setTimeout(() => {
            const link = document.createElement('a');

            link.href = url;
            link.download = '';
            link.hidden = true;
            document.body.append(link);
            link.click();
            link.remove();
        }, index * 1000);
    });
}
