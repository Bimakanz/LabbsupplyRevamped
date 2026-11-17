@extends('layouts.app')

@section('title', 'Blog - '.setting('site_name', 'Lab Supply'))
@section('meta_description', 'Artikel, tips, dan informasi seputar peralatan laboratorium dari Lab Supply.')

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Blog', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Blog</h1>
        <p class="mt-2 max-w-2xl text-sm leading-7 text-muted">Informasi, panduan, dan tips seputar peralatan serta kebutuhan laboratorium.</p>

        @if ($posts->isEmpty())
            <div class="mt-8 rounded-lg border border-dashed border-line bg-mist px-6 py-20 text-center">
                <p class="font-display text-lg font-medium text-ink">Belum ada artikel.</p>
                <p class="mt-2 text-sm text-muted">Nantikan artikel terbaru dari kami.</p>
            </div>
        @else
            <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <article class="group overflow-hidden rounded-lg border border-line">
                        <a href="{{ $post->url }}" class="block aspect-[16/10] overflow-hidden bg-mist">
                            @if ($post->image)
                                <img src="{{ str_starts_with($post->image, 'http') ? $post->image : asset('storage/'.$post->image) }}" alt="{{ $post->title }}"
                                     class="size-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="grid size-full place-items-center text-sm text-muted">Lab Supply</div>
                            @endif
                        </a>
                        <div class="p-5">
                            <p class="text-xs text-muted">{{ optional($post->published_at)->format('d M Y') ?? $post->created_at->format('d M Y') }}</p>
                            <h2 class="mt-2 font-display text-base font-semibold leading-6 text-ink">
                                <a href="{{ $post->url }}" class="hover:text-brand">{{ $post->title }}</a>
                            </h2>
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-muted">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 140) }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
@endsection
