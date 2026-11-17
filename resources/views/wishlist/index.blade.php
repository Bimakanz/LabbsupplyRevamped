@extends('layouts.app')

@section('title', 'Wishlist - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Wishlist', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Wishlist</h1>
                <p class="mt-2 text-sm text-muted">{{ $products->count() }} produk disimpan</p>
            </div>
            <a href="{{ route('shop') }}" class="text-sm font-medium text-brand hover:text-brand-dark">&larr; Lanjut belanja</a>
        </div>

        @if ($products->isEmpty())
            <div class="mt-8 rounded-lg border border-dashed border-line bg-mist px-6 py-20 text-center">
                <p class="font-display text-lg font-medium text-ink">Wishlist Anda masih kosong.</p>
                <p class="mt-2 text-sm text-muted">Simpan produk favorit Anda agar mudah ditemukan kembali.</p>
                <a href="{{ route('shop') }}" class="btn-primary mt-6">Jelajahi Produk</a>
            </div>
        @else
            <div class="mt-8 grid grid-cols-2 gap-x-5 gap-y-10 sm:gap-x-8 lg:grid-cols-4">
                @foreach ($products as $product)
                    <div class="relative">
                        <x-product-card :product="$product" compact hide-actions />
                        <button type="button" data-wishlist-remove="{{ $product->id }}"
                                class="absolute right-3 top-3 z-20 grid size-8 cursor-pointer place-items-center rounded-full bg-white text-muted shadow transition hover:bg-red-50 hover:text-red-600"
                                title="Hapus dari wishlist">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @include('partials.shop-scripts')
    <script>
        document.addEventListener('click', function (event) {
            const button = event.target.closest('[data-wishlist-remove]');

            if (!button) {
                return;
            }

            button.disabled = true;

            window.postJson('{{ route('wishlist.toggle') }}', { product_id: button.dataset.wishlistRemove })
                .then(() => window.location.reload())
                .catch((error) => {
                    button.disabled = false;
                    window.toast(error.message, 'error');
                });
        });
    </script>
@endpush
