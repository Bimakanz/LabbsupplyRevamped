@extends('layouts.app')

@section('title', 'Compare - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Compare', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Compare Products</h1>
                <p class="mt-2 text-sm text-muted">Bandingkan hingga 4 produk sekaligus.</p>
            </div>
            @if ($products->isNotEmpty())
                <button type="button" data-compare-clear class="text-sm text-muted underline hover:text-red-600">Hapus semua</button>
            @endif
        </div>

        @if ($products->isEmpty())
            <div class="mt-8 rounded-lg border border-dashed border-line bg-mist px-6 py-20 text-center">
                <p class="font-display text-lg font-medium text-ink">Belum ada produk untuk dibandingkan.</p>
                <p class="mt-2 text-sm text-muted">Tambahkan produk dari halaman katalog untuk melihat perbandingan spesifikasi.</p>
                <a href="{{ route('shop') }}" class="btn-primary mt-6">Jelajahi Produk</a>
            </div>
        @else
            <div class="mt-8 overflow-x-auto rounded-lg border border-line">
                <table class="w-full min-w-[720px] border-collapse text-sm">
                    <tbody>
                        <tr class="border-b border-line">
                            <th class="w-40 bg-mist px-4 py-4 text-left font-medium text-muted">Produk</th>
                            @foreach ($products as $product)
                                <td class="px-4 py-4 align-top">
                                    <div class="relative">
                                        <a href="{{ $product->url }}" class="block aspect-square overflow-hidden rounded border border-line bg-mist">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="size-full object-contain p-3">
                                        </a>
                                        <button type="button" data-compare-remove="{{ $product->id }}"
                                                class="absolute right-2 top-2 grid size-7 place-items-center rounded-full bg-white text-muted shadow hover:text-red-600" title="Hapus">
                                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/></svg>
                                        </button>
                                    </div>
                                    <a href="{{ $product->url }}" class="mt-3 block font-medium leading-6 text-ink hover:text-brand">{{ $product->name }}</a>
                                </td>
                            @endforeach
                        </tr>
                        <tr class="border-b border-line">
                            <th class="bg-mist px-4 py-3 text-left font-medium text-muted">Harga</th>
                            @foreach ($products as $product)
                                <td class="px-4 py-3 font-semibold text-brand">{{ $product->formatted_price }}</td>
                            @endforeach
                        </tr>
                        <tr class="border-b border-line">
                            <th class="bg-mist px-4 py-3 text-left font-medium text-muted">Kategori</th>
                            @foreach ($products as $product)
                                <td class="px-4 py-3 text-muted">{{ $product->category?->name ?? '-' }}</td>
                            @endforeach
                        </tr>
                        <tr class="border-b border-line">
                            <th class="bg-mist px-4 py-3 text-left font-medium text-muted">Ketersediaan</th>
                            @foreach ($products as $product)
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs {{ $product->is_in_stock ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $product->is_in_stock ? 'In stock' : 'Out of stock' }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                        <tr class="border-b border-line">
                            <th class="bg-mist px-4 py-3 text-left font-medium text-muted">SKU</th>
                            @foreach ($products as $product)
                                <td class="px-4 py-3 text-muted">{{ $product->sku }}</td>
                            @endforeach
                        </tr>
                        <tr class="border-b border-line">
                            <th class="bg-mist px-4 py-3 align-top text-left font-medium text-muted">Deskripsi</th>
                            @foreach ($products as $product)
                                <td class="px-4 py-3 align-top text-xs leading-6 text-muted">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($product->description ?? $product->short_description ?? '-'), 180) }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="bg-mist px-4 py-4"></th>
                            @foreach ($products as $product)
                                <td class="px-4 py-4">
                                    <button type="button" class="btn-primary !px-4 !py-2 !text-xs" data-add-to-cart="{{ $product->id }}">Add to cart</button>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @include('partials.shop-scripts')
    <script>
        document.addEventListener('click', function (event) {
            const remove = event.target.closest('[data-compare-remove]');

            if (remove) {
                window.postJson('{{ route('compare.remove') }}', { product_id: remove.dataset.compareRemove })
                    .then(() => window.location.reload())
                    .catch((error) => window.toast(error.message, 'error'));
            }
        });
    </script>
@endpush
