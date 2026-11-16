@props(['title', 'products', 'columns' => 3, 'subtitle' => null, 'link' => null, 'linkLabel' => 'View All Products'])

<section class="bg-white py-12 lg:py-16">
    <div class="container-x">
        <div class="flex flex-wrap items-end justify-between gap-4 border-b border-line pb-5">
            <div>
                <h2 class="section-title">{{ $title }}</h2>
                <p class="mt-1 text-sm text-muted">{{ $products->count() }} products</p>
            </div>
            @if ($subtitle)
                <p class="max-w-md text-sm leading-6 text-muted">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="mt-8 grid grid-cols-2 gap-x-5 gap-y-10 sm:gap-x-8 lg:grid-cols-{{ $columns }}">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        @if ($link)
            <div class="mt-12 flex justify-center">
                <a href="{{ $link }}" class="btn-outline">{{ $linkLabel }}</a>
            </div>
        @endif
    </div>
</section>
