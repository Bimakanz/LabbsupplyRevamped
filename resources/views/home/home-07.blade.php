@extends('layouts.app')

@section('title', 'Home 07 - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.home-hero', [
        'eyebrow' => 'Fast delivery. Trusted quality.',
        'heading' => 'Lab supplies that keep your research moving',
        'subheading' => 'Pesan hari ini, tim kami siap membantu pengadaan laboratorium Anda.',
    ])

    <x-product-section title="Featured Products" :products="$featured" :columns="4" :link="route('shop')" />

    <section class="bg-white py-12">
        <div class="container-x grid gap-6 lg:grid-cols-[1.2fr_1fr]">
            <div class="rounded-lg bg-brand p-8 text-white">
                <h2 class="font-display text-2xl font-semibold">Butuh penawaran harga?</h2>
                <p class="mt-3 max-w-md text-sm leading-7 text-white/85">
                    Kirimkan daftar kebutuhan Anda, tim sales kami akan menyiapkan penawaran resmi lengkap dengan spesifikasi teknis.
                </p>
                <a href="{{ route('quote.index') }}" class="btn mt-6 bg-white text-ink hover:bg-mist">Request a Quote</a>
            </div>
            <div class="rounded-lg border border-line p-8">
                <h2 class="font-display text-xl font-semibold text-ink">Layanan Teknis</h2>
                <p class="mt-3 text-sm leading-7 text-muted">
                    Instalasi, kalibrasi, dan pelatihan penggunaan alat oleh teknisi berpengalaman.
                </p>
                <a href="{{ url('instalasi-training') }}" class="btn-outline mt-6 !py-2.5 !text-xs">Pelajari Layanan</a>
            </div>
        </div>
    </section>

    <x-product-section title="New Products" :products="$newProducts->take(8)" :columns="4" :link="route('shop')" />
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
