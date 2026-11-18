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

                <div class="mt-5 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                    <form action="{{ route('quote.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn-dark w-full justify-center">
                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-linecap="round" stroke-linejoin="round"/>
                                <polyline points="14 2 14 8 20 8" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="16" y1="13" x2="8" y2="13" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="16" y1="17" x2="8" y2="17" stroke-linecap="round" stroke-linejoin="round"/>
                                <polyline points="10 9 9 9 8 9" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Request Penawaran (RFQ)
                        </button>
                    </form>

                    @php
                        $waNumber = '628111174992';
                        $waText = urlencode("Halo LabSupply, saya ingin diskusi & bertanya seputar produk: {$product->name} (SKU: {$product->sku})");
                    @endphp
                    <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" rel="noopener"
                       class="btn-outline w-full justify-center border-emerald-600 text-emerald-700 hover:bg-emerald-50">
                        <svg class="size-4 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2m.01 18.15c-1.5 0-2.97-.4-4.24-1.16l-.3-.18-3.12.82.83-3.04-.2-.31a7.9 7.9 0 0 1-1.22-4.37c0-4.38 3.56-7.94 7.94-7.94 4.38 0 7.94 3.56 7.94 7.94 0 4.38-3.56 7.94-7.94 7.94"/>
                        </svg>
                        Diskusi Produk via WA
                    </a>
                </div>

                <div class="mt-5 rounded-lg border border-line bg-mist/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ink">Layanan Khusus LabSupply:</p>
                    <div class="mt-2.5 grid gap-2.5 sm:grid-cols-2 text-xs">
                        <a href="{{ route('page.show', 'instalasi-training') }}" class="group flex items-start gap-2.5 rounded border border-line/70 bg-white p-2.5 transition hover:border-brand">
                            <span class="grid size-7 shrink-0 place-items-center rounded bg-brand/10 text-brand">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="font-medium text-ink group-hover:text-brand">Instalasi & Training</p>
                                <p class="mt-0.5 text-muted leading-tight">Pemasangan on-site & pelatihan operasional alat.</p>
                            </div>
                        </a>
                        <a href="{{ route('page.show', 'service') }}" class="group flex items-start gap-2.5 rounded border border-line/70 bg-white p-2.5 transition hover:border-brand">
                            <span class="grid size-7 shrink-0 place-items-center rounded bg-brand/10 text-brand">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="font-medium text-ink group-hover:text-brand">Service Alat Lab</p>
                                <p class="mt-0.5 text-muted leading-tight">Pemeliharaan berkala, perbaikan, & kalibrasi alat.</p>
                            </div>
                        </a>
                    </div>
                </div>

                <dl class="mt-6 space-y-2 text-sm">
                    <div class="flex gap-2"><dt class="w-28 shrink-0 text-muted">Kategori</dt><dd class="text-ink">{{ $product->category?->name ?? '-' }}</dd></div>
                    <div class="flex gap-2"><dt class="w-28 shrink-0 text-muted">Ketersediaan</dt><dd class="text-ink">{{ $product->is_in_stock ? 'Tersedia' : 'Habis' }}</dd></div>
                    <div class="flex gap-2"><dt class="w-28 shrink-0 text-muted">Pengiriman</dt><dd class="text-ink">Seluruh Indonesia</dd></div>
                </dl>
            </div>
        </div>

        <div class="mt-14 border-t border-line pt-8">
            <div class="flex flex-wrap gap-4 sm:gap-6 border-b border-line">
                <button type="button" @click="tab = 'description'"
                        class="border-b-2 pb-3 text-sm font-semibold transition"
                        :class="tab === 'description' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-ink'">
                    Deskripsi & Spesifikasi
                </button>
                <button type="button" @click="tab = 'services'"
                        class="border-b-2 pb-3 text-sm font-semibold transition"
                        :class="tab === 'services' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-ink'">
                    Instalasi & Service Alat
                </button>
                <button type="button" @click="tab = 'discussion'"
                        class="border-b-2 pb-3 text-sm font-semibold transition"
                        :class="tab === 'discussion' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-ink'">
                    Diskusi Produk
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

                <div x-show="tab === 'services'" x-cloak class="max-w-4xl space-y-6">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="rounded-lg border border-line bg-mist/50 p-6">
                            <div class="flex items-center gap-3">
                                <span class="grid size-10 place-items-center rounded-lg bg-brand/10 text-brand">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                    </svg>
                                </span>
                                <h3 class="font-display text-base font-semibold text-ink">Instalasi & Training</h3>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-muted">
                                LabSupply menyediakan layanan pemasangan instrumen on-site dan pelatihan pengoperasian bagi laboran, peneliti, atau teknisi lab oleh tim teknis bersertifikasi.
                            </p>
                            <ul class="mt-3 space-y-2 text-xs text-ink/80">
                                <li class="flex items-center gap-2">
                                    <svg class="size-3.5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <span>Perakitan dan pengecekan fungsi awal (Commissioning)</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="size-3.5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <span>Pelatihan standard operating procedure (SOP) alat</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="size-3.5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <span>Dukungan dokumentasi instalasi lengkap</span>
                                </li>
                            </ul>
                            <div class="mt-5">
                                <a href="{{ route('page.show', 'instalasi-training') }}" class="btn-outline !py-2 !text-xs font-semibold">
                                    Pelajari Syarat & Prosedur →
                                </a>
                            </div>
                        </div>

                        <div class="rounded-lg border border-line bg-mist/50 p-6">
                            <div class="flex items-center gap-3">
                                <span class="grid size-10 place-items-center rounded-lg bg-brand/10 text-brand">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"/>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                    </svg>
                                </span>
                                <h3 class="font-display text-base font-semibold text-ink">Service & Kalibrasi Alat Lab</h3>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-muted">
                                Menjaga akurasi dan masa pakai instrumen laboratorium Anda dengan perawatan berkala, penggantian suku cadang orisinal, serta kalibrasi terstandar.
                            </p>
                            <ul class="mt-3 space-y-2 text-xs text-ink/80">
                                <li class="flex items-center gap-2">
                                    <svg class="size-3.5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <span>Preventive maintenance & pembersihan sensor</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="size-3.5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <span>Perbaikan komponen & troubleshooting elektrikal</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="size-3.5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <span>Verifikasi akurasi pengukuran instrumen</span>
                                </li>
                            </ul>
                            <div class="mt-5">
                                <a href="{{ route('page.show', 'service') }}" class="btn-outline !py-2 !text-xs font-semibold">
                                    Informasi Layanan Service →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'discussion'" x-cloak class="max-w-4xl space-y-6">
                    <div class="rounded-lg border border-line bg-mist/40 p-6">
                        <h3 class="font-display text-base font-semibold text-ink">Konsultasi & Diskusi Teknis Produk</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">
                            Punya pertanyaan seputar kompatibilitas reagen kimia, spesifikasi alat laboratorium, atau permintaan sertifikat analisis (CoA & MSDS) untuk <strong>{{ $product->name }}</strong>? Tim spesialis produk LabSupply siap membantu Anda.
                        </p>
                        <div class="mt-5 flex flex-wrap items-center gap-3">
                            <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" rel="noopener"
                               class="btn-primary !bg-emerald-600 hover:!bg-emerald-700 !py-2.5">
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2m.01 18.15c-1.5 0-2.97-.4-4.24-1.16l-.3-.18-3.12.82.83-3.04-.2-.31a7.9 7.9 0 0 1-1.22-4.37c0-4.38 3.56-7.94 7.94-7.94 4.38 0 7.94 3.56 7.94 7.94 0 4.38-3.56 7.94-7.94 7.94"/>
                                </svg>
                                Diskusi via WhatsApp
                            </a>
                            <a href="{{ route('contact.index') }}" class="btn-outline !py-2.5">
                                Kirim Pesan / Formulir Pertanyaan
                            </a>
                            <a href="{{ route('page.show', 'diskusi-produk') }}" class="text-xs font-semibold text-brand hover:underline">
                                Baca Panduan Diskusi Produk →
                            </a>
                        </div>
                    </div>
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
