@extends('layouts.app')

@section('title', 'Home 05 - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.home-hero', [
        'eyebrow' => 'Your laboratory, fully equipped',
        'heading' => 'Quality lab supplies delivered across Indonesia',
        'subheading' => 'Belanja mudah, pengiriman cepat, dukungan teknis siap membantu.',
        'center' => true,
    ])

    <section class="bg-white py-12">
        <div class="container-x grid gap-5 lg:grid-cols-2">
            <a href="{{ route('category.show', $categories->first()->slug ?? 'consumables') }}"
               class="group relative overflow-hidden rounded-lg bg-brand-soft p-8 transition hover:bg-brand/20">
                <p class="text-xs uppercase tracking-wide text-brand">Consumables</p>
                <h2 class="mt-2 max-w-sm font-display text-2xl font-semibold leading-tight text-ink">Bahan habis pakai untuk pekerjaan harian</h2>
                <span class="mt-6 inline-block text-sm font-medium text-brand">Jelajahi &rarr;</span>
            </a>
            <a href="{{ route('category.show', $categories->skip(1)->first()->slug ?? 'glassware') }}"
               class="group relative overflow-hidden rounded-lg bg-mist p-8 transition hover:bg-line/60">
                <p class="text-xs uppercase tracking-wide text-muted">Equipment</p>
                <h2 class="mt-2 max-w-sm font-display text-2xl font-semibold leading-tight text-ink">Peralatan presisi untuk hasil yang konsisten</h2>
                <span class="mt-6 inline-block text-sm font-medium text-brand">Jelajahi &rarr;</span>
            </a>
        </div>
    </section>

    <x-product-section title="New Products" :products="$newProducts->take(8)" :columns="4" :link="route('shop')" />

    <x-product-section title="Featured Products" :products="$featured" :columns="4" :link="route('shop')" />
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
