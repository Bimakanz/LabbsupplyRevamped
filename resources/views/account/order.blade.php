@extends('layouts.app')

@section('title', 'Detail Pesanan '.$order->number.' - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [
        ['label' => 'My Account', 'url' => route('account.dashboard')],
        ['label' => 'Pesanan', 'url' => route('account.orders')],
        ['label' => $order->number, 'url' => null],
    ]])

    <div class="container-x py-10 lg:py-14">
        <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
            @include('partials.account-sidebar')

            <div class="space-y-6">
                <div class="rounded-lg border border-line p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h1 class="font-display text-xl font-semibold text-ink">{{ $order->number }}</h1>
                            <p class="mt-1 text-xs text-muted">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-mist px-3 py-1 text-xs font-medium text-ink">{{ $order->status_label }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $order->isPaid() ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $order->payment_status_label }}</span>
                        </div>
                    </div>

                    <ul class="mt-5 divide-y divide-line">
                        @foreach ($order->items as $item)
                            <li class="flex items-center gap-3 py-3">
                                <div class="size-14 shrink-0 overflow-hidden rounded border border-line bg-mist">
                                    @if ($item->product)
                                        <img src="{{ $item->product->image_url }}" alt="" class="size-full object-contain p-1">
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-ink">{{ $item->name }}</p>
                                    <p class="text-xs text-muted">{{ $item->quantity }} &times; {{ rupiah($item->price) }}</p>
                                </div>
                                <p class="text-sm font-medium text-ink">{{ rupiah($item->subtotal) }}</p>
                            </li>
                        @endforeach
                    </ul>

                    <dl class="mt-4 space-y-2 border-t border-line pt-4 text-sm">
                        <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd>{{ rupiah($order->subtotal) }}</dd></div>
                        @if ((float) $order->discount > 0)
                            <div class="flex justify-between"><dt class="text-muted">Diskon</dt><dd class="text-emerald-600">- {{ rupiah($order->discount) }}</dd></div>
                        @endif
                        <div class="flex justify-between"><dt class="text-muted">Pengiriman</dt><dd>Gratis</dd></div>
                        <div class="flex justify-between border-t border-line pt-2 font-semibold"><dt>Total</dt><dd class="text-brand">{{ $order->formatted_total }}</dd></div>
                    </dl>

                    @if (! $order->isPaid())
                        <a href="{{ route('payment.show', $order) }}" class="btn-primary mt-5">Selesaikan Pembayaran</a>
                    @endif
                </div>

                <div class="rounded-lg border border-line p-6 text-sm">
                    <h2 class="font-display text-base font-semibold text-ink">Alamat Pengiriman</h2>
                    <p class="mt-2 leading-6 text-muted">
                        {{ trim($order->billing_first_name.' '.$order->billing_last_name) }}<br>
                        {{ $order->billing_address_1 }}{{ $order->billing_address_2 ? ', '.$order->billing_address_2 : '' }}<br>
                        {{ $order->billing_city }}{{ $order->billing_state ? ', '.$order->billing_state : '' }} {{ $order->billing_postcode }}<br>
                        {{ $order->billing_phone }} &middot; {{ $order->billing_email }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
