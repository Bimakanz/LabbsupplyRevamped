@php
    $activeCategory = (string) request('category', '');
    $navCategories = $navCategories ?? \App\Models\Category::query()->active()->ordered()->get();
@endphp

<div id="category-modal" class="fixed inset-0 z-[75] hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Select Categories">
    <div class="absolute inset-0 bg-black/30 opacity-0 transition-opacity duration-200" data-category-overlay onclick="closeCategoryModal()"></div>

    <div class="absolute left-1/2 top-1/2 max-h-[85vh] w-[min(980px,calc(100vw-2rem))] -translate-x-1/2 -translate-y-1/2 overflow-y-auto rounded-lg bg-white p-6 opacity-0 shadow-2xl transition duration-200 sm:p-10"
         data-category-panel>
        <div class="flex items-start justify-between gap-4 pb-4">
            <h2 class="font-display text-xl font-normal text-muted">Select Categories</h2>
            <button type="button" onclick="closeCategoryModal()"
                    class="-mr-2 -mt-1 grid size-9 place-items-center rounded-full text-ink transition hover:bg-mist"
                    aria-label="Tutup">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="h-px w-full bg-line"></div>

        <div class="mt-5 columns-1 gap-x-12 sm:columns-2 lg:columns-3">
            <button type="button"
                    data-category-option=""
                    onclick="selectCategory('', 'All categories')"
                    class="block w-full break-inside-avoid py-2.5 text-left text-[15px] leading-6 text-[#1e2d4a] transition hover:text-brand {{ $activeCategory === '' ? 'font-medium text-brand' : '' }}">
                All categories
            </button>

            @foreach ($navCategories as $category)
                <button type="button"
                        data-category-option="{{ $category->slug }}"
                        onclick="selectCategory(@js($category->slug), @js($category->name))"
                        class="block w-full break-inside-avoid py-2.5 text-left text-[15px] leading-6 text-[#1e2d4a] transition hover:text-brand {{ $activeCategory === $category->slug ? 'font-medium text-brand' : '' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>
</div>
