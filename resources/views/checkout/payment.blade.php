@extends('layouts.app')

@section('title', 'Pembayaran '.$order->number.' - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Checkout', 'url' => null], ['label' => 'Pembayaran', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="mx-auto max-w-4xl">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="font-display text-[26px] font-semibold tracking-tight text-ink lg:text-[32px]">Selesaikan Pembayaran</h1>
                    <p class="mt-1 text-sm text-muted">Pesanan <strong class="text-ink">{{ $order->number }}</strong> sedang menunggu pembayaran.</p>
                </div>
                <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700">{{ $order->payment_status_label }}</span>
            </div>

            <div class="mt-8 grid gap-8 lg:grid-cols-[380px_1fr]">
                <div class="rounded-lg border border-line bg-white p-6 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-muted">Total Pembayaran</p>
                    <p class="mt-1 font-display text-[28px] font-semibold text-brand">{{ $order->formatted_total }}</p>

                    <div class="mt-6 rounded-lg border border-line bg-white p-4">
                        @if ($qr)
                            <div class="mx-auto w-full max-w-[240px] [&_svg]:h-auto [&_svg]:w-full">
                                {!! $qr !!}
                            </div>
                            <p class="mt-3 text-xs text-muted">Pindai kode QR dengan aplikasi bank atau e-wallet Anda.</p>
                        @else
                            <div class="grid min-h-[200px] place-items-center rounded bg-mist px-4 text-sm text-muted">
                                Kode pembayaran belum tersedia. Silakan hubungi kami.
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('payment.confirm', $order) }}" method="post" class="mt-5">
                        @csrf
                        <button type="submit" class="btn-primary !w-full">Sudah bayar</button>
                    </form>
                    <p class="mt-3 text-[11px] leading-5 text-muted">Setelah pembayaran diterima, status pesanan otomatis berubah menjadi <strong class="text-ink">Diproses</strong>.</p>
                </div>

                <div class="space-y-6">
                    <div class="rounded-lg border border-line p-6">
                        <h2 class="font-display text-lg font-semibold text-ink">Detail Pesanan</h2>

                        <ul class="mt-4 divide-y divide-line">
                            @foreach ($order->items as $item)
                                <li class="flex items-center gap-3 py-3">
                                    <div class="size-14 shrink-0 overflow-hidden rounded border border-line bg-mist">
                                        @if ($item->product)
                                            <img src="{{ $item->product->image_url }}" alt="" class="size-full object-contain p-1">
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-2 text-sm font-medium text-ink">{{ $item->name }}</p>
                                        <p class="mt-0.5 text-xs text-muted">{{ $item->quantity }} &times; {{ rupiah($item->price) }}</p>
                                    </div>
                                    <p class="text-sm font-medium text-ink">{{ rupiah($item->subtotal) }}</p>
                                </li>
                            @endforeach
                        </ul>

                        <dl class="mt-4 space-y-2 border-t border-line pt-4 text-sm">
                            <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd>{{ rupiah($order->subtotal) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-muted">Pengiriman</dt><dd>Gratis</dd></div>
                            <div class="flex justify-between border-t border-line pt-2 font-semibold"><dt>Total</dt><dd class="text-brand">{{ $order->formatted_total }}</dd></div>
                        </dl>
                    </div>

                    <div class="rounded-lg border border-line bg-mist p-6">
                        <h2 class="font-display text-base font-semibold text-ink">Cara Pembayaran</h2>
                        <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm leading-6 text-muted">
                            <li>Buka aplikasi mobile banking atau e-wallet yang mendukung QRIS.</li>
                            <li>Pindai kode QR di samping, lalu periksa nominal pembayaran.</li>
                            <li>Konfirmasi pembayaran dan simpan bukti transaksi.</li>
                            <li>Tekan tombol <strong class="text-ink">Sudah bayar</strong> untuk menyelesaikan pesanan.</li>
                        </ol>
                        <p class="mt-4 text-xs text-muted">Butuh bantuan? <a href="{{ wa_link('Halo, saya butuh bantuan pembayaran pesanan '.$order->number) }}" target="_blank" rel="noopener" class="text-brand underline">Hubungi kami via WhatsApp</a>.</p>
                    </div>

                    <div class="rounded-lg border border-line p-6 text-sm">
                        <h2 class="font-display text-base font-semibold text-ink">Alamat Pengiriman</h2>
                        <p class="mt-2 leading-6 text-muted">
                            {{ trim($order->billing_first_name.' '.$order->billing_last_name) }}<br>
                            {{ $order->billing_company ? $order->billing_company.'<br>' : '' }}
                            {{ $order->billing_address_1 }}{{ $order->billing_address_2 ? ', '.$order->billing_address_2 : '' }}<br>
                            {{ $order->billing_city }}{{ $order->billing_state ? ', '.$order->billing_state : '' }} {{ $order->billing_postcode }}<br>
                            {{ $order->billing_phone }} &middot; {{ $order->billing_email }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
