@extends('layouts.app')

@section('title', ($product->meta_title ?: $product->name.' - '.setting('site_name', 'Lab Supply')))
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description ?? $product->name), 155))

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->image_url,
            'description' => \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 300),
            'sku' => $product->sku,
            'brand' => ['@type' => 'Brand', 'name' => setting('site_name', 'Lab Supply')],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'IDR',
                'price' => (float) $product->price,
                'availability' => $product->is_in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => $product->url,
            ],
        ], JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    @include('partials.breadcrumbs', ['items' => array_filter([
        ['label' => 'Semua Produk', 'url' => route('shop')],
        $product->category ? ['label' => $product->category->name, 'url' => route('category.show', $product->category->slug)] : null,
        ['label' => $product->name, 'url' => null],
    ])])

    <div class="container-x py-10 lg:py-14" x-data="{ qty: 1, tab: 'description', activeImage: '{{ $product->image_url }}' }">
        <div class="grid gap-10 lg:grid-cols-2 lg:gap-14">
            <div>
                <div class="overflow-hidden rounded-lg border border-line bg-[#f6f7f8]">
                    <img :src="activeImage" src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         class="aspect-square w-full object-contain p-6" width="800" height="800">
                </div>

                @if ($product->images->count() > 1)
                    <div class="mt-4 flex gap-3">
                        @foreach ($product->images as $image)
                            <button type="button" @click="activeImage = '{{ $image->url }}'"
                                    class="size-20 overflow-hidden rounded border border-line bg-mist transition hover:border-brand">
                                <img src="{{ $image->url }}" alt="{{ $image->alt ?? $product->name }}" class="size-full object-contain p-1">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                @if ($product->category)
                    <a href="{{ route('category.show', $product->category->slug) }}" class="text-xs font-medium uppercase tracking-[0.1em] text-brand">
                        {{ $product->category->name }}
                    </a>
                @endif

                <h1 class="mt-2 font-display text-[26px] font-semibold leading-tight text-ink lg:text-[34px]">{{ $product->name }}</h1>

                <div class="mt-3 flex items-center gap-2 text-sm text-muted">
                    <div class="flex items-center gap-0.5 text-[#f5a623]">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="size-4" viewBox="0 0 20 20" fill="{{ $i <= round((float) $product->rating_avg) ? 'currentColor' : '#dfe4e8' }}">
                                <path d="M10 1.8l2.5 5.1 5.6.8-4 3.9.9 5.5-5-2.6-5 2.6.9-5.5-4-3.9 5.6-.8Z"/>
                            </svg>
                        @endfor
                    </div>
                    <span>{{ $product->rating_count }} review</span>
                    <span aria-hidden="true">|</span>
                    <span>SKU: {{ $product->sku }}</span>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-4">
                    <span class="font-display text-[30px] font-semibold text-ink">{{ $product->formatted_price }}</span>
                    @if ($product->is_in_stock)
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">In stock ({{ $product->stock }})</span>
                    @else
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Out of stock</span>
                    @endif
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3 border-y border-line py-5">
                    <div class="flex h-11 items-center rounded-full border border-line">
                        <button type="button" class="grid size-11 place-items-center text-lg text-muted hover:text-ink" @click="qty = Math.max(1, qty - 1)">−</button>
                        <input type="number" x-model.number="qty" min="1" max="99" class="w-12 border-0 bg-transparent text-center text-sm font-medium outline-none" aria-label="Jumlah">
                        <button type="button" class="grid size-11 place-items-center text-lg text-muted hover:text-ink" @click="qty = Math.min(99, qty + 1)">+</button>
                    </div>

                    <button type="button" class="btn-primary min-w-[190px] flex-1 sm:flex-none"
                            data-product-add="{{ $product->id }}" :data-quantity="qty"
                            @click="$dispatch('add-to-cart', { id: {{ $product->id }}, qty: qty })">
                        Add to cart
                    </button>

                    <button type="button" class="btn-outline" data-wishlist-toggle="{{ $product->id }}">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M12 20.7s-8-4.7-8-10.2A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 8 3.5c0 5.5-8 10.2-8 10.2Z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Wishlist
                    </button>

                    <button type="button" class="btn-outline" data-compare-toggle="{{ $product->id }}">Compare</button>
                </div>

                <div class="mt-5 flex flex-col gap-3">
                    <a href="{{ route('quote.index') }}" class="btn-dark w-full sm:w-auto">Request Penawaran Harga</a>
                    <p class="text-xs leading-6 text-muted">Butuh harga khusus untuk pembelian dalam jumlah besar? Ajukan permintaan penawaran dan tim kami akan menghubungi Anda.</p>
                </div>

                <dl class="mt-6 space-y-2 text-sm">
                    <div class="flex gap-2"><dt class="w-28 shrink-0 text-muted">Kategori</dt><dd class="text-ink">{{ $product->category?->name ?? '-' }}</dd></div>
                    <div class="flex gap-2"><dt class="w-28 shrink-0 text-muted">Ketersediaan</dt><dd class="text-ink">{{ $product->is_in_stock ? 'Tersedia' : 'Habis' }}</dd></div>
                    <div class="flex gap-2"><dt class="w-28 shrink-0 text-muted">Pengiriman</dt><dd class="text-ink">Seluruh Indonesia</dd></div>
                </dl>
            </div>
        </div>

        <div class="mt-14 border-t border-line pt-8">
            <div class="flex gap-6 border-b border-line">
                <button type="button" @click="tab = 'description'"
                        class="border-b-2 pb-3 text-sm font-semibold transition"
                        :class="tab === 'description' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-ink'">
                    Description
                </button>
                <button type="button" @click="tab = 'reviews'"
                        class="border-b-2 pb-3 text-sm font-semibold transition"
                        :class="tab === 'reviews' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-ink'">
                    Reviews ({{ $product->approvedReviews->count() }})
                </button>
            </div>

            <div class="py-7">
                <div x-show="tab === 'description'" class="prose-site max-w-4xl">
                    {!! $product->description ?: '<p>'.e($product->short_description ?: 'Deskripsi produk belum tersedia. Hubungi kami untuk informasi lebih lanjut.').'</p>' !!}
                </div>

                <div x-show="tab === 'reviews'" x-cloak>
                    <div class="grid gap-10 lg:grid-cols-[1fr_380px]">
                        <div class="space-y-5">
                            @forelse ($product->approvedReviews as $review)
                                <div class="rounded-lg border border-line p-5">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium text-ink">{{ $review->name }}</p>
                                        <p class="text-xs text-muted">{{ $review->created_at->format('d M Y') }}</p>
                                    </div>
                                    <div class="mt-1 flex items-center gap-0.5 text-[#f5a623]">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="size-3.5" viewBox="0 0 20 20" fill="{{ $i <= $review->rating ? 'currentColor' : '#dfe4e8' }}">
                                                <path d="M10 1.8l2.5 5.1 5.6.8-4 3.9.9 5.5-5-2.6-5 2.6.9-5.5-4-3.9 5.6-.8Z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="mt-2 text-sm leading-6 text-muted">{{ $review->comment }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-muted">Belum ada review untuk produk ini.</p>
                            @endforelse
                        </div>

                        <div class="rounded-lg border border-line bg-mist p-5">
                            <h2 class="font-display text-base font-semibold text-ink">Tulis Review</h2>
                            <p class="mt-1 text-xs text-muted">Review Anda akan tampil setelah disetujui admin.</p>
                            <form action="{{ route('reviews.store', $product) }}" method="post" class="mt-4 space-y-3">
                                @csrf
                                <div>
                                    <label class="label" for="review_name">Nama</label>
                                    <input id="review_name" name="name" class="field" required value="{{ old('name') }}">
                                </div>
                                <div>
                                    <label class="label" for="review_email">Email</label>
                                    <input id="review_email" type="email" name="email" class="field" required value="{{ old('email') }}">
                                </div>
                                <div>
                                    <label class="label" for="review_rating">Rating</label>
                                    <x-select name="rating"
                                              :options="collect(range(5, 1))->map(fn ($i) => ['value' => $i, 'label' => $i.' bintang'])->all()"
                                              :value="old('rating', 5)" />
                                </div>
                                <div>
                                    <label class="label" for="review_comment">Review</label>
                                    <textarea id="review_comment" name="comment" rows="3" class="field" required>{{ old('comment') }}</textarea>
                                </div>
                                <button type="submit" class="btn-primary !w-full">Kirim Review</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($frequentlyBought->isNotEmpty())
            <section class="mt-12">
                <h2 class="section-title">Frequently Bought Together</h2>
                <div class="mt-6 grid grid-cols-2 gap-x-5 gap-y-10 sm:gap-x-8 lg:grid-cols-3">
                    @foreach ($frequentlyBought as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($related->isNotEmpty())
            <section class="mt-14">
                <h2 class="section-title">Related Products</h2>
                <div class="mt-6 grid grid-cols-2 gap-x-5 gap-y-10 sm:gap-x-8 lg:grid-cols-4">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($recentlyViewed->isNotEmpty())
            <section class="mt-14">
                <h2 class="section-title">Recently Viewed</h2>
                <div class="mt-6 grid grid-cols-2 gap-x-5 gap-y-10 sm:gap-x-8 lg:grid-cols-4">
                    @foreach ($recentlyViewed as $item)
                        <x-product-card :product="$item" compact />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    @include('partials.shop-scripts')
    <script>
        document.addEventListener('add-to-cart', async function (event) {
            try {
                const data = await window.postJson('{{ route('cart.add') }}', {
                    product_id: event.detail.id,
                    quantity: event.detail.qty,
                });

                window.toast(data.message);
                document.querySelectorAll('[data-cart-count]').forEach((el) => (el.textContent = data.count));
                document.querySelectorAll('[data-cart-total]').forEach((el) => (el.textContent = data.total));
                window.refreshCartCounters();
            } catch (error) {
                window.toast(error.message, 'error');
            }
        });
    </script>
@endpush
