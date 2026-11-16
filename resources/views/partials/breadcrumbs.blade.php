@props(['items' => []])

<nav aria-label="Breadcrumb" class="border-b border-line bg-mist">
    <div class="container-x flex flex-wrap items-center gap-2 py-4 text-[13px] text-muted">
        <a href="{{ route('home') }}" class="hover:text-brand">Home</a>
        @foreach ($items as $item)
            <span aria-hidden="true">/</span>
            @if (! empty($item['url']))
                <a href="{{ $item['url'] }}" class="hover:text-brand">{{ $item['label'] }}</a>
            @else
                <span class="text-ink">{{ $item['label'] }}</span>
            @endif
        @endforeach
    </div>
</nav>
