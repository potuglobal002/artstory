import './bootstrap';

if (document.querySelector('[data-virtual-gallery]')) {
    import('./virtual-gallery');
}

const menuToggle = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');
const menuClose = document.querySelector('[data-menu-close]');

if (menuToggle && mobileMenu) {
    const closeMenu = () => {
        mobileMenu.classList.add('hidden');
        document.body.classList.remove('site-menu-open');
        menuToggle.setAttribute('aria-expanded', 'false');
    };

    menuToggle.addEventListener('click', () => {
        const isOpen = !mobileMenu.classList.contains('hidden');

        mobileMenu.classList.toggle('hidden', isOpen);
        document.body.classList.toggle('site-menu-open', !isOpen);
        menuToggle.setAttribute('aria-expanded', String(!isOpen));
    });

    menuClose?.addEventListener('click', closeMenu);

    mobileMenu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });
}

document.querySelectorAll('[data-artwork-inquiry]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        const feedback = form.querySelector('[data-inquiry-feedback]');
        button.disabled = true;
        button.textContent = 'Saving inquiry...';
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Unable to save inquiry.');
            feedback.textContent = result.whatsapp_url ? 'Inquiry saved. WhatsApp is opening now.' : 'Inquiry saved successfully.';
            feedback.className = 'mb-3 text-sm text-green-700';
            if (result.whatsapp_url) window.open(result.whatsapp_url, '_blank', 'noopener');
            form.reset();
        } catch (error) {
            feedback.textContent = error.message || 'Unable to save inquiry.';
            feedback.className = 'mb-3 text-sm text-red-700';
        } finally {
            button.disabled = false;
            button.textContent = 'Send Inquiry on WhatsApp';
        }
    });
});

document.querySelectorAll('input[type="checkbox"][name="artist"], input[type="checkbox"][name="style"], input[type="checkbox"][name="status"]').forEach((checkbox) => {
    checkbox.addEventListener('change', () => {
        if (!checkbox.checked) {
            return;
        }

        document.querySelectorAll(`input[type="checkbox"][name="${checkbox.name}"]`).forEach((peer) => {
            if (peer !== checkbox) {
                peer.checked = false;
            }
        });
    });
});

document.querySelectorAll('[data-price-range]').forEach((range) => {
    const output = document.querySelector('[data-price-output]');
    const update = () => {
        if (output) {
            output.textContent = Number(range.value || 0).toLocaleString();
        }
    };

    range.addEventListener('input', update);
    update();
});

document.querySelectorAll('[data-artwork-catalog]').forEach((catalog) => {
    const button = catalog.querySelector('[data-artwork-load-more]');

    if (!button) {
        return;
    }

    const grid = catalog.querySelector('[data-artwork-grid]');
    const panel = catalog.querySelector('[data-artwork-load-more-panel]');
    const status = catalog.querySelector('[data-artwork-load-more-status]');
    const progress = catalog.querySelector('[data-artwork-load-more-progress]');
    const feedback = catalog.querySelector('[data-artwork-load-more-feedback]');

    button.addEventListener('click', async () => {
        const nextPageUrl = catalog.dataset.nextPageUrl;

        if (!nextPageUrl) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Loading...';
        feedback.textContent = '';

        try {
            const response = await fetch(nextPageUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Unable to load more artworks.');
            }

            grid.insertAdjacentHTML('beforeend', result.html);
            catalog.dataset.nextPageUrl = result.next_page_url || '';
            catalog.dataset.shown = result.shown;
            catalog.dataset.total = result.total;
            status.textContent = `Showing ${result.shown} of ${result.total} artworks`;
            progress.style.width = `${result.total ? (result.shown / result.total) * 100 : 0}%`;

            if (!result.next_page_url) {
                button.remove();
            } else {
                button.disabled = false;
                button.textContent = 'Load more';
            }
        } catch (error) {
            feedback.textContent = error.message || 'Unable to load more artworks.';
            button.disabled = false;
            button.textContent = 'Load more';
        }
    });
});
