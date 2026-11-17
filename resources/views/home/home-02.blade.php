@extends('layouts.app')

@section('title', 'Home 02 - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.home-hero', [
        'eyebrow' => 'Trusted by laboratories across Indonesia',
        'heading' => 'Precision instruments for confident results',
        'subheading' => 'Solusi laboratorium yang akurat, terkalibrasi, dan siap pakai.',
        'center' => true,
    ])

    <x-product-section title="Best Seller" :products="$bestSellers" :columns="3"
        subtitle="Produk paling banyak dipilih oleh pelanggan Lab Supply."
        :link="route('shop')" link-label="Browse All" />

    <section class="bg-mist py-12 lg:py-16">
        <div class="container-x grid gap-6 lg:grid-cols-3">
            @foreach ([
                ['Konsultasi Gratis', 'Tim teknis kami membantu memilih produk yang sesuai aplikasi Anda.'],
                ['Produk Original', 'Semua produk bersumber dari distributor resmi dan bergaransi.'],
                ['Pengiriman Aman', 'Packing khusus untuk alat laboratorium yang presisi.'],
            ] as [$title, $text])
                <div class="rounded-lg border border-line bg-white p-6">
                    <h3 class="font-display text-base font-semibold text-ink">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-muted">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <x-product-section title="New Arrivals" :products="$newProducts->take(6)" :columns="3" :link="route('shop')" />
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
