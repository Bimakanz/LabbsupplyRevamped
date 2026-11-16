@extends('layouts.app')

@section('title', 'Home 01 - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.home-hero', [
        'eyebrow' => 'Lab Supply for Your Laboratory',
        'heading' => 'Everything your laboratory needs, in one place',
        'subheading' => 'Peralatan, bahan, dan consumables untuk riset yang lebih efisien.',
    ])

    <x-product-section title="Deal Of The Day" :products="$featured" :columns="4"
        subtitle="Pilihan produk unggulan dengan kualitas terbaik untuk laboratorium Anda."
        :link="route('shop')" link-label="Shop Now" />

    <section class="bg-mist py-12">
        <div class="container-x">
            <h2 class="section-title">Shop by Category</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}"
                       class="flex items-center justify-between rounded-lg border border-line bg-white px-5 py-4 text-sm font-medium text-ink transition hover:border-brand hover:text-brand">
                        {{ $category->name }}
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-product-section title="Featured Products" :products="$newProducts->take(8)" :columns="4" :link="route('shop')" />
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
