@extends('layouts.app')

@section('title', 'My Account - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'My Account', 'url' => route('account.dashboard')]]])

    <div class="container-x py-10 lg:py-14">
        <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
            @include('partials.account-sidebar')

            <div class="space-y-6">
                <div class="rounded-lg border border-line p-6">
                    <h1 class="font-display text-xl font-semibold text-ink">Halo, {{ auth()->user()->name }}</h1>
                    <p class="mt-1 text-sm text-muted">Selamat datang di akun Lab Supply Anda.</p>

                    <form action="{{ route('account.profile') }}" method="post" class="mt-5 grid gap-4 sm:grid-cols-2">
                        @csrf
                        <div>
                            <label class="label" for="name">Nama</label>
                            <input id="name" name="name" class="field" value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div>
                            <label class="label" for="phone">Telepon</label>
                            <input id="phone" name="phone" class="field" value="{{ old('phone', auth()->user()->phone) }}">
                        </div>
                        <div>
                            <label class="label" for="company">Perusahaan</label>
                            <input id="company" name="company" class="field" value="{{ old('company', auth()->user()->company) }}">
                        </div>
                        <div>
                            <label class="label" for="email">Email</label>
                            <input id="email" class="field bg-mist" value="{{ auth()->user()->email }}" disabled>
                        </div>
                        <div>
                            <label class="label" for="password">Password baru (opsional)</label>
                            <input id="password" type="password" name="password" class="field" autocomplete="new-password">
                        </div>
                        <div>
                            <label class="label" for="password_confirmation">Konfirmasi password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="field" autocomplete="new-password">
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" class="btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

                <div class="rounded-lg border border-line p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-lg font-semibold text-ink">Pesanan Terakhir</h2>
                        <a href="{{ route('account.orders') }}" class="text-xs font-medium text-brand hover:underline">Lihat semua</a>
                    </div>

                    @if ($orders->isEmpty())
                        <p class="mt-4 text-sm text-muted">Belum ada pesanan. <a href="{{ route('shop') }}" class="text-brand underline">Mulai belanja</a>.</p>
                    @else
                        <ul class="mt-4 divide-y divide-line">
                            @foreach ($orders as $order)
                                <li class="flex flex-wrap items-center justify-between gap-2 py-3 text-sm">
                                    <div>
                                        <a href="{{ route('account.order', $order) }}" class="font-medium text-ink hover:text-brand">{{ $order->number }}</a>
                                        <p class="text-xs text-muted">{{ $order->created_at->format('d M Y') }} &middot; {{ $order->items->count() }} item</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="rounded-full px-2.5 py-1 text-xs {{ $order->isPaid() ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $order->payment_status_label }}</span>
                                        <span class="font-medium text-brand">{{ $order->formatted_total }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
