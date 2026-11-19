@props(['product', 'compact' => false, 'hideActions' => false])

@php
    $inWishlist = in_array($product->id, app(\App\Services\WishlistService::class)->ids(), true);
    $inCompare = in_array($product->id, app(\App\Services\CompareService::class)->ids(), true);
@endphp

<article class="group relative flex h-full flex-col cursor-pointer" data-product-card="{{ $product->id }}" data-product-url="{{ $product->url }}">
    <div class="relative overflow-hidden rounded-md bg-[#f4f5f6]">
        <a href="{{ $product->url }}" class="block">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                 class="aspect-square w-full object-contain p-4 transition duration-500 group-hover:scale-[1.03]"
                 loading="lazy" width="600" height="600">
        </a>

        @unless ($hideActions)
            <div class="absolute right-3 top-3 flex flex-col gap-2.5 opacity-100 transition duration-300 lg:opacity-0 lg:group-hover:opacity-100">
                <button type="button"
                        class="grid size-10 place-items-center rounded-full shadow-md transition-all duration-200 hover:scale-110 {{ $inWishlist ? 'bg-brand text-white' : 'bg-white text-ink hover:bg-brand hover:text-white' }}"
                        data-wishlist-toggle="{{ $product->id }}" data-product-name="{{ $product->name }}" title="Wishlist" aria-label="Wishlist">
                    <svg class="size-5 pointer-events-none" viewBox="0 0 24 24" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 20.7s-8-4.7-8-10.2A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 8 3.5c0 5.5-8 10.2-8 10.2Z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <button type="button"
                        class="hidden size-10 place-items-center rounded-full shadow-md transition-all duration-200 hover:scale-110 sm:grid {{ $inCompare ? 'bg-brand text-white' : 'bg-white text-ink hover:bg-brand hover:text-white' }}"
                        data-compare-toggle="{{ $product->id }}" data-product-name="{{ $product->name }}" title="Compare" aria-label="Compare">
                    <svg class="size-5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h10M4 7l3-3M4 7l3 3M20 17H10m10 0-3-3m3 3-3 3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        @endunless

        <button type="button" data-add-to-cart="{{ $product->id }}" data-product-name="{{ $product->name }}"
                class="absolute inset-x-0 bottom-0 translate-y-full bg-ink/95 py-2.5 text-center text-[13px] font-semibold text-white transition duration-300 hover:bg-brand group-hover:translate-y-0">
            Add to cart
        </button>
    </div>

    <div class="flex flex-1 flex-col px-1 pt-4">
        @if ($product->category)
            <a href="{{ route('category.show', $product->category->slug) }}" class="text-[11px] font-medium uppercase tracking-[0.08em] text-muted">
                {{ $product->category->name }}
            </a>
        @endif

        <h3 class="mt-1.5 font-display text-[15px] font-medium leading-6 text-ink {{ $compact ? 'line-clamp-1' : 'line-clamp-2' }}">
            <a href="{{ $product->url }}" class="transition hover:text-brand">{{ $product->name }}</a>
        </h3>

        <div class="mt-1.5 flex items-center gap-1 text-[#f5a623]">
            @for ($i = 1; $i <= 5; $i++)
                <svg class="size-3.5" viewBox="0 0 20 20" fill="{{ $i <= round((float) $product->rating_avg) ? 'currentColor' : '#dfe4e8' }}">
                    <path d="M10 1.8l2.5 5.1 5.6.8-4 3.9.9 5.5-5-2.6-5 2.6.9-5.5-4-3.9 5.6-.8Z"/>
                </svg>
            @endfor
            <span class="ml-1 text-[11px] text-muted">({{ $product->rating_count }})</span>
        </div>

        <div class="mt-auto pt-2">
            <span class="font-display text-[15px] font-semibold text-ink">{{ $product->formatted_price }}</span>
        </div>
    </div>
</article>
