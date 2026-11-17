@extends('layouts.app')

@section('title', 'Home 04 - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.home-hero', [
        'eyebrow' => 'Solutions for every laboratory',
        'heading' => 'Build a smarter, safer laboratory workflow',
        'subheading' => 'Dari glassware hingga instrumen analitik, semuanya tersedia.',
    ])

    <section class="bg-white py-12 lg:py-16">
        <div class="container-x grid gap-6 lg:grid-cols-3">
            @foreach ($categories->take(3) as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="group relative overflow-hidden rounded-lg border border-line bg-mist p-7 transition hover:border-brand">
                    <p class="text-xs uppercase tracking-wide text-muted">Kategori</p>
                    <h2 class="mt-2 font-display text-xl font-semibold text-ink group-hover:text-brand">{{ $category->name }}</h2>
                    <p class="mt-2 text-sm leading-6 text-muted">Temukan produk pilihan untuk kebutuhan {{ strtolower($category->name) }}.</p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-brand">Lihat produk <span aria-hidden="true">&rarr;</span></span>
                </a>
            @endforeach
        </div>
    </section>

    <x-product-section title="Top Selling Product" :products="$bestSellers" :columns="3" :link="route('shop')" />

    <section class="bg-slate-brand py-12 text-white">
        <div class="container-x flex flex-col items-center justify-between gap-5 text-center lg:flex-row lg:text-left">
            <div>
                <h2 class="font-display text-2xl font-semibold">Butuh harga khusus untuk pengadaan?</h2>
                <p class="mt-2 text-sm text-white/80">Ajukan penawaran dan dapatkan harga terbaik untuk institusi Anda.</p>
            </div>
            <a href="{{ route('quote.index') }}" class="btn bg-white text-ink hover:bg-mist">Request a Quote</a>
        </div>
    </section>

    <x-product-section title="New Products" :products="$newProducts->take(6)" :columns="3" :link="route('shop')" />
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
