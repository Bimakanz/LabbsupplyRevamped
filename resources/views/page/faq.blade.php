@extends('layouts.app')

@php
    $groups = $faq['groups'];
    $totalQuestions = collect($groups)->sum(fn (array $group): int => count($group['items']));
    $pageTitle = $page->title ?: 'Shopping FAQs';
    $titleWords = preg_split('/\s+/', trim($pageTitle)) ?: [$pageTitle];
    $lastTitleWord = array_pop($titleWords);
    $leadingTitleWords = implode(' ', $titleWords);
    $heroSummary = $faq['intro'][1] ?? ($faq['intro'][0] ?? 'Temukan jawaban cepat seputar produk, pengiriman, pembayaran, garansi, dan pemesanan di LabSupply.id.');
    $initialOpen = isset($groups[0]) ? $groups[0]['key'].'-0' : null;

    $searchIndex = collect($groups)
        ->flatMap(fn (array $group): array => collect($group['items'])
            ->map(fn (array $item, int $index): array => [
                'id' => $group['key'].'-'.$index,
                'category' => $group['key'],
                'text' => \Illuminate\Support\Str::lower(html_entity_decode(strip_tags($item['question'].' '.$item['answer']), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
            ])
            ->all())
        ->values()
        ->all();

    $groupIcons = [
        '<path d="M9 3h6M10 3v6.2L5.5 17a2 2 0 0 0 1.8 3h9.4a2 2 0 0 0 1.8-3L14 9.2V3" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.5 14h9" stroke-linecap="round"/>',
        '<path d="M3 6h11v9H3z" stroke-linejoin="round"/><path d="M14 9h4l3 3v3h-7z" stroke-linejoin="round"/><circle cx="7" cy="18" r="1.6"/><circle cx="17.5" cy="18" r="1.6"/>',
        '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18" stroke-linecap="round"/><path d="M7 15h3" stroke-linecap="round"/>',
        '<path d="M12 3l7 3v5.5c0 4.2-2.9 7.5-7 9.5-4.1-2-7-5.3-7-9.5V6z" stroke-linejoin="round"/><path d="m9.2 12 2 2 3.6-4" stroke-linecap="round" stroke-linejoin="round"/>',
        '<circle cx="12" cy="8" r="3.2"/><path d="M5.5 20a6.5 6.5 0 0 1 13 0" stroke-linecap="round"/><path d="M4 4h3" stroke-linecap="round"/>',
        '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.6 2.6 0 1 1 4.3 1.9c-.9.8-1.8 1.2-1.8 2.7" stroke-linecap="round"/><path d="M12 17h.01" stroke-linecap="round"/>',
    ];

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($groups)
            ->flatMap(fn (array $group): array => collect($group['items'])->map(fn (array $item): array => [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($item['answer']), ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
                ],
            ])->all())
            ->values()
            ->all(),
    ];
@endphp

@section('title', $page->meta_title ?: $pageTitle.' - '.setting('site_name', 'Lab Supply'))
@section('meta_description', $page->meta_description ?: \Illuminate\Support\Str::limit($heroSummary, 155))

@push('head')
    <script type="application/ld+json">
        {!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => $pageTitle, 'url' => null]]])

    <div x-data="{
        query: '',
        category: 'all',
        page: 1,
        perPage: 9,
        openItem: @js($initialOpen),
        faqIndex: @js($searchIndex),
        get results() {
            const query = this.query.trim().toLowerCase();

            return this.faqIndex.filter((item) => {
                const inCategory = this.category === 'all' || item.category === this.category;
                const matchesQuery = query === '' || item.text.includes(query);

                return inCategory && matchesQuery;
            });
        },
        get totalPages() {
            return Math.max(1, Math.ceil(this.results.length / this.perPage));
        },
        get pageItems() {
            const start = (this.page - 1) * this.perPage;

            return this.results.slice(start, start + this.perPage);
        },
        get pageIds() {
            return this.pageItems.map((item) => item.id);
        },
        get showingFrom() {
            return this.results.length === 0 ? 0 : (this.page - 1) * this.perPage + 1;
        },
        get showingTo() {
            return Math.min(this.page * this.perPage, this.results.length);
        },
        hasCategoryResults(key) {
            return this.pageItems.some((item) => item.category === key);
        },
        isVisible(id) {
            return this.pageIds.includes(id);
        },
        goToPage(number) {
            if (number < 1 || number > this.totalPages || number === this.page) {
                return;
            }

            this.page = number;
            this.openItem = null;
            document.getElementById('faq-list')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
        toggle(id) {
            this.openItem = this.openItem === id ? null : id;
        },
        selectCategory(key) {
            this.category = key;
            this.page = 1;
            this.openItem = null;
        },
        reset() {
            this.query = '';
            this.category = 'all';
            this.page = 1;
            this.openItem = @js($initialOpen);
        },
    }" x-init="$watch('query', () => { page = 1 }); $watch('category', () => { page = 1 })">
        <section class="border-b border-line bg-white">
            <div class="container-x py-10 lg:py-12">
                <div class="max-w-3xl">
                    <h1 class="font-display text-[34px] font-semibold leading-[1.1] tracking-tight text-ink sm:text-[42px] lg:text-[48px]">
                        @if ($leadingTitleWords !== '')
                            {{ $leadingTitleWords }}
                        @endif
                        <span class="text-brand">{{ $lastTitleWord }}</span>
                    </h1>

                    <p class="mt-4 max-w-2xl text-[15px] leading-7 text-muted">
                        {{ $heroSummary }}
                    </p>

                </div>
            </div>
        </section>

        <div class="container-x py-10 lg:py-14">
            <div class="grid gap-8 lg:grid-cols-[280px_minmax(0,1fr)] lg:gap-10">
                <aside class="min-w-0">
                    <div>
                        <div class="rounded-xl border border-line bg-white p-2 shadow-sm">
                            <p class="px-3 pb-2 pt-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-muted">Kategori</p>

                            <nav class="flex gap-1 overflow-x-auto pb-1 lg:block lg:space-y-1 lg:overflow-visible lg:pb-0" aria-label="Kategori FAQ">
                                <button type="button" @click="selectCategory('all')"
                                        class="flex shrink-0 items-center gap-2 rounded-lg border px-3 py-2 text-left text-[13px] font-medium transition lg:w-full"
                                        :class="category === 'all' ? 'border-brand/40 bg-brand-soft text-brand' : 'border-transparent text-muted hover:bg-mist hover:text-ink'">
                                    <span class="grid size-7 shrink-0 place-items-center rounded-md transition"
                                          :class="category === 'all' ? 'bg-white text-brand shadow-sm' : 'bg-mist text-muted'">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <rect x="4" y="4" width="6" height="6" rx="1.5"/><rect x="14" y="4" width="6" height="6" rx="1.5"/><rect x="4" y="14" width="6" height="6" rx="1.5"/><rect x="14" y="14" width="6" height="6" rx="1.5"/>
                                        </svg>
                                    </span>
                                    <span class="whitespace-nowrap">Semua Pertanyaan</span>
                                    <span class="ml-auto shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                          :class="category === 'all' ? 'bg-brand text-white' : 'bg-mist text-muted'">{{ $totalQuestions }}</span>
                                </button>

                                @foreach ($groups as $group)
                                    <button type="button" @click="selectCategory(@js($group['key']))"
                                            class="flex shrink-0 items-center gap-2 rounded-lg border px-3 py-2 text-left text-[13px] font-medium transition lg:w-full"
                                            :class="category === @js($group['key']) ? 'border-brand/40 bg-brand-soft text-brand' : 'border-transparent text-muted hover:bg-mist hover:text-ink'">
                                        <span class="grid size-7 shrink-0 place-items-center rounded-md transition"
                                              :class="category === @js($group['key']) ? 'bg-white text-brand shadow-sm' : 'bg-mist text-muted'">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                                {!! $groupIcons[$loop->index % count($groupIcons)] !!}
                                            </svg>
                                        </span>
                                        <span class="whitespace-nowrap lg:whitespace-normal">{{ $group['title'] }}</span>
                                        <span class="ml-auto shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                              :class="category === @js($group['key']) ? 'bg-brand text-white' : 'bg-mist text-muted'">{{ count($group['items']) }}</span>
                                    </button>
                                @endforeach
                            </nav>
                        </div>

                    </div>
                </aside>

                <div class="min-w-0">
                    <div class="rounded-xl border border-line bg-white p-4 shadow-[0_1px_2px_rgba(51,62,72,0.04)] sm:p-5">
                        <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
                            <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-brand">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>
                                </svg>
                                Cari jawaban
                            </div>
                            <p class="text-xs text-muted">
                                <span x-show="query.trim() === '' && category === 'all'">Menampilkan <span class="font-semibold text-ink">{{ $totalQuestions }}</span> pertanyaan dalam <span class="font-semibold text-ink">{{ count($groups) }}</span> kategori</span>
                                <span x-show="query.trim() !== '' || category !== 'all'" x-cloak><span class="font-semibold text-ink" x-text="results.length"></span> pertanyaan ditemukan</span>
                            </p>
                        </div>

                        <div class="relative mt-3">
                            <svg class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>
                            </svg>
                            <input type="search" x-model.debounce.150ms="query"
                                   placeholder="Contoh: pengiriman, garansi, pembayaran"
                                   class="field !rounded-full !py-3 !pl-11 !pr-11"
                                   aria-label="Cari pertanyaan FAQ">
                            <button type="button" x-show="query !== ''" x-cloak @click="query = ''"
                                    class="absolute right-3 top-1/2 grid size-7 -translate-y-1/2 place-items-center rounded-full bg-mist text-muted transition hover:bg-line hover:text-ink"
                                    aria-label="Hapus pencarian">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="mr-1 text-[11px] font-medium uppercase tracking-[0.12em] text-muted">Paling dicari</span>
                            @foreach ([['Pengiriman', 'pengiriman'], ['Garansi', 'garansi'], ['Pembayaran', 'pembayaran'], ['Pre-order', 'pre-order']] as [$label, $term])
                                <button type="button" @click="query = '{{ $term }}'; category = 'all'; openItem = null"
                                        class="rounded-full border border-line bg-white px-3 py-1.5 text-xs font-medium text-muted transition hover:border-brand/40 hover:bg-brand-soft hover:text-brand">
                                    {{ $label }}
                                </button>
                            @endforeach

                            <button type="button" x-show="query.trim() !== '' || category !== 'all'" x-cloak @click="reset()"
                                    class="ml-auto inline-flex items-center gap-1.5 rounded-full border border-line bg-white px-3 py-1.5 text-xs font-medium text-muted transition hover:border-brand/40 hover:text-brand">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4v5h5M20 20v-5h-5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M20 9A8 8 0 0 0 5.6 6.6L4 9M4 15a8 8 0 0 0 14.4 2.4L20 15" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Reset filter
                            </button>
                        </div>
                    </div>

                    <div id="faq-list" class="mt-4 scroll-mt-6 space-y-5">
                        @foreach ($groups as $group)
                            <section x-show="(category === 'all' || category === @js($group['key'])) && hasCategoryResults(@js($group['key']))"
                                     x-transition.opacity.duration.200ms
                                     class="overflow-hidden rounded-xl border border-line bg-white shadow-[0_1px_2px_rgba(51,62,72,0.04)]">
                                <div class="flex items-center gap-3 border-b border-line bg-[#fbfcfd] px-5 py-4 sm:px-6">
                                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-brand-soft text-brand">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            {!! $groupIcons[$loop->index % count($groupIcons)] !!}
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <h2 class="font-display text-[17px] font-semibold leading-tight text-ink">{{ $group['title'] }}</h2>
                                        <p class="mt-0.5 text-xs text-muted">{{ count($group['items']) }} pertanyaan</p>
                                    </div>
                                    <span class="ml-auto hidden rounded-full border border-line bg-white px-2.5 py-1 text-[11px] font-semibold text-muted sm:inline-flex">{{ count($group['items']) }}</span>
                                </div>

                                <div class="divide-y divide-line px-5 sm:px-6">
                                    @foreach ($group['items'] as $item)
                                        @php
                                            $questionId = $group['key'].'-'.$loop->index;
                                            $answerClass = 'text-sm leading-7 text-muted [&_a]:font-medium [&_a]:text-brand [&_a]:underline [&_a]:underline-offset-2 [&_strong]:font-semibold [&_strong]:text-ink';
                                        @endphp

                                        <article x-show="isVisible(@js($questionId))">
                                            <h3>
                                                <button type="button" @click="toggle(@js($questionId))"
                                                        class="group flex w-full items-start gap-4 py-4 text-left sm:py-5"
                                                        :aria-expanded="openItem === @js($questionId) ? 'true' : 'false'"
                                                        aria-controls="faq-panel-{{ $questionId }}">
                                                    <span class="mt-0.5 grid size-7 shrink-0 place-items-center rounded-full border border-line bg-white text-[11px] font-semibold text-muted transition group-hover:border-brand/40 group-hover:text-brand">
                                                        {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                                    </span>
                                                    <span class="flex-1 text-[15px] font-medium leading-6 text-ink transition group-hover:text-brand">
                                                        {{ $item['question'] }}
                                                    </span>
                                                    <span class="mt-1 shrink-0 text-muted transition-colors duration-200 group-hover:text-brand"
                                                          :class="openItem === @js($questionId) ? 'text-brand' : ''">
                                                        <svg class="size-4 transition-transform duration-300 ease-out"
                                                             :class="openItem === @js($questionId) ? 'rotate-180' : ''"
                                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </span>
                                                </button>
                                            </h3>

                                            <div id="faq-panel-{{ $questionId }}" x-cloak
                                                 class="grid transition-[grid-template-rows,opacity] duration-300 ease-out motion-reduce:transition-none"
                                                 :class="openItem === @js($questionId) ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'"
                                                 :aria-hidden="openItem === @js($questionId) ? 'false' : 'true'"
                                                 :inert="openItem === @js($questionId) ? null : true">
                                                <div class="overflow-hidden">
                                                    <div class="pb-5 pl-0 sm:pl-[44px] sm:pr-10">
                                                        <div class="border-l-2 border-brand/30 pl-4 {{ $answerClass }}">
                                                            {!! $item['answer'] ?: 'Jawaban untuk pertanyaan ini belum tersedia.' !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <nav x-show="results.length > 0 && totalPages > 1" x-cloak
                         class="mt-6 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2"
                         aria-label="Paginasi FAQ">
                        <button type="button" @click="goToPage(page - 1)" :disabled="page === 1" aria-label="Halaman sebelumnya"
                                class="inline-flex h-9 shrink-0 items-center gap-1.5 rounded-full border border-line bg-white px-2.5 text-xs font-semibold text-muted transition enabled:hover:-translate-x-0.5 enabled:hover:border-brand/40 enabled:hover:bg-brand-soft enabled:hover:text-brand disabled:cursor-not-allowed disabled:opacity-40">
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Sebelumnya</span>
                        </button>

                        <template x-for="number in totalPages" :key="number">
                            <button type="button" @click="goToPage(number)" x-text="number"
                                    :aria-current="page === number ? 'page' : null"
                                    class="grid size-8 shrink-0 place-items-center rounded-xl text-xs font-semibold transition duration-200 sm:size-9"
                                    :class="page === number ? 'bg-brand text-white shadow-[0_8px_18px_-8px_rgba(73,129,212,0.9)]' : 'border border-line bg-white text-muted hover:-translate-y-0.5 hover:border-brand/40 hover:bg-brand-soft hover:text-brand'"></button>
                        </template>

                        <button type="button" @click="goToPage(page + 1)" :disabled="page === totalPages" aria-label="Halaman berikutnya"
                                class="inline-flex h-9 shrink-0 items-center gap-1.5 rounded-full border border-line bg-white px-2.5 text-xs font-semibold text-muted transition enabled:hover:translate-x-0.5 enabled:hover:border-brand/40 enabled:hover:bg-brand-soft enabled:hover:text-brand disabled:cursor-not-allowed disabled:opacity-40">
                            <span>Berikutnya</span>
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </nav>

                    <div x-show="results.length === 0" x-cloak class="mt-4 rounded-xl border border-dashed border-line bg-white px-6 py-12 text-center">
                        <div class="mx-auto grid size-12 place-items-center rounded-full bg-mist text-muted shadow-sm">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h2 class="mt-4 font-display text-lg font-semibold text-ink">Pertanyaan tidak ditemukan</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-muted">Coba gunakan kata kunci lain, atau hubungi tim kami jika pertanyaan Anda belum terjawab.</p>
                        <button type="button" @click="reset()" class="btn-primary mt-5 !py-2.5 !text-xs">Tampilkan semua pertanyaan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
