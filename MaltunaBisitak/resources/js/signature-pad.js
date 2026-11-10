/**
 * Minimal signature pad: draws on a <canvas> and keeps a PNG data URL in a hidden input.
 *
 * <div data-signature-pad data-signature-requires="checkbox-id">
 *     <canvas></canvas>
 *     <input type="hidden" name="signature">
 *     <button type="button" data-signature-clear>Garbitu</button>
 *     <p data-signature-locked hidden>…</p>
 *     <p data-signature-error hidden>…</p>
 * </div>
 *
 * With data-signature-requires, the pad stays locked until that checkbox is ticked
 * (for example, accepting the safety documents), and unticking it clears the signature.
 */
export function initSignaturePads(root = document) {
    root.querySelectorAll('[data-signature-pad]').forEach((container) => {
        const canvas = container.querySelector('canvas');
        const input = container.querySelector('input[type="hidden"]');
        const error = container.querySelector('[data-signature-error]');
        const context = canvas.getContext('2d');
        const gate = container.dataset.signatureRequires
            ? document.getElementById(container.dataset.signatureRequires)
            : null;
        const lockedMessage = container.querySelector('[data-signature-locked]');
        const clearButton = container.querySelector('[data-signature-clear]');
        let isDrawing = false;

        const isLocked = () => gate !== null && !gate.checked;

        const clear = () => {
            context.clearRect(0, 0, canvas.width, canvas.height);
            input.value = '';
        };

        const resize = () => {
            const ratio = window.devicePixelRatio || 1;
            const { width, height } = canvas.getBoundingClientRect();

            canvas.width = width * ratio;
            canvas.height = height * ratio;
            context.scale(ratio, ratio);
            context.lineWidth = 2.5;
            context.lineCap = 'round';
            context.lineJoin = 'round';
            context.strokeStyle = '#0f172a';
            input.value = '';
        };

        const pointFrom = (event) => {
            const rect = canvas.getBoundingClientRect();

            return { x: event.clientX - rect.left, y: event.clientY - rect.top };
        };

        canvas.addEventListener('pointerdown', (event) => {
            if (isLocked()) {
                return;
            }

            const { x, y } = pointFrom(event);

            isDrawing = true;
            canvas.setPointerCapture(event.pointerId);
            context.beginPath();
            context.moveTo(x, y);
            context.lineTo(x, y);
            context.stroke();
        });

        canvas.addEventListener('pointermove', (event) => {
            if (!isDrawing) {
                return;
            }

            const { x, y } = pointFrom(event);

            context.lineTo(x, y);
            context.stroke();
        });

        const finishStroke = () => {
            if (!isDrawing) {
                return;
            }

            isDrawing = false;
            input.value = canvas.toDataURL('image/png');
            error.hidden = true;
        };

        canvas.addEventListener('pointerup', finishStroke);
        canvas.addEventListener('pointercancel', finishStroke);

        clearButton.addEventListener('click', clear);

        const updateLock = () => {
            const locked = isLocked();

            container.style.opacity = locked ? '0.5' : '';
            canvas.style.cursor = locked ? 'not-allowed' : 'crosshair';
            canvas.setAttribute('aria-disabled', String(locked));
            clearButton.disabled = locked;
            lockedMessage && (lockedMessage.hidden = !locked);

            if (locked) {
                clear();
            }
        };

        gate?.addEventListener('change', updateLock);

        container.closest('form')?.addEventListener('submit', (event) => {
            if (isLocked()) {
                event.preventDefault();
                lockedMessage && (lockedMessage.hidden = false);
                gate.focus();
                gate.scrollIntoView({ behavior: 'smooth', block: 'center' });

                return;
            }

            if (!input.value) {
                event.preventDefault();
                error.hidden = false;
                canvas.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Resizing clears the canvas, so only follow the window while nothing has been signed yet.
        window.addEventListener('resize', () => {
            if (!input.value) {
                resize();
            }
        });

        resize();
        updateLock();
    });
}
