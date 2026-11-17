@extends('layouts.app')

@section('title', 'Pembayaran Berhasil - '.setting('site_name', 'Lab Supply'))

@section('content')
    <div class="container-x py-14 lg:py-20">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mx-auto grid size-20 place-items-center rounded-full bg-emerald-50 text-emerald-600">
                <svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h1 class="mt-6 font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Pembayaran berhasil</h1>
            <p class="mt-3 text-sm leading-6 text-muted">
                Terima kasih! Pesanan <strong class="text-ink">{{ $order->number }}</strong> sudah kami terima dan sedang diproses.
                Konfirmasi juga dikirim ke <strong class="text-ink">{{ $order->billing_email }}</strong>.
            </p>

            <div class="mt-8 rounded-lg border border-line bg-mist p-6 text-left">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-display text-base font-semibold text-ink">Ringkasan Pesanan</h2>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">{{ $order->status_label }}</span>
                </div>

                <ul class="mt-4 divide-y divide-line">
                    @foreach ($order->items as $item)
                        <li class="flex items-center gap-3 py-3">
                            <div class="size-12 shrink-0 overflow-hidden rounded border border-line bg-white">
                                @if ($item->product)
                                    <img src="{{ $item->product->image_url }}" alt="" class="size-full object-contain p-1">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="line-clamp-1 text-sm font-medium text-ink">{{ $item->name }}</p>
                                <p class="text-xs text-muted">{{ $item->quantity }} &times; {{ rupiah($item->price) }}</p>
                            </div>
                            <p class="text-sm font-medium text-ink">{{ rupiah($item->subtotal) }}</p>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-3 flex items-center justify-between border-t border-line pt-3 font-semibold">
                    <span>Total</span>
                    <span class="text-brand">{{ $order->formatted_total }}</span>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('shop') }}" class="btn-primary">Lanjut Belanja</a>
                <a href="{{ route('order.track') }}" class="btn-outline">Lacak Pesanan</a>
                <a href="{{ wa_link('Halo, saya ingin menanyakan pesanan '.$order->number) }}" target="_blank" rel="noopener" class="btn-outline">Hubungi Kami</a>
            </div>
        </div>
    </div>
@endsection
