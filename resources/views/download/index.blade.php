@extends('layouts.app')

@section('title', 'Download - '.setting('site_name', 'Lab Supply'))
@section('meta_description', 'Unduh katalog produk, company profile, dan dokumen pendukung Lab Supply.')

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Download', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="max-w-2xl">
            <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Download</h1>
            <p class="mt-3 text-sm leading-7 text-muted">
                Unduh katalog dan dokumen pendukung untuk membantu Anda memilih produk yang tepat.
            </p>
        </div>

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($downloads as $download)
                <div class="flex flex-col rounded-lg border border-line p-6 transition hover:border-brand hover:shadow-sm">
                    <div class="grid size-12 place-items-center rounded-lg bg-brand-soft text-brand">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z" stroke-linejoin="round"/>
                            <path d="M14 3v5h5M12 12v5m0 0-2-2m2 2 2-2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2 class="mt-4 font-display text-base font-semibold text-ink">{{ $download->title }}</h2>
                    <p class="mt-2 flex-1 text-sm leading-6 text-muted">{{ $download->description }}</p>
                    <a href="{{ $download->file_url }}" target="_blank" rel="noopener" class="btn-outline mt-5 !py-2 !text-xs" @if($download->file_url === '#') onclick="event.preventDefault(); window.toast('File contoh belum tersedia. Hubungi kami untuk mendapatkan dokumen ini.', 'error');" @endif>
                        Download
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
