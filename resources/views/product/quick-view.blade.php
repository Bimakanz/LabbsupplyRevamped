@php
    $quickCart = app(\App\Services\CartService::class);
@endphp

<div class="grid gap-6 p-5 sm:grid-cols-2 sm:p-7" x-data="{ qty: 1 }">
    <div class="overflow-hidden rounded-lg border border-line bg-[#f6f7f8]">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aspect-square w-full object-contain p-5">
    </div>

    <div>
        @if ($product->category)
            <p class="text-[11px] font-medium uppercase tracking-[0.1em] text-brand">{{ $product->category->name }}</p>
        @endif
        <h2 class="mt-1.5 font-display text-xl font-semibold leading-7 text-ink">{{ $product->name }}</h2>

        <p class="mt-3 font-display text-2xl font-semibold text-brand">{{ $product->formatted_price }}</p>

        <p class="mt-3 text-sm leading-6 text-muted line-clamp-5">
            {{ $product->short_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 320) }}
        </p>

        <div class="mt-5 flex items-center gap-3">
            <div class="flex h-10 items-center rounded-full border border-line">
                <button type="button" class="grid size-9 place-items-center text-muted" @click="qty = Math.max(1, qty - 1)">−</button>
                <input type="number" x-model.number="qty" min="1" max="99" class="w-10 border-0 bg-transparent text-center text-sm outline-none" aria-label="Jumlah">
                <button type="button" class="grid size-9 place-items-center text-muted" @click="qty = Math.min(99, qty + 1)">+</button>
            </div>
            <button type="button" class="btn-primary flex-1" @click="addToCart({{ $product->id }}, qty)">Add to cart</button>
        </div>

        <div class="mt-4 flex flex-wrap gap-3 text-xs">
            <a href="{{ $product->url }}" class="text-brand underline">Lihat detail produk</a>
            <a href="{{ route('quote.index') }}" class="text-brand underline">Request penawaran</a>
        </div>

        <p class="mt-4 text-xs text-muted">
            {{ $product->is_in_stock ? 'Stok tersedia: '.$product->stock : 'Stok habis' }} &middot; SKU {{ $product->sku }}
        </p>
    </div>
</div>

<script>
    window.addToCart = async function (id, qty) {
        try {
            const data = await window.postJson('{{ route('cart.add') }}', { product_id: id, quantity: qty });
            if (window.showCenterNotice) {
                window.showCenterNotice({ title: data.product_name || @js($product->name), target: 'cart', action: 'add' });
            } else {
                window.toast(data.message);
            }
            document.querySelectorAll('[data-cart-count]').forEach((el) => (el.textContent = data.count));
            document.querySelectorAll('[data-cart-total]').forEach((el) => (el.textContent = data.total));
            window.refreshCartCounters();
            document.getElementById('quick-view-modal')?.classList.add('hidden');
        } catch (error) {
            window.toast(error.message, 'error');
        }
    };
</script>
