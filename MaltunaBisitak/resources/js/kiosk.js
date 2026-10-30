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

    // The kiosk browser saves the label in C:\Etiquetas, where the print script picks it up.
    if (kiosk.dataset.labelUrl) {
        window.location.assign(kiosk.dataset.labelUrl);
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
