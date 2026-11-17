@extends('layouts.app')

@section('title', ($page->meta_title ?: $page->title.' - '.setting('site_name', 'Lab Supply')))
@section('meta_description', $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content ?? ''), 155))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => $page->title, 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="mx-auto max-w-4xl">
            <h1 class="font-display text-[30px] font-semibold tracking-tight text-ink lg:text-[40px]">{{ $page->title }}</h1>

            <div class="prose-site mt-8">
                {!! $page->content ?: '<p>Konten halaman ini belum tersedia.</p>' !!}
            </div>
        </div>
    </div>
@endsection
