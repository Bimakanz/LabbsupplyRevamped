@php
    $compareProducts = app(\App\Services\CompareService::class)->products();
@endphp

@if ($compareProducts->isNotEmpty())
    <div class="border-t border-line bg-white">
        <div class="container-x flex flex-wrap items-center gap-4 py-3">
            <span class="text-sm font-semibold text-ink">Compare ({{ $compareProducts->count() }})</span>
            <div class="flex flex-1 flex-wrap items-center gap-3">
                @foreach ($compareProducts as $product)
                    <a href="{{ $product->url }}" class="flex items-center gap-2 rounded border border-line bg-mist px-2 py-1 text-xs text-ink hover:border-brand">
                        <img src="{{ $product->image_url }}" alt="" class="size-8 rounded object-contain">
                        <span class="max-w-[160px] truncate">{{ $product->name }}</span>
                    </a>
                @endforeach
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('compare.index') }}" class="btn-primary !px-4 !py-2 !text-xs">Compare</a>
                <button type="button" data-compare-clear class="text-xs text-muted underline hover:text-brand">Clear</button>
            </div>
        </div>
    </div>
@endif
