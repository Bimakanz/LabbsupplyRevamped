@extends('layouts.app')

@section('title', $post->title.' - '.setting('site_name', 'Lab Supply'))
@section('meta_description', $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 155))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Blog', 'url' => route('blog.index')], ['label' => $post->title, 'url' => null]]])

    <article class="container-x py-10 lg:py-14">
        <div class="mx-auto max-w-3xl">
            <p class="text-xs text-muted">{{ optional($post->published_at)->format('d M Y') ?? $post->created_at->format('d M Y') }}</p>
            <h1 class="mt-2 font-display text-[28px] font-semibold leading-tight tracking-tight text-ink lg:text-[36px]">{{ $post->title }}</h1>

            @if ($post->image)
                <img src="{{ str_starts_with($post->image, 'http') ? $post->image : asset('storage/'.$post->image) }}"
                     alt="{{ $post->title }}" class="mt-6 aspect-[16/9] w-full rounded-lg object-cover">
            @endif

            <div class="prose-site mt-8">
                {!! $post->content !!}
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="mx-auto mt-14 max-w-5xl border-t border-line pt-10">
                <h2 class="section-title">Artikel Lainnya</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-3">
                    @foreach ($related as $item)
                        <a href="{{ $item->url }}" class="group rounded-lg border border-line p-5 transition hover:border-brand">
                            <h3 class="font-display text-sm font-semibold leading-6 text-ink group-hover:text-brand">{{ $item->title }}</h3>
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-muted">{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->content), 100) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
@endsection
