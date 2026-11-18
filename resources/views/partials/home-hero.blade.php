@php
    $banner = $banner ?? null;
    $title = $title ?? 'New Products';
    $eyebrow = $eyebrow ?? 'Accurate, reliable, and built for everyday lab work.';
    $heading = $heading ?? 'Perform precise light analysis with consistent results — every time';
    $subheading = $subheading ?? 'The power of analysis in every beam of light';
    $image = $image ?? asset('theme/img/hero.webp');
    $link = $link ?? route('shop');
    $center = $center ?? false;

    if ($banner) {
        $heading = $banner->title ?: $heading;
        $subheading = $banner->subtitle ?: $subheading;
        $image = $banner->image ? media_url($banner->image) : $image;
        $link = $banner->link ?: $link;
    }
@endphp

<section class="hero-texture relative overflow-hidden">
    <div class="container-x grid items-center gap-8 py-12 lg:grid-cols-[1.05fr_1fr] lg:py-0 {{ $center ? 'lg:grid-cols-1 lg:py-20' : '' }}">
        <div class="relative z-10 py-4 lg:py-24 {{ $center ? 'mx-auto max-w-3xl text-center' : '' }}">
            <p class="font-display text-lg text-ink/80 sm:text-xl">{{ $eyebrow }}</p>
            <h1 class="mt-3 font-montserrat text-[30px] font-bold uppercase leading-[1.15] tracking-tight text-brand sm:text-[38px] lg:text-[42px] {{ $center ? 'mx-auto' : 'max-w-[640px]' }}">
                {{ $heading }}
            </h1>
            <p class="mt-5 font-display text-lg text-ink/75 sm:text-xl">{{ $subheading }}</p>
            <div class="mt-8 flex flex-wrap items-center gap-3 {{ $center ? 'justify-center' : '' }}">
                <a href="{{ $link }}" class="btn-primary">Shop Now</a>
                <a href="{{ route('quote.index') }}" class="btn-outline">Request a Quote</a>
            </div>
        </div>

        @unless ($center)
            <div class="relative flex items-center justify-center">
                <img src="{{ $image }}" alt="Laboratory instrument"
                     class="relative z-10 h-auto w-full max-w-[560px] object-contain mix-blend-multiply" loading="eager">
            </div>
        @endunless
    </div>
</section>

<div class="border-b border-line bg-white">
    <div class="container-x grid grid-cols-2 divide-line py-5 sm:grid-cols-4 sm:divide-x">
        @foreach ([
            ['free_shipping_text', 'Free delivery', 'M3 7h11v8H3zM14 10h4l3 3v2h-7z'],
            ['support_text', 'Support 24/7', 'M4 13a8 8 0 1 1 16 0v4a3 3 0 0 1-3 3h-2'],
            ['payment_text', 'Secure Payment', 'M3 6h18v12H3zM3 10h18'],
            ['guarantee_text', 'Reliable Guarantee', 'M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z'],
        ] as [$key, $fallback, $icon])
            <div class="flex items-center gap-3 px-3 py-2 sm:justify-center">
                <svg class="size-6 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="{{ $icon }}" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-[13px] font-medium text-ink">{{ setting($key, $fallback) }}</span>
            </div>
        @endforeach
    </div>
</div>
