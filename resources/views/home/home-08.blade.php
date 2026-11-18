@extends('layouts.app')

@section('title', setting('site_name', 'Lab Supply').' - '.setting('site_tagline', 'Lab Supply for Your Laboratory'))

@section('content')
    @include('partials.home-hero', [
        'eyebrow' => 'Accurate, reliable, and built for everyday lab work.',
        'heading' => 'Perform precise light analysis with consistent results — every time',
        'subheading' => 'The power of analysis in every beam of light',
    ])

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
