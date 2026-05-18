/**
 * Confirms destructive form submits via a native browser dialog.
 *
 * Markup contract:
 *
 *   <form method="POST" action="..." data-confirm="Are you sure?">
 *     ...
 *   </form>
 *
 * The submit is cancelled when the user dismisses the dialog. The dialog
 * text comes from the `data-confirm` attribute so copy lives in the view.
 */

const SELECTOR = 'form[data-confirm]';

function attach(form) {
    form.addEventListener('submit', (event) => {
        const message = form.dataset.confirm;
        if (message && ! window.confirm(message)) {
            event.preventDefault();
        }
    });
}

function init() {
    document.querySelectorAll(SELECTOR).forEach(attach);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
} else {
    init();
}
