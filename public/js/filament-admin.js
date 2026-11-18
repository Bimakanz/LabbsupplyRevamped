(function () {
    if (window.labsAdminUiLoaded) {
        return;
    }

    window.labsAdminUiLoaded = true;

    const CARET = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>';

    function formatMoney(input) {
        const digits = input.value.replace(/\D+/g, '');

        input.value = digits === '' ? '' : digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function bindMoneyFields(root) {
        root.querySelectorAll('input[data-labs-money]').forEach((input) => {
            if (input.dataset.labsMoneyBound === '1') {
                return;
            }

            input.dataset.labsMoneyBound = '1';

            input.addEventListener('input', () => formatMoney(input));
            input.addEventListener('blur', () => formatMoney(input));

            formatMoney(input);
        });
    }

    function upgradeSelect(select) {
        if (select.dataset.labsUpgraded === '1' || ! select.parentNode) {
            return;
        }

        select.dataset.labsUpgraded = '1';
        select.classList.add('labs-native-select');
        select.setAttribute('tabindex', '-1');
        select.setAttribute('aria-hidden', 'true');

        const wrap = document.createElement('div');
        wrap.className = 'labs-select';

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'labs-select-btn';
        button.setAttribute('aria-haspopup', 'listbox');
        button.setAttribute('aria-expanded', 'false');
        button.innerHTML = '<span class="labs-select-value"></span><span class="labs-select-caret">'+CARET+'</span>';

        const menu = document.createElement('div');
        menu.className = 'labs-select-menu';
        menu.setAttribute('role', 'listbox');
        menu.hidden = true;

        select.parentNode.insertBefore(wrap, select);
        wrap.appendChild(button);
        wrap.appendChild(menu);
        wrap.appendChild(select);

        const valueLabel = button.querySelector('.labs-select-value');

        const sync = () => {
            const selected = select.options[select.selectedIndex];
            valueLabel.textContent = selected ? selected.textContent.trim() : '';

            menu.querySelectorAll('[data-value]').forEach((item) => {
                item.classList.toggle('is-selected', item.dataset.value === select.value);
            });
        };

        let closeOnOutsideClick = null;

        const close = () => {
            menu.hidden = true;
            button.setAttribute('aria-expanded', 'false');

            if (closeOnOutsideClick) {
                document.removeEventListener('click', closeOnOutsideClick);
                closeOnOutsideClick = null;
            }
        };

        const open = () => {
            menu.hidden = false;
            button.setAttribute('aria-expanded', 'true');

            closeOnOutsideClick = (event) => {
                if (! wrap.contains(event.target)) {
                    close();
                }
            };

            window.setTimeout(() => document.addEventListener('click', closeOnOutsideClick), 0);
        };

        const build = () => {
            menu.innerHTML = '';

            Array.from(select.options).forEach((option) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'labs-select-option';
                item.dataset.value = option.value;
                item.setAttribute('role', 'option');
                item.textContent = option.textContent.trim();

                item.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();

                    if (select.value !== option.value) {
                        select.value = option.value;
                        select.dispatchEvent(new Event('input', { bubbles: true }));
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    sync();
                    close();
                });

                menu.appendChild(item);
            });

            sync();
        };

        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            menu.hidden ? open() : close();
        });

        button.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                close();
            }
        });

        select.addEventListener('change', sync);

        build();
    }

    let scanTimer = null;

    function scan() {
        bindMoneyFields(document);
        document.querySelectorAll('.fi-pagination select').forEach(upgradeSelect);
    }

    function scheduleScan() {
        window.clearTimeout(scanTimer);
        scanTimer = window.setTimeout(scan, 60);
    }

    document.addEventListener('DOMContentLoaded', scheduleScan);
    document.addEventListener('livewire:navigated', scheduleScan);
    document.addEventListener('livewire:init', () => {
        window.Livewire.hook('morph.updated', scheduleScan);
    });

    new MutationObserver(scheduleScan).observe(document.documentElement, {
        childList: true,
        subtree: true,
    });

    scheduleScan();
})();
