@php
    $cart = app(\App\Services\CartService::class);
    $items = $cart->content();
@endphp

<div data-mini-cart class="text-sm">
    @if ($items->isEmpty())
        <p class="px-4 py-6 text-center text-muted">No products in the cart.</p>
    @else
        <ul class="max-h-80 divide-y divide-line overflow-y-auto">
            @foreach ($items as $item)
                <li class="flex gap-3 px-4 py-3">
                    <a href="{{ $item['product']->url }}" class="size-16 shrink-0 overflow-hidden rounded border border-line bg-mist">
                        <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" class="size-full object-contain p-1">
                    </a>
                    <div class="min-w-0 flex-1">
                        <a href="{{ $item['product']->url }}" class="line-clamp-2 text-[13px] font-medium leading-5 text-ink hover:text-brand">
                            {{ $item['product']->name }}
                        </a>
                        <p class="mt-1 text-xs text-muted">{{ $item['quantity'] }} &times; {{ $item['product']->formatted_price }}</p>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="border-t border-line px-4 py-3">
            <div class="flex items-center justify-between font-semibold text-ink">
                <span>Subtotal</span>
                <span class="text-brand">{{ rupiah($cart->subtotal()) }}</span>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <a href="{{ route('cart.index') }}" class="btn-outline !px-3 !py-2 !text-xs">View Cart</a>
                <a href="{{ route('checkout.index') }}" class="btn-primary !px-3 !py-2 !text-xs">Checkout</a>
            </div>
        </div>
    @endif
</div>
