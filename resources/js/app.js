import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.toast = function (message, type = 'success') {
    const container = document.getElementById('toast-stack');

    if (!container) {
        return;
    }

    const el = document.createElement('div');
    el.className =
        'pointer-events-auto flex items-start gap-3 rounded-lg border px-4 py-3 text-sm shadow-lg transition duration-300 ' +
        (type === 'error'
            ? 'border-red-200 bg-red-50 text-red-700'
            : 'border-emerald-200 bg-emerald-50 text-emerald-700');
    el.innerHTML =
        '<span class="mt-0.5">' +
        (type === 'error' ? '&#9888;' : '&#10003;') +
        '</span><span>' +
        message +
        '</span>';

    container.appendChild(el);

    setTimeout(() => {
        el.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => el.remove(), 300);
    }, 3200);
};

window.postJson = async function (url, payload) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        },
        body: JSON.stringify(payload),
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
    }

    return data;
};

window.refreshCartCounters = function () {
    fetch('/cart/mini', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then((response) => response.text())
        .then((html) => {
            document.querySelectorAll('[data-mini-cart]').forEach((el) => {
                el.innerHTML = html;
            });
        })
        .catch(() => {});
};

window.openCartDrawer = function () {
    const drawer = document.getElementById('cart-drawer');

    if (! drawer) {
        return;
    }

    drawer.classList.remove('hidden');
    drawer.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');

    window.requestAnimationFrame(() => {
        drawer.querySelector('[data-cart-drawer-overlay]')?.classList.remove('opacity-0');
        drawer.querySelector('[data-cart-drawer-panel]')?.classList.remove('translate-x-full');
        drawer.querySelector('[data-cart-drawer-close]')?.focus();
    });
};

window.closeCartDrawer = function () {
    const drawer = document.getElementById('cart-drawer');

    if (! drawer || drawer.classList.contains('hidden')) {
        return;
    }

    drawer.querySelector('[data-cart-drawer-overlay]')?.classList.add('opacity-0');
    drawer.querySelector('[data-cart-drawer-panel]')?.classList.add('translate-x-full');
    document.body.classList.remove('overflow-hidden');

    window.setTimeout(() => {
        drawer.classList.add('hidden');
        drawer.setAttribute('aria-hidden', 'true');
    }, 300);
};

window.openCategoryModal = function () {
    const modal = document.getElementById('category-modal');

    if (! modal) {
        return;
    }

    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');

    window.requestAnimationFrame(() => {
        modal.querySelector('[data-category-overlay]')?.classList.remove('opacity-0');
        modal.querySelector('[data-category-panel]')?.classList.remove('opacity-0', 'scale-95');
    });
};

window.closeCategoryModal = function () {
    const modal = document.getElementById('category-modal');

    if (! modal || modal.classList.contains('hidden')) {
        return;
    }

    modal.querySelector('[data-category-overlay]')?.classList.add('opacity-0');
    modal.querySelector('[data-category-panel]')?.classList.add('opacity-0', 'scale-95');
    document.body.classList.remove('overflow-hidden');

    window.setTimeout(() => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    }, 200);
};

window.selectCategory = function (slug, label) {
    document.querySelectorAll('[data-category-input]').forEach((input) => {
        input.value = slug;
    });

    document.querySelectorAll('[data-category-label]').forEach((element) => {
        element.textContent = label;
    });

    document.querySelectorAll('[data-category-option]').forEach((option) => {
        const isActive = option.dataset.categoryOption === slug;
        option.classList.toggle('text-brand', isActive);
        option.classList.toggle('font-medium', isActive);
    });

    window.closeCategoryModal();
};

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        window.closeCartDrawer();
        window.closeCategoryModal();
    }
});

document.addEventListener('alpine:init', () => {
    Alpine.data('customSelect', (config = {}) => ({
        open: false,
        value: config.value ?? '',
        options: config.options ?? [],
        placeholder: config.placeholder ?? 'Pilih...',
        submit: config.submit ?? false,

        get selected() {
            return this.options.find((option) => String(option.value) === String(this.value)) ?? null;
        },

        get label() {
            return this.selected ? this.selected.label : this.placeholder;
        },

        toggle() {
            this.open = ! this.open;
        },

        close() {
            this.open = false;
        },

        choose(option) {
            this.value = option.value;
            this.open = false;

            if (this.submit) {
                this.$nextTick(() => this.$el.closest('form')?.submit());
            }
        },

        init() {
            this.$watch('open', (open) => {
                if (open) {
                    this.$nextTick(() => {
                        this.$el.querySelector('[data-option-active]')?.scrollIntoView({ block: 'nearest' });
                    });
                }
            });

            document.addEventListener('click', (event) => {
                if (this.open && ! this.$el.contains(event.target)) {
                    this.close();
                }
            });
        },
    }));
});

Alpine.start();
