<div id="cart-drawer" class="fixed inset-0 z-[70] hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Keranjang">
    <div class="absolute inset-0 bg-black/40 opacity-0 transition-opacity duration-300" data-cart-drawer-overlay onclick="closeCartDrawer()"></div>

    <div class="absolute inset-y-0 right-0 flex w-[min(420px,100%)] translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 ease-out" data-cart-drawer-panel>
        <div class="flex items-center justify-between border-b border-line px-5 py-4">
            <h2 class="font-display text-lg font-semibold text-ink">Your cart</h2>
            <button type="button" data-cart-drawer-close onclick="closeCartDrawer()"
                    class="grid size-9 place-items-center rounded-full border border-line text-ink transition hover:bg-mist"
                    aria-label="Tutup keranjang">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            @include('partials.mini-cart')
        </div>

        <div class="border-t border-line bg-mist px-5 py-3 text-[11px] leading-5 text-muted">
            Gratis pengiriman ke seluruh Indonesia. Pembayaran aman melalui Mayar.
        </div>
    </div>
</div>
