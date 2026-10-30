/**
 * Minimal signature pad: draws on a <canvas> and keeps a PNG data URL in a hidden input.
 *
 * <div data-signature-pad>
 *     <canvas></canvas>
 *     <input type="hidden" name="signature">
 *     <button type="button" data-signature-clear>Garbitu</button>
 *     <p data-signature-error hidden>…</p>
 * </div>
 */
export function initSignaturePads(root = document) {
    root.querySelectorAll('[data-signature-pad]').forEach((container) => {
        const canvas = container.querySelector('canvas');
        const input = container.querySelector('input[type="hidden"]');
        const error = container.querySelector('[data-signature-error]');
        const context = canvas.getContext('2d');
        let isDrawing = false;

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

        container.querySelector('[data-signature-clear]').addEventListener('click', () => {
            context.clearRect(0, 0, canvas.width, canvas.height);
            input.value = '';
        });

        container.closest('form')?.addEventListener('submit', (event) => {
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
    });
}
