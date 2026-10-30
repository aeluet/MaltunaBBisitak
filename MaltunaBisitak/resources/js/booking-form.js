/**
 * Public booking form: add and remove visitor blocks, and ask for the group size only for groups.
 * Without JavaScript the form still works with a single visitor and the group size always visible.
 */
export function initBookingForms(root = document) {
    root.querySelectorAll('[data-booking-form]').forEach((form) => {
        const list = form.querySelector('[data-visitors]');
        const template = form.querySelector('[data-visitor-template]');
        const addButton = form.querySelector('[data-add-visitor]');
        const groupSize = form.querySelector('[data-group-size]');
        const maxVisitors = Number(list.dataset.max);

        const visitorBlocks = () => list.querySelectorAll('[data-visitor]');
        let nextIndex = Math.max(-1, ...[...visitorBlocks()].map((block) => Number(block.dataset.index))) + 1;

        const updateAddButton = () => {
            addButton.hidden = visitorBlocks().length >= maxVisitors;
        };

        addButton.addEventListener('click', () => {
            list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(nextIndex++)));
            list.lastElementChild.querySelector('input')?.focus();
            updateAddButton();
        });

        list.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove-visitor]');

            if (removeButton) {
                removeButton.closest('[data-visitor]').remove();
                updateAddButton();
            }
        });

        const updateGroupSize = () => {
            const isGroup = form.querySelector('input[name="is_group"]:checked')?.value === '1';

            groupSize.hidden = !isGroup;
            groupSize.querySelector('input').required = isGroup;
        };

        form.querySelectorAll('input[name="is_group"]').forEach((radio) => radio.addEventListener('change', updateGroupSize));

        updateGroupSize();
        updateAddButton();
    });
}
