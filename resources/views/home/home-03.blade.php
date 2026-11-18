@extends('layouts.app')

@section('title', 'Home 03 - '.setting('site_name', 'Lab Supply'))

@section('content')
    <section class="bg-slate-brand py-16 text-white lg:py-20">
        <div class="container-x grid items-center gap-10 lg:grid-cols-2">
            <div>
                <p class="text-sm uppercase tracking-[0.18em] text-white/70">Laboratory Partner</p>
                <h1 class="mt-4 font-display text-[32px] font-bold leading-tight sm:text-[42px]">
                    {{ $banner?->title ?: 'Lab Supply for Your Laboratory' }}
                </h1>
                <p class="mt-4 max-w-lg text-sm leading-7 text-white/80">
                    {{ $banner?->subtitle ?: 'Menyediakan alat dan bahan laboratorium untuk institusi pendidikan, rumah sakit, dan industri di seluruh Indonesia.' }}
                </p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ $banner?->link ?: route('shop') }}" class="btn bg-white text-ink hover:bg-mist">Lihat Produk</a>
                    <a href="{{ route('quote.index') }}" class="btn border border-white/40 text-white hover:bg-white/10">Minta Penawaran</a>
                </div>
            </div>
            <img src="{{ $banner?->image ? media_url($banner->image) : asset('theme/img/hero.webp') }}" alt="Laboratory" class="mx-auto max-h-[380px] w-full object-contain">
        </div>
    </section>

    <x-product-section title="Produk Unggulan" :products="$featured" :columns="4" :link="route('shop')" />

    <section class="bg-mist py-12">
        <div class="container-x grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categories->take(8) as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="rounded-lg border border-line bg-white px-5 py-6 text-center text-sm font-medium text-ink transition hover:border-brand hover:text-brand">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </section>

    <x-product-section title="New Products" :products="$newProducts->take(8)" :columns="4" :link="route('shop')" />
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
