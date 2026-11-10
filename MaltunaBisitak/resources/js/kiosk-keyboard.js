/**
 * On-screen keyboard for the touch kiosk, like the KioskBoard keyboard of the original application.
 *
 * It opens when someone taps a text field inside [data-kiosk], types into it as a real keyboard would
 * (so validation, autofill and the idle timer keep working) and closes when tapping elsewhere.
 * A physical keyboard and the USB QR reader keep working as before.
 */
const LAYOUTS = {
    letters: [
        ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
        ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p'],
        ['a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'ñ'],
        ['{shift}', 'z', 'x', 'c', 'v', 'b', 'n', 'm', '{backspace}'],
        ['{accents}', '@', '.', '-', '{space}', '_', '{enter}', '{close}'],
    ],
    accents: [
        ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
        ['á', 'é', 'í', 'ó', 'ú', 'ü', 'à', 'è', 'ò', 'ç'],
        ['\'', ',', '(', ')', '/', '&', '+', '!', '?', ':'],
        ['{shift}', 'ä', 'ë', 'ï', 'ö', 'â', 'ê', 'ô', '{backspace}'],
        ['{letters}', '@', '.', '-', '{space}', '_', '{enter}', '{close}'],
    ],
};

const TEXT_TYPES = ['text', 'email', 'search', 'tel', 'url'];

export function initKioskKeyboard(root = document) {
    const kiosk = root.querySelector('[data-kiosk]');

    if (!kiosk) {
        return;
    }

    // The footer goes away while the keyboard is open, to leave its height to the field being typed in.
    const footer = kiosk.querySelector('footer');
    const labels = { close: 'Itxi', space: '', enter: '↵', accents: 'áé', letters: 'abc', ...JSON.parse(kiosk.dataset.keyboard || '{}') };
    const panel = document.createElement('div');
    let target = null;
    let layout = 'letters';
    let shift = false;

    panel.setAttribute('role', 'group');
    panel.setAttribute('aria-label', 'Teklatua');
    panel.hidden = true;
    panel.dataset.kioskKeyboard = '';
    panel.className = 'fixed inset-x-0 bottom-0 z-50 border-t border-slate-300 bg-slate-100 p-2 shadow-[0_-8px_24px_rgb(0_0_0/0.12)] select-none';
    document.body.append(panel);

    const isTextField = (element) => element instanceof HTMLInputElement
        && TEXT_TYPES.includes(element.type)
        && !element.readOnly
        && !element.disabled
        && kiosk.contains(element);

    const render = () => {
        panel.replaceChildren(...LAYOUTS[layout].map((row) => {
            const rowElement = document.createElement('div');

            rowElement.className = 'mx-auto mb-1.5 flex max-w-5xl justify-center gap-1.5 last:mb-0';
            row.forEach((key) => rowElement.append(keyButton(key)));

            return rowElement;
        }));
    };

    const keyButton = (key) => {
        const button = document.createElement('button');
        const special = key.startsWith('{') ? key.slice(1, -1) : null;

        button.type = 'button';
        button.dataset.key = key;
        button.className = `h-14 min-w-0 flex-1 rounded-lg font-medium text-slate-800 shadow-sm ring-1 ring-slate-300 active:bg-brand-100 ${special === null ? 'bg-white text-2xl' : 'bg-slate-200 text-lg'}`;

        if (special === null) {
            button.textContent = shift ? key.toLocaleUpperCase() : key;
        } else {
            button.textContent = { shift: '⇧', backspace: '⌫', space: labels.space, enter: labels.enter, close: labels.close, accents: labels.accents, letters: labels.letters }[special];
            button.setAttribute('aria-label', special === 'space' ? (labels.space || 'space') : special);

            if (special === 'space') {
                button.classList.replace('flex-1', 'flex-[4]');
            }

            if (special === 'enter' || special === 'close') {
                button.classList.add('flex-[1.5]');
            }

            if (special === 'shift' && shift) {
                button.classList.add('ring-2', 'ring-brand-500');
            }
        }

        // Keep the focus (and the caret) in the field while tapping keys.
        button.addEventListener('pointerdown', (event) => event.preventDefault());
        button.addEventListener('click', () => press(key));

        return button;
    };

    const insert = (text) => {
        // Email inputs do not expose the caret position, so they always type at the end.
        try {
            const start = target.selectionStart ?? target.value.length;
            const end = target.selectionEnd ?? start;

            target.setRangeText(text, start, end, 'end');
        } catch {
            target.value += text;
        }
    };

    const removeLast = () => {
        try {
            const start = target.selectionStart ?? target.value.length;
            const end = target.selectionEnd ?? start;

            if (start === end && start > 0) {
                target.setRangeText('', start - 1, end, 'end');
            } else if (start !== end) {
                target.setRangeText('', start, end, 'end');
            }
        } catch {
            target.value = target.value.slice(0, -1);
        }
    };

    const press = (key) => {
        if (!target) {
            return;
        }

        switch (key) {
            case '{shift}':
                shift = !shift;
                render();

                return;
            case '{accents}':
            case '{letters}':
                layout = key === '{accents}' ? 'accents' : 'letters';
                render();

                return;
            case '{close}':
                close();

                return;
            case '{enter}':
                next();

                return;
            case '{backspace}':
                removeLast();
                break;
            case '{space}':
                insert(' ');
                break;
            default:
                insert(shift ? key.toLocaleUpperCase() : key);

                if (shift) {
                    shift = false;
                    render();
                }
        }

        target.dispatchEvent(new Event('input', { bubbles: true }));
    };

    // Enter moves to the next field of the form, and sends the form from the last one.
    const next = () => {
        const form = target.form;
        const fields = form ? [...form.elements].filter((element) => element.matches('input, select, textarea') && !element.disabled && element.type !== 'hidden' && element.offsetParent !== null) : [];
        const following = fields[fields.indexOf(target) + 1];

        if (following && following.type !== 'submit') {
            following.focus();

            if (isTextField(following)) {
                open(following);
            } else {
                close();
            }

            return;
        }

        close();
        form?.requestSubmit();
    };

    const open = (input) => {
        target = input;
        // Do not let the tablet open its own keyboard on top of this one.
        input.setAttribute('inputmode', 'none');
        shift = input.type !== 'email' && input.value === '';
        layout = 'letters';
        render();
        panel.hidden = false;
        footer && (footer.hidden = true);
        // The kiosk page never scrolls, so make it shorter instead: the card holding the field
        // scrolls inside the space left above the keyboard and the field stays in sight.
        kiosk.style.height = `calc(100dvh - ${panel.offsetHeight}px)`;
        input.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    const close = () => {
        panel.hidden = true;
        target = null;
        kiosk.style.height = '';
        footer && (footer.hidden = false);
    };

    // Only a tap opens it: the scanner field gets the focus by itself and must not cover the screen.
    document.addEventListener('pointerdown', (event) => {
        if (panel.contains(event.target)) {
            return;
        }

        const field = event.target.closest?.('input');

        if (field && isTextField(field)) {
            open(field);
        } else if (!event.target.closest?.('label, select')) {
            close();
        }
    });

    document.addEventListener('focusin', (event) => {
        if (!panel.hidden && isTextField(event.target) && event.target !== target) {
            open(event.target);
        }
    });

    document.addEventListener('submit', close);
}
