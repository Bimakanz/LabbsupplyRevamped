<script>
    document.addEventListener('click', async function (event) {
        const addButton = event.target.closest('[data-add-to-cart]');
        const wishlistButton = event.target.closest('[data-wishlist-toggle]');
        const compareButton = event.target.closest('[data-compare-toggle]');
        const compareClear = event.target.closest('[data-compare-clear]');
        const quickView = event.target.closest('[data-quick-view]');

        if (addButton) {
            event.preventDefault();
            addButton.disabled = true;

            try {
                const data = await window.postJson('{{ route('cart.add') }}', {
                    product_id: addButton.dataset.addToCart,
                    quantity: 1,
                });

                window.toast(data.message);
                document.querySelectorAll('[data-cart-count]').forEach((el) => (el.textContent = data.count));
                document.querySelectorAll('[data-cart-total]').forEach((el) => (el.textContent = data.total));
                window.refreshCartCounters();
            } catch (error) {
                window.toast(error.message, 'error');
            } finally {
                addButton.disabled = false;
            }

            return;
        }

        if (wishlistButton) {
            event.preventDefault();
            const id = wishlistButton.dataset.wishlistToggle;

            try {
                const data = await window.postJson('{{ route('wishlist.toggle') }}', { product_id: id });

                wishlistButton.classList.toggle('bg-brand', data.added);
                wishlistButton.classList.toggle('text-white', data.added);
                wishlistButton.querySelector('svg')?.setAttribute('fill', data.added ? 'currentColor' : 'none');
                document.querySelectorAll('[data-wishlist-count]').forEach((el) => (el.textContent = data.count));
                window.toast(data.message);
            } catch (error) {
                window.toast(error.message, 'error');
            }

            return;
        }

        if (compareButton) {
            event.preventDefault();
            const id = compareButton.dataset.compareToggle;

            try {
                const data = await window.postJson('{{ route('compare.toggle') }}', { product_id: id });

                compareButton.classList.toggle('bg-brand', data.added);
                compareButton.classList.toggle('text-white', data.added);
                document.querySelectorAll('[data-compare-count]').forEach((el) => (el.textContent = data.count));
                window.toast(data.message);
                refreshCompareBar();
            } catch (error) {
                window.toast(error.message, 'error');
            }

            return;
        }

        if (compareClear) {
            event.preventDefault();

            try {
                await window.postJson('{{ route('compare.clear') }}', {});
                document.querySelectorAll('[data-compare-toggle]').forEach((el) => {
                    el.classList.remove('bg-brand', 'text-white');
                });
                document.querySelectorAll('[data-compare-count]').forEach((el) => (el.textContent = '0'));
                refreshCompareBar();
                window.toast('Perbandingan dikosongkan.');
            } catch (error) {
                window.toast(error.message, 'error');
            }

            return;
        }

        if (event.target.closest('[data-quick-view-close]')) {
            closeQuickView();
            return;
        }

        if (quickView) {
            event.preventDefault();
            openQuickView(quickView.getAttribute('href'));
            return;
        }

        // Jika mengklik link kategori di dalam card, biarkan navigasi kategori berjalan
        if (event.target.closest('a[href*="product-category"]')) {
            return;
        }

        const productCard = event.target.closest('[data-product-card]');
        if (productCard) {
            const url = productCard.dataset.quickViewUrl || productCard.querySelector('[data-quick-view]')?.getAttribute('href');
            if (url) {
                event.preventDefault();
                openQuickView(url);
            }
        }
    });

    async function refreshCompareBar() {
        const dock = document.getElementById('compare-dock');

        if (!dock) {
            return;
        }

        try {
            const response = await fetch('{{ route('compare.bar') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();

            if (html.includes('data-compare-clear')) {
                dock.innerHTML = html;
                dock.classList.remove('hidden');
            } else {
                dock.innerHTML = '';
                dock.classList.add('hidden');
            }
        } catch (error) {
            // Ignore refresh failures.
        }
    }

    async function openQuickView(url) {
        const modal = document.getElementById('quick-view-modal');

        if (!modal) {
            window.location.href = url;
            return;
        }

        modal.classList.remove('hidden');
        modal.querySelector('[data-quick-view-body]').innerHTML =
            '<div class="grid min-h-[280px] place-items-center text-sm text-muted">Memuat produk...</div>';

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            modal.querySelector('[data-quick-view-body]').innerHTML = html;
        } catch (error) {
            modal.querySelector('[data-quick-view-body]').innerHTML =
                '<p class="p-8 text-center text-sm text-red-600">Gagal memuat produk.</p>';
        }
    }

    function closeQuickView() {
        const modal = document.getElementById('quick-view-modal');
        modal?.classList.add('hidden');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeQuickView();
        }
    });

    document.addEventListener('DOMContentLoaded', refreshCompareBar);
</script>
