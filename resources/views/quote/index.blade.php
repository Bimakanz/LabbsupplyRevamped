@extends('layouts.app')

@section('title', 'Request a Quote - '.setting('site_name', 'Lab Supply'))
@section('meta_description', 'Ajukan permintaan penawaran harga untuk pembelian alat dan bahan laboratorium dalam jumlah besar.')

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Request a Quote', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="max-w-2xl">
            <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Request penawaran Harga</h1>
            <p class="mt-3 text-sm leading-7 text-muted">
                Isi formulir di bawah untuk mendapatkan penawaran harga terbaik. Tim kami akan menghubungi Anda dalam 1&times;24 jam kerja.
            </p>
        </div>

        <div class="mt-8 grid gap-10 lg:grid-cols-[1fr_360px]">
            <form action="{{ route('quote.store') }}" method="post" class="space-y-6 rounded-lg border border-line p-6">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="name">Nama lengkap <span class="text-red-500">*</span></label>
                        <input id="name" name="name" class="field" required value="{{ old('name', auth()->user()?->name) }}">
                    </div>
                    <div>
                        <label class="label" for="company">Perusahaan / Institusi</label>
                        <input id="company" name="company" class="field" value="{{ old('company', auth()->user()?->company) }}">
                    </div>
                    <div>
                        <label class="label" for="email">Email <span class="text-red-500">*</span></label>
                        <input id="email" type="email" name="email" class="field" required value="{{ old('email', auth()->user()?->email) }}">
                    </div>
                    <div>
                        <label class="label" for="phone">No. telepon / WhatsApp <span class="text-red-500">*</span></label>
                        <input id="phone" name="phone" class="field" required value="{{ old('phone', auth()->user()?->phone) }}">
                    </div>
                </div>

                <div>
                    <label class="label" for="message">Kebutuhan Anda</label>
                    <textarea id="message" name="message" rows="5" class="field" placeholder="Jelaskan kebutuhan produk, jumlah, dan spesifikasi yang diinginkan.">{{ old('message') }}</textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-base font-semibold text-ink">Produk yang diminta</h2>
                        <span class="text-xs text-muted">{{ $items->count() }} produk</span>
                    </div>

                    @if ($items->isEmpty())
                        <p class="mt-2 rounded-lg bg-mist px-4 py-3 text-xs leading-6 text-muted">
                            Belum ada produk dipilih. Anda dapat memilih produk pada daftar di samping.
                        </p>
                    @else
                        <ul class="mt-3 divide-y divide-line rounded-lg border border-line">
                            @foreach ($items as $product)
                                <li class="flex items-center gap-3 px-4 py-3">
                                    <img src="{{ $product->image_url }}" alt="" class="size-12 rounded border border-line object-contain p-1">
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-1 text-sm font-medium text-ink">{{ $product->name }}</p>
                                        <p class="text-xs text-muted">{{ $product->formatted_price }}</p>
                                    </div>
                                    <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $product->id }}">
                                    <input type="number" name="items[{{ $loop->index }}][quantity]" value="1" min="1" max="999"
                                           class="field !w-20 !py-1.5 !text-center !text-xs" aria-label="Jumlah">
                                    <button type="button" data-quote-remove="{{ $product->id }}" class="text-xs text-muted underline hover:text-red-600">Hapus</button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <button type="submit" class="btn-primary w-full sm:w-auto">Kirim Permintaan</button>
            </form>

            <aside class="space-y-6">
                <div class="rounded-lg border border-line bg-mist p-6">
                    <h2 class="font-display text-base font-semibold text-ink">Pilih Produk</h2>
                    <p class="mt-1 text-xs text-muted">Tambahkan produk yang ingin Anda minta penawarannya.</p>

                    <form action="{{ route('quote.add') }}" method="post" class="mt-4 space-y-2">
                        @csrf
                        <x-select name="product_id" placeholder="Pilih produk..."
                                  :options="$products->map(fn ($product) => ['value' => $product->id, 'label' => $product->name])->values()->all()"
                                  required class="!py-2 !text-xs" />
                        <button type="submit" class="btn-outline !w-full !py-2 !text-xs">Tambahkan</button>
                    </form>
                </div>

                <div class="rounded-lg border border-line p-6 text-sm">
                    <h2 class="font-display text-base font-semibold text-ink">Butuh respons cepat?</h2>
                    <p class="mt-2 leading-6 text-muted">Hubungi tim sales kami langsung melalui WhatsApp pada jam kerja.</p>
                    <a href="{{ wa_link('Halo, saya ingin meminta penawaran harga.') }}" target="_blank" rel="noopener" class="btn-primary mt-4 !w-full !py-2.5 !text-xs">Chat WhatsApp</a>
                </div>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('click', function (event) {
            const remove = event.target.closest('[data-quote-remove]');

            if (remove) {
                window.postJson('{{ route('quote.remove') }}', { product_id: remove.dataset.quoteRemove })
                    .then(() => window.location.reload())
                    .catch((error) => window.toast(error.message, 'error'));
            }
        });
    </script>
@endpush
