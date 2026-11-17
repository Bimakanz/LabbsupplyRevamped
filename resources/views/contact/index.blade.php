@extends('layouts.app')

@section('title', 'Hubungi Kami - '.setting('site_name', 'Lab Supply'))
@section('meta_description', 'Hubungi Lab Supply untuk pertanyaan produk, penawaran harga, dan dukungan teknis laboratorium.')

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Hubungi Kami', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="max-w-2xl">
            <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Hubungi Kami</h1>
            <p class="mt-3 text-sm leading-7 text-muted">Kami siap membantu kebutuhan laboratorium Anda. Kirim pesan dan tim kami akan segera merespons.</p>
        </div>

        <div class="mt-8 grid gap-10 lg:grid-cols-[1fr_360px]">
            <form action="{{ route('contact.store') }}" method="post" class="space-y-4 rounded-lg border border-line p-6">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="name">Nama <span class="text-red-500">*</span></label>
                        <input id="name" name="name" class="field" required value="{{ old('name') }}">
                    </div>
                    <div>
                        <label class="label" for="email">Email <span class="text-red-500">*</span></label>
                        <input id="email" type="email" name="email" class="field" required value="{{ old('email') }}">
                    </div>
                    <div>
                        <label class="label" for="phone">Telepon</label>
                        <input id="phone" name="phone" class="field" value="{{ old('phone') }}">
                    </div>
                    <div>
                        <label class="label" for="subject">Subjek</label>
                        <input id="subject" name="subject" class="field" value="{{ old('subject') }}">
                    </div>
                </div>
                <div>
                    <label class="label" for="message">Pesan <span class="text-red-500">*</span></label>
                    <textarea id="message" name="message" rows="6" class="field" required>{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="btn-primary">Kirim Pesan</button>
            </form>

            <aside class="space-y-5">
                <div class="rounded-lg border border-line bg-mist p-6">
                    <h2 class="font-display text-base font-semibold text-ink">Informasi Kontak</h2>
                    <ul class="mt-4 space-y-4 text-sm text-muted">
                        <li>
                            <span class="block text-xs font-medium uppercase tracking-wide text-ink">Alamat</span>
                            <span class="mt-1 block leading-6">{{ setting('address') }}</span>
                        </li>
                        <li>
                            <span class="block text-xs font-medium uppercase tracking-wide text-ink">Telepon</span>
                            <a href="tel:{{ setting('phone_link') }}" class="mt-1 block hover:text-brand">{{ setting('phone') }}</a>
                        </li>
                        <li>
                            <span class="block text-xs font-medium uppercase tracking-wide text-ink">Email</span>
                            <a href="mailto:{{ setting('email') }}" class="mt-1 block hover:text-brand">{{ setting('email') }}</a>
                        </li>
                    </ul>
                </div>

                <div class="rounded-lg border border-line p-6">
                    <h2 class="font-display text-base font-semibold text-ink">WhatsApp</h2>
                    <p class="mt-2 text-sm leading-6 text-muted">Respon cepat pada jam kerja Senin–Jumat, 08.00–17.00 WIB.</p>
                    <a href="{{ wa_link() }}" target="_blank" rel="noopener" class="btn-primary mt-4 !w-full">Chat WhatsApp</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
