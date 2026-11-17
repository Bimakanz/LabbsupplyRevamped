<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', setting('site_name', 'Lab Supply').' - '.setting('site_tagline', 'Lab Supply for Your Laboratory'))</title>
    <meta name="description" content="@yield('meta_description', 'Lab Supply menyediakan alat dan bahan laboratorium untuk kebutuhan riset, pendidikan, dan industri di Indonesia.')">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', setting('site_name', 'Lab Supply'))">
    <meta property="og:description" content="@yield('meta_description', 'Lab Supply for Your Laboratory')">
    <meta property="og:image" content="{{ asset('theme/img/logo.webp') }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('theme/img/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('theme/img/favicon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('theme/img/favicon-192.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-white">
    <div id="toast-stack" class="pointer-events-none fixed right-4 top-24 z-[80] flex w-[min(360px,calc(100vw-2rem))] flex-col gap-2"></div>

    @include('partials.header')
    @include('partials.cart-drawer')
    @include('partials.category-modal')

    <main class="min-h-[50vh]">
        @yield('content')
    </main>

    @include('partials.footer')

    @include('partials.flash')

    <div id="compare-dock" class="fixed inset-x-0 bottom-0 z-40 hidden border-t border-line bg-white/95 shadow-[0_-8px_30px_rgba(0,0,0,0.08)] backdrop-blur"></div>

    <div id="quick-view-modal" class="fixed inset-0 z-[75] hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity duration-300" data-quick-view-close></div>
        <div class="relative left-1/2 top-1/2 max-h-[90vh] w-[min(960px,calc(100vw-1.5rem))] -translate-x-1/2 -translate-y-1/2 overflow-y-auto rounded-xl bg-white shadow-2xl transition-all duration-300">
            <button type="button" data-quick-view-close
                    class="absolute right-3 top-3 z-10 grid size-9 place-items-center rounded-full bg-white/90 text-ink shadow hover:bg-mist transition"
                    aria-label="Tutup">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/></svg>
            </button>
            <div data-quick-view-body></div>
        </div>
    </div>

    @include('partials.compare-bar')

    <a href="{{ wa_link() }}" target="_blank" rel="noopener"
       class="fixed bottom-5 right-5 z-50 grid size-14 place-items-center rounded-full bg-[#25D366] text-white shadow-xl transition hover:scale-105 hover:bg-[#1ebe5b]"
       aria-label="Chat WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-7">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.347-.347.52-.52.174-.174.232-.298.347-.497.116-.198.058-.371-.028-.52-.086-.148-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    @stack('scripts')
</body>
</html>
