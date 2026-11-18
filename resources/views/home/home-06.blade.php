@extends('layouts.app')

@section('title', 'Home 06 - '.setting('site_name', 'Lab Supply'))

@section('content')
@php
    $heroImage = $banner?->image ? media_url($banner->image) : null;
@endphp

    <section class="relative overflow-hidden bg-ink bg-cover bg-center py-16 text-white lg:py-24"
             @if ($heroImage) style="background-image: url('{{ $heroImage }}');" @endif>
        @if ($heroImage)
            <div class="absolute inset-0 bg-gradient-to-r from-ink/90 via-ink/75 to-ink/35"></div>
        @endif

        <div class="container-x relative grid items-center gap-10 lg:grid-cols-[1fr_1.1fr]">
            <div class="max-w-[640px]">
                <p class="text-sm uppercase tracking-[0.2em] text-white/60">Lab Supply</p>
                <h1 class="mt-4 font-display text-[32px] font-bold leading-tight sm:text-[44px]">
                    {{ $banner?->title ?: 'Science needs reliable tools. We deliver them.' }}
                </h1>
                <p class="mt-4 max-w-lg text-sm leading-7 text-white/75">
                    {{ $banner?->subtitle ?: 'Alat dan bahan laboratorium pilihan untuk riset, diagnostik, dan pendidikan — dengan layanan purna jual yang responsif.' }}
                </p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ $banner?->link ?: route('shop') }}" class="btn-primary">Shop Now</a>
                    <a href="{{ route('contact.index') }}" class="btn border border-white/30 text-white hover:bg-white/10">Hubungi Kami</a>
                </div>
            </div>

            <img src="{{ asset('theme/img/hero.webp') }}" alt="Laboratory" class="mx-auto max-h-[360px] w-full object-contain">
        </div>
    </section>

    <x-product-section title="Best Seller" :products="$bestSellers" :columns="3"
        subtitle="Produk andalan yang paling sering dipesan pelanggan kami." :link="route('shop')" />

    <section class="bg-mist py-12">
        <div class="container-x grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ($categories->take(8) as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="rounded-lg border border-line bg-white px-4 py-5 text-center text-sm font-medium text-ink transition hover:border-brand hover:text-brand">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </section>

    <x-product-section title="New Products" :products="$newProducts->take(6)" :columns="3" :link="route('shop')" />
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
