@extends('layouts.app')

@section('title', $title.' - '.setting('site_name', 'Lab Supply'))
@section('meta_description', 'Jelajahi '.$title.' di Lab Supply. Tersedia berbagai alat dan bahan laboratorium berkualitas.')

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Semua Produk', 'url' => route('shop')]] + (isset($activeCategory) && $activeCategory ? [['label' => $activeCategory->name, 'url' => null]] : [])])

    <div class="container-x py-10 lg:py-14">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">{{ $title }}</h1>
                <p class="mt-2 text-sm text-muted">{{ $products->count() }} produk ditemukan</p>
            </div>

            <form action="{{ url()->current() }}" method="get" class="flex items-center gap-2">
                @if (request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                <label for="sort" class="text-sm text-muted">Urutkan</label>
                <select id="sort" name="sort" class="field !w-auto !py-2" onchange="this.form.submit()">
                    <option value="name" @selected($sort === 'name')>Nama A-Z</option>
                    <option value="price_asc" @selected($sort === 'price_asc')>Harga terendah</option>
                    <option value="price_desc" @selected($sort === 'price_desc')>Harga tertinggi</option>
                    <option value="newest" @selected($sort === 'newest')>Terbaru</option>
                </select>
            </form>
        </div>

        <div class="mt-8 grid gap-10 lg:grid-cols-[250px_1fr]">
            <aside class="space-y-8">
                <form action="{{ route('search') }}" method="get" class="relative">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                           class="field !pr-10">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted hover:text-brand" aria-label="Cari">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                </form>

                <div>
                    <h2 class="font-display text-sm font-semibold uppercase tracking-wide text-ink">Kategori</h2>
                    <ul class="mt-4 space-y-1.5 text-sm">
                        <li>
                            <a href="{{ route('shop') }}" class="flex items-center justify-between rounded px-2 py-1.5 {{ request()->routeIs('shop') ? 'bg-brand-soft font-medium text-brand' : 'text-muted hover:bg-mist hover:text-ink' }}">
                                Semua Produk
                            </a>
                        </li>
                        @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('category.show', $category->slug) }}"
                                   class="flex items-center justify-between rounded px-2 py-1.5 {{ isset($activeCategory) && $activeCategory?->id === $category->id ? 'bg-brand-soft font-medium text-brand' : 'text-muted hover:bg-mist hover:text-ink' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-lg border border-line bg-mist p-5">
                    <h2 class="font-display text-sm font-semibold text-ink">Butuh bantuan?</h2>
                    <p class="mt-2 text-sm leading-6 text-muted">Tim kami siap membantu memilih produk yang sesuai kebutuhan laboratorium Anda.</p>
                    <a href="{{ wa_link() }}" target="_blank" rel="noopener" class="btn-primary mt-4 !w-full !py-2.5 !text-xs">Chat WhatsApp</a>
                </div>
            </aside>

            <div>
                @if ($products->isEmpty())
                    <div class="rounded-lg border border-dashed border-line bg-mist px-6 py-20 text-center">
                        <p class="font-display text-lg font-medium text-ink">Produk tidak ditemukan</p>
                        <p class="mt-2 text-sm text-muted">Coba kata kunci lain atau jelajahi semua produk kami.</p>
                        <a href="{{ route('shop') }}" class="btn-primary mt-6">Lihat Semua Produk</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-x-5 gap-y-10 sm:gap-x-8 lg:grid-cols-3">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
