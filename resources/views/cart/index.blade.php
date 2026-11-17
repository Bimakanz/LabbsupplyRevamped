@extends('layouts.app')

@section('title', 'Cart - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Cart', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Shopping Cart</h1>

        @if ($items->isEmpty())
            <div class="mt-8 rounded-lg border border-dashed border-line bg-mist px-6 py-20 text-center">
                <p class="font-display text-lg font-medium text-ink">Keranjang Anda masih kosong.</p>
                <p class="mt-2 text-sm text-muted">Jelajahi produk laboratorium kami dan mulai belanja.</p>
                <a href="{{ route('shop') }}" class="btn-primary mt-6">Mulai Belanja</a>
            </div>
        @else
            <div class="mt-8 grid gap-10 lg:grid-cols-[1fr_360px]">
                <div>
                    <div class="hidden grid-cols-[1fr_140px_140px_40px] gap-4 border-b border-line pb-3 text-xs font-semibold uppercase tracking-wide text-muted sm:grid">
                        <span>Produk</span>
                        <span class="text-center">Harga</span>
                        <span class="text-center">Jumlah</span>
                        <span></span>
                    </div>

                    <ul class="divide-y divide-line" x-data>
                        @foreach ($items as $item)
                            <li class="grid gap-4 py-5 sm:grid-cols-[1fr_140px_140px_40px] sm:items-center" data-cart-row="{{ $item['product']->id }}">
                                <div class="flex gap-4">
                                    <a href="{{ $item['product']->url }}" class="size-20 shrink-0 overflow-hidden rounded border border-line bg-mist">
                                        <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" class="size-full object-contain p-1">
                                    </a>
                                    <div class="min-w-0">
                                        <a href="{{ $item['product']->url }}" class="line-clamp-2 font-medium text-ink hover:text-brand">{{ $item['product']->name }}</a>
                                        @if ($item['product']->category)
                                            <p class="mt-1 text-xs text-muted">{{ $item['product']->category->name }}</p>
                                        @endif
                                        <p class="mt-1 text-xs text-muted sm:hidden">{{ $item['product']->formatted_price }}</p>
                                    </div>
                                </div>

                                <div class="hidden text-center text-sm font-medium text-ink sm:block">{{ $item['product']->formatted_price }}</div>

                                <div class="flex items-center gap-2 sm:justify-center">
                                    <div class="flex h-10 items-center rounded-full border border-line">
                                        <button type="button" class="grid size-9 place-items-center text-muted hover:text-ink"
                                                data-cart-qty="{{ $item['product']->id }}" data-step="-1">−</button>
                                        <input type="number" value="{{ $item['quantity'] }}" min="1" max="99"
                                               class="w-10 border-0 bg-transparent text-center text-sm outline-none"
                                               data-cart-input="{{ $item['product']->id }}" aria-label="Jumlah {{ $item['product']->name }}">
                                        <button type="button" class="grid size-9 place-items-center text-muted hover:text-ink"
                                                data-cart-qty="{{ $item['product']->id }}" data-step="1">+</button>
                                    </div>
                                    <span class="text-sm font-semibold text-ink sm:hidden">{{ rupiah($item['subtotal']) }}</span>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end">
                                    <button type="button" class="text-xs text-muted underline hover:text-red-600" data-cart-remove="{{ $item['product']->id }}">Hapus</button>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                        <a href="{{ route('shop') }}" class="text-sm font-medium text-brand hover:text-brand-dark">&larr; Lanjut belanja</a>
                    </div>
                </div>

                <aside class="h-fit rounded-lg border border-line bg-mist p-6 lg:sticky lg:top-6">
                    <h2 class="font-display text-lg font-semibold text-ink">Ringkasan Pesanan</h2>

                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd class="font-medium text-ink">{{ rupiah($subtotal) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Pengiriman</dt><dd class="font-medium text-ink">Gratis</dd></div>
                        <div class="flex justify-between border-t border-line pt-3 text-base"><dt class="font-semibold text-ink">Total</dt><dd class="font-display font-semibold text-brand">{{ rupiah($total) }}</dd></div>
                    </dl>

                    <a href="{{ route('checkout.index') }}" class="btn-primary mt-6 !w-full">Checkout</a>

                    <div class="mt-4 space-y-2 text-[11px] leading-5 text-muted">
                        <p>Pembayaran aman melalui Mayar.</p>
                        <p>Butuh penawaran harga? <a href="{{ route('quote.index') }}" class="text-brand underline">Request a Quote</a></p>
                    </div>
                </aside>
            </div>
        @endif

        @if ($suggestions->isNotEmpty())
            <section class="mt-16 border-t border-line pt-10">
                <h2 class="section-title">You may also like</h2>
                <div class="mt-6 grid grid-cols-2 gap-x-5 gap-y-10 sm:gap-x-8 lg:grid-cols-4">
                    @foreach ($suggestions as $product)
                        <x-product-card :product="$product" compact />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    @include('partials.shop-scripts')
    <script>
        async function updateCart(productId, quantity) {
            try {
                const data = await window.postJson('{{ route('cart.update') }}', {
                    product_id: productId,
                    quantity: quantity,
                });

                document.querySelectorAll('[data-cart-count]').forEach((el) => (el.textContent = data.count));
                document.querySelectorAll('[data-cart-total]').forEach((el) => (el.textContent = data.total));
                window.refreshCartCounters();
                window.location.reload();
            } catch (error) {
                window.toast(error.message, 'error');
            }
        }

        document.addEventListener('click', function (event) {
            const stepper = event.target.closest('[data-cart-qty]');

            if (stepper) {
                const id = stepper.dataset.cartQty;
                const input = document.querySelector('[data-cart-input="' + id + '"]');
                const next = Math.max(1, Math.min(99, parseInt(input.value || '1', 10) + parseInt(stepper.dataset.step, 10)));
                input.value = next;
                updateCart(id, next);
            }

            const remove = event.target.closest('[data-cart-remove]');

            if (remove) {
                window.postJson('{{ route('cart.remove') }}', { product_id: remove.dataset.cartRemove })
                    .then(() => window.location.reload())
                    .catch((error) => window.toast(error.message, 'error'));
            }
        });

        document.addEventListener('change', function (event) {
            const input = event.target.closest('[data-cart-input]');

            if (input) {
                updateCart(input.dataset.cartInput, Math.max(1, parseInt(input.value || '1', 10)));
            }
        });
    </script>
@endpush
