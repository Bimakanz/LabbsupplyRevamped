@extends('layouts.app')

@section('title', setting('site_name', 'Lab Supply').' - '.setting('site_tagline', 'Lab Supply for Your Laboratory'))

@section('content')
    <section class="hero-texture relative overflow-hidden">
        <div class="container-x grid items-center gap-8 py-12 lg:grid-cols-[1.05fr_1fr] lg:py-0">
            <div class="relative z-10 py-4 lg:py-24">
                <p class="font-display text-lg text-ink/80 sm:text-xl">Accurate, reliable, and built for everyday lab work.</p>
                <h1 class="mt-3 max-w-[640px] font-montserrat text-[30px] font-bold uppercase leading-[1.15] tracking-tight text-brand sm:text-[38px] lg:text-[42px]">
                    Perform precise light analysis with consistent results — every time
                </h1>
                <p class="mt-5 font-display text-lg text-ink/75 sm:text-xl">The power of analysis in every beam of light</p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('shop') }}" class="btn-primary">Shop Now</a>
                    <a href="{{ route('quote.index') }}" class="btn-outline">Request a Quote</a>
                </div>
            </div>
            <div class="relative flex items-center justify-center">
                <img src="{{ asset('theme/img/hero.webp') }}" alt="Laboratory instrument"
                     class="relative z-10 h-auto w-full max-w-[560px] object-contain mix-blend-multiply" loading="eager">
            </div>
        </div>
    </section>

    <section class="border-b border-line bg-white">
        <div class="container-x grid grid-cols-2 divide-line py-5 sm:grid-cols-4 sm:divide-x">
            @foreach ([
                ['free_shipping_text', 'Free delivery', 'M3 7h11v8H3zM14 10h4l3 3v2h-7z'],
                ['support_text', 'Support 24/7', 'M4 13a8 8 0 1 1 16 0v4a3 3 0 0 1-3 3h-2'],
                ['payment_text', 'Secure Payment', 'M3 6h18v12H3zM3 10h18'],
                ['guarantee_text', 'Reliable Guarantee', 'M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z'],
            ] as [$key, $fallback, $icon])
                <div class="flex items-center gap-3 px-3 py-2 sm:justify-center">
                    <svg class="size-6 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="{{ $icon }}" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-[13px] font-medium text-ink">{{ setting($key, $fallback) }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-12 lg:py-16">
        <div class="container-x">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b border-line pb-5">
                <div>
                    <h2 class="section-title">New Products</h2>
                    <p class="mt-1 text-sm text-muted">{{ $newProducts->count() }} products</p>
                </div>
                <p class="max-w-md text-sm leading-6 text-muted">
                    Pilihan alat dan bahan laboratorium terbaru dari Lab Supply untuk kebutuhan riset dan produksi Anda.
                </p>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-x-5 gap-y-10 sm:gap-x-8 lg:grid-cols-3">
                @foreach ($newProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-12 flex justify-center">
                <a href="{{ route('shop') }}" class="btn-outline">View All Products</a>
            </div>
        </div>
    </section>

    <section class="bg-mist py-12">
        <div class="container-x grid gap-6 lg:grid-cols-3">
            @foreach ($categories->take(6) as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="flex items-center justify-between rounded-lg border border-line bg-white px-6 py-5 transition hover:border-brand hover:shadow-sm">
                    <span class="font-display text-[15px] font-medium text-ink">{{ $category->name }}</span>
                    <span class="text-sm text-brand">&rarr;</span>
                </a>
            @endforeach
        </div>
    </section>
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
