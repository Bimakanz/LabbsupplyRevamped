@php
    $cartService = app(\App\Services\CartService::class);
    $wishlistService = app(\App\Services\WishlistService::class);
    $compareService = app(\App\Services\CompareService::class);
    $navCategories = \App\Models\Category::query()->active()->ordered()->get();
    $cartCount = $cartService->count();
    $cartTotal = $cartService->total();
    $wishlistCount = $wishlistService->count();
    $compareCount = $compareService->count();
@endphp

<header x-data="{ mobileOpen: false }" id="site-header"
        class="sticky top-0 z-50 bg-white transition-transform duration-300 ease-[cubic-bezier(0.33,1,0.68,1)] will-change-transform">
    <div class="container-x flex items-center gap-4 py-4 lg:gap-8 lg:py-6">
        <a href="{{ route('home') }}" class="shrink-0" aria-label="Lab Supply">
            <img src="{{ asset('theme/img/logo.webp') }}" alt="Lab Supply" class="h-9 w-auto sm:h-10 lg:h-12">
        </a>

        <form action="{{ route('search') }}" method="get" class="hidden flex-1 lg:block">
            <div class="mx-auto flex h-14 w-full max-w-[680px] items-center rounded-full border-2 border-[#9db4cd] bg-white pl-6 pr-2">
                <div class="relative shrink-0">
                    <button type="button" onclick="openCategoryModal()"
                            class="flex h-8 w-[150px] items-center justify-between gap-2 pr-1 text-left text-[15px] text-ink outline-none">
                        <span data-category-label class="truncate">{{ request('category') ? ($navCategories->firstWhere('slug', request('category'))?->name ?? 'All categories') : 'All categories' }}</span>
                        <svg class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <input type="hidden" name="category" value="{{ request('category') }}" data-category-input>
                </div>
                <span class="mx-4 h-7 w-px bg-line"></span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search for products"
                       class="h-full w-full bg-transparent text-[15px] text-ink outline-none placeholder:text-muted/80">
                <button type="submit" aria-label="Cari"
                        class="grid size-10 shrink-0 place-items-center rounded-full bg-brand text-white transition hover:bg-brand-dark">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5" stroke-linecap="round"></path>
                    </svg>
                </button>
            </div>
        </form>

        <div class="ml-auto flex items-center gap-3 sm:gap-5">
            <a href="{{ route('wishlist.index') }}" class="group hidden items-center gap-2 text-ink sm:flex" title="Wishlist">
                <span class="relative">
                    <svg class="size-6 text-ink transition group-hover:text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M12 20.7s-8-4.7-8-10.2A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 8 3.5c0 5.5-8 10.2-8 10.2Z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span data-wishlist-count class="absolute -right-2 -top-2 grid min-w-[18px] place-items-center rounded-full bg-brand px-1 text-[10px] font-semibold leading-[18px] text-white">{{ $wishlistCount }}</span>
                </span>
            </a>

            <a href="{{ route('compare.index') }}" class="group hidden items-center gap-2 text-ink sm:flex" title="Compare">
                <span class="relative">
                    <svg class="size-6 text-ink transition group-hover:text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M4 7h10M4 7l3-3M4 7l3 3M20 17H10m10 0-3-3m3 3-3 3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span data-compare-count class="absolute -right-2 -top-2 grid min-w-[18px] place-items-center rounded-full bg-brand px-1 text-[10px] font-semibold leading-[18px] text-white">{{ $compareCount }}</span>
                </span>
            </a>

            <button type="button" onclick="openCartDrawer()" class="flex items-center gap-3 text-ink" title="Cart" aria-label="Buka keranjang">
                <span class="relative">
                    <svg class="size-7 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h8.4a2 2 0 0 0 2-1.6L21.5 7H6" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9.5" cy="20" r="1.4"></circle>
                        <circle cx="18" cy="20" r="1.4"></circle>
                    </svg>
                    <span data-cart-count class="absolute -right-2 -top-2 grid min-w-[18px] place-items-center rounded-full bg-brand px-1 text-[10px] font-semibold leading-[18px] text-white">{{ $cartCount }}</span>
                </span>
                <span class="hidden text-sm leading-tight xl:block">
                    <span class="block text-muted">Your cart:</span>
                    <span data-cart-total class="block font-semibold text-brand">{{ rupiah($cartTotal) }}</span>
                </span>
            </a>

            <button type="button" class="grid size-10 place-items-center rounded-md border border-line text-ink lg:hidden" @click="mobileOpen = true" aria-label="Menu">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="container-x pb-3 lg:hidden">
        <form action="{{ route('search') }}" method="get" class="flex h-12 items-center rounded-full border-2 border-[#9db4cd] bg-white pl-5 pr-1.5">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search for products"
                   class="h-full w-full bg-transparent text-sm outline-none placeholder:text-muted/80">
            <button type="submit" class="grid size-9 place-items-center rounded-full bg-brand text-white" aria-label="Cari">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5" stroke-linecap="round"></path>
                </svg>
            </button>
        </form>
    </div>

    <nav class="bg-slate-brand text-white">
        <div class="container-x hidden items-center gap-8 lg:flex">
            <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button type="button" class="nav-link" @click="open = !open">
                    Produk
                    <svg class="size-3.5 transition" :class="open && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5 7.5 10 12l5-4.5" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                    </svg>
                </button>
                <div x-show="open" x-transition.origin.top.left x-cloak
                     class="absolute left-0 top-full w-[560px] rounded-b-lg border border-t-0 border-line bg-white p-5 text-ink shadow-xl">
                    <div class="grid grid-cols-3 gap-x-6 gap-y-2">
                        <a href="{{ route('shop') }}" class="col-span-3 mb-1 flex items-center justify-between rounded-md bg-mist px-3 py-2 text-sm font-semibold text-ink hover:text-brand">
                            Semua Produk
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        @foreach ($navCategories as $category)
                            <a href="{{ route('category.show', $category->slug) }}" class="rounded px-2 py-1.5 text-[13.5px] text-muted hover:bg-mist hover:text-brand">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button type="button" class="nav-link" @click="open = !open">
                    Cara Order
                    <svg class="size-3.5 transition" :class="open && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5 7.5 10 12l5-4.5" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                    </svg>
                </button>
                <div x-show="open" x-transition.origin.top.left x-cloak class="absolute left-0 top-full w-60 rounded-b-lg border border-t-0 border-line bg-white py-2 text-ink shadow-xl">
                    @foreach ([
                        ['quote.index', 'Request a Quote'],
                        ['order.track', 'Order Tracking'],
                        ['faqs', 'FAQs'],
                        ['shopping-faq', 'Shopping FAQs'],
                    ] as [$route, $label])
                        <a href="{{ \Illuminate\Support\Facades\Route::has($route) ? route($route) : url($route) }}"
                           class="block px-4 py-2 text-sm text-muted hover:bg-mist hover:text-brand">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button type="button" class="nav-link" @click="open = !open">
                    Tentang Kami
                    <svg class="size-3.5 transition" :class="open && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5 7.5 10 12l5-4.5" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                    </svg>
                </button>
                <div x-show="open" x-transition.origin.top.left x-cloak class="absolute left-0 top-full w-60 rounded-b-lg border border-t-0 border-line bg-white py-2 text-ink shadow-xl">
                    @foreach ([
                        ['tentang-kami', 'Tentang Kami'],
                        ['service', 'Service'],
                        ['instalasi-training', 'Instalasi & Training'],
                        ['diskusi-produk', 'Diskusi Produk'],
                        ['home-lab', 'Home Lab'],
                        ['reagecon', 'Reagecon'],
                        ['download', 'Download'],
                        ['blog', 'Blog'],
                        ['hubungi-kami', 'Hubungi Kami'],
                    ] as [$slug, $label])
                        <a href="{{ url($slug) }}" class="block px-4 py-2 text-sm text-muted hover:bg-mist hover:text-brand">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('cart.index') }}" class="nav-link">Cart</a>
            <a href="{{ route('checkout.index') }}" class="nav-link">Checkout</a>

            <div class="ml-auto flex h-12 items-center gap-5 text-[13px]">
                <span class="opacity-90">{{ setting('free_shipping_text', 'Free delivery') }}</span>
                <span class="opacity-90">{{ setting('support_text', 'Support 24/7') }}</span>
            </div>
        </div>

        <div class="container-x flex h-12 items-center justify-between lg:hidden">
            <a href="{{ route('shop') }}" class="text-sm font-medium">Semua Produk</a>
            <a href="{{ route('quote.index') }}" class="text-sm font-medium">Request Quote</a>
            <a href="{{ route('order.track') }}" class="text-sm font-medium">Order Tracking</a>
        </div>
    </nav>

    <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-[60] lg:hidden">
        <div class="absolute inset-0 bg-black/40" @click="mobileOpen = false"></div>
        <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="absolute inset-y-0 left-0 flex w-[86%] max-w-sm flex-col overflow-y-auto bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <img src="{{ asset('theme/img/logo.webp') }}" alt="Lab Supply" class="h-8 w-auto">
                <button type="button" class="grid size-9 place-items-center rounded-md border border-line" @click="mobileOpen = false" aria-label="Tutup">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/></svg>
                </button>
            </div>
            <nav class="flex flex-col gap-1 px-5 py-4 text-[15px]">
                <a href="{{ route('shop') }}" class="py-2 font-medium">Semua Produk</a>
                @foreach ($navCategories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" class="py-1.5 pl-3 text-sm text-muted">{{ $category->name }}</a>
                @endforeach
                <div class="my-2 h-px bg-line"></div>
                @foreach ([
                    ['quote.index', 'Request a Quote'],
                    ['order.track', 'Order Tracking'],
                    ['wishlist.index', 'Wishlist'],
                    ['compare.index', 'Compare'],
                ] as [$route, $label])
                    <a href="{{ route($route) }}" class="py-2 font-medium">{{ $label }}</a>
                @endforeach
                <button type="button" class="py-2 text-left font-medium" @click="mobileOpen = false; openCartDrawer()">Cart</button>
                <a href="{{ route('checkout.index') }}" class="py-2 font-medium">Checkout</a>
                <div class="my-2 h-px bg-line"></div>
                @foreach (['tentang-kami' => 'Tentang Kami', 'service' => 'Service', 'instalasi-training' => 'Instalasi & Training', 'diskusi-produk' => 'Diskusi Produk', 'download' => 'Download', 'blog' => 'Blog'] as $slug => $label)
                    <a href="{{ url($slug) }}" class="py-2 text-sm text-muted">{{ $label }}</a>
                @endforeach
            </nav>
        </div>
    </div>
</header>

<script>
    (function () {
        const header = document.getElementById('site-header');

        if (! header) {
            return;
        }

        const hiddenClasses = ['-translate-y-full', 'pointer-events-none'];
        let lastY = window.scrollY;
        let ticking = false;

        function hideHeader() {
            hiddenClasses.forEach((name) => header.classList.add(name));
        }

        function showHeader() {
            hiddenClasses.forEach((name) => header.classList.remove(name));
        }

        function onScroll() {
            const y = Math.max(0, window.scrollY);
            const delta = y - lastY;

            if (y <= 80) {
                showHeader();
            } else if (delta > 6) {
                hideHeader();
            } else if (delta < -2) {
                showHeader();
            }

            lastY = y;
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (! ticking) {
                ticking = true;
                window.requestAnimationFrame(onScroll);
            }
        }, { passive: true });

        document.addEventListener('focusin', showHeader);
    })();
</script>
