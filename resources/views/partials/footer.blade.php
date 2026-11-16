@php
    $footerCategories = \App\Models\Category::query()->active()->ordered()->take(6)->get();
@endphp

<footer class="mt-16 bg-ink text-white/80">
    <div class="container-x grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-1">
            <a href="{{ route('home') }}">
                <img src="{{ asset('theme/img/logo.webp') }}" alt="Lab Supply" class="h-11 w-auto brightness-0 invert">
            </a>
            <p class="mt-4 text-sm leading-7">{{ setting('footer_about', 'Lab Supply for Your Laboratory') }}</p>
            <p class="mt-4 text-sm leading-7">Distributor alat dan bahan laboratorium untuk kebutuhan riset, pendidikan, dan industri di seluruh Indonesia.</p>
        </div>

        <div>
            <h2 class="font-display text-base font-semibold text-white">Kategori</h2>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('shop') }}" class="transition hover:text-white">Semua Produk</a></li>
                @foreach ($footerCategories as $category)
                    <li>
                        <a href="{{ route('category.show', $category->slug) }}" class="transition hover:text-white">{{ $category->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="font-display text-base font-semibold text-white">Get help</h2>
            <ul class="mt-4 space-y-2.5 text-sm">
                @foreach ([
                    'privacy-policy' => 'Privacy Policy',
                    'shopping-faq' => 'Shopping FAQs',
                    'terms-and-conditions' => 'Terms & Conditions',
                    'disclaimer' => 'Disclaimer',
                    'dmca' => 'DMCA',
                ] as $slug => $label)
                    <li><a href="{{ url($slug) }}" class="transition hover:text-white">{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="font-display text-base font-semibold text-white">Contact</h2>
            <ul class="mt-4 space-y-3.5 text-sm">
                <li class="flex gap-3">
                    <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M12 21s7-5.3 7-11a7 7 0 1 0-14 0c0 5.7 7 11 7 11Z" stroke-linejoin="round"/>
                        <circle cx="12" cy="10" r="2.5"/>
                    </svg>
                    <a href="https://maps.app.goo.gl/evGmvGtFXy63B4qo7" target="_blank" rel="noopener" class="leading-6 transition hover:text-white">
                        {{ setting('address', 'Ruko No. 8 Green Residence – Jl. Pangeran Sogiri, RT.01/RW.01, Kecamatan Tanah Baru, Bogor Utara 16154') }}
                    </a>
                </li>
                <li class="flex gap-3">
                    <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M6 3h3l2 5-2.5 1.5a12 12 0 0 0 6 6L16 13l5 2v3a2 2 0 0 1-2.2 2A17 17 0 0 1 4 5.2 2 2 0 0 1 6 3Z" stroke-linejoin="round"/>
                    </svg>
                    <a href="tel:{{ setting('phone_link', '628111174992') }}" class="transition hover:text-white">{{ setting('phone', '+62 811-1174-992') }}</a>
                </li>
                <li class="flex gap-3">
                    <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="m4 7 8 6 8-6" stroke-linejoin="round"/>
                    </svg>
                    <a href="mailto:{{ setting('email', 'support@labsupply.id') }}" class="transition hover:text-white">{{ setting('email', 'support@labsupply.id') }}</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-x flex flex-col items-center justify-between gap-3 py-5 text-xs sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'Lab Supply') }}. All rights reserved.</p>
            <p>{{ setting('site_tagline', 'Lab Supply for Your Laboratory') }}</p>
        </div>
    </div>
</footer>
