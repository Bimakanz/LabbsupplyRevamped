@extends('layouts.app')

@section('title', 'Order Tracking - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Order Tracking', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="mx-auto max-w-3xl">
            <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Order Tracking</h1>
            <p class="mt-3 text-sm leading-7 text-muted">Masukkan nomor pesanan Anda untuk melihat status terkini.</p>

            <form action="{{ route('order.track.post') }}" method="post" class="mt-6 grid gap-3 rounded-lg border border-line p-6 sm:grid-cols-[1fr_1fr_auto]">
                @csrf
                <div>
                    <label class="label" for="number">Nomor pesanan</label>
                    <input id="number" name="number" class="field" placeholder="LS-XXXXXX-XXXXXX" required value="{{ old('number', $order?->number) }}">
                </div>
                <div>
                    <label class="label" for="track_email">Email (opsional)</label>
                    <input id="track_email" type="email" name="email" class="field" value="{{ old('email') }}">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">Lacak</button>
                </div>
            </form>

            @if ($order)
                <div class="mt-8 rounded-lg border border-line">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line bg-mist px-6 py-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-muted">Nomor pesanan</p>
                            <p class="font-display text-lg font-semibold text-ink">{{ $order->number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs uppercase tracking-wide text-muted">Status</p>
                            <p class="font-medium text-brand">{{ $order->status_label }}</p>
                        </div>
                    </div>

                    <div class="px-6 py-5">
                        <ol class="grid gap-5 sm:grid-cols-4">
                            @foreach ([
                                ['pending', 'Pesanan dibuat', 'Menunggu pembayaran'],
                                ['processing', 'Diproses', 'Pembayaran diterima'],
                                ['completed', 'Selesai', 'Pesanan dikirim'],
                            ] as $step)
                                @php
                                    $states = ['pending' => 1, 'processing' => 2, 'completed' => 3, 'cancelled' => 0];
                                    $current = $states[$order->status] ?? 1;
                                    $index = $loop->index + 1;
                                    $active = $current >= $index;
                                @endphp
                                <li class="flex gap-3">
                                    <span class="mt-0.5 grid size-7 shrink-0 place-items-center rounded-full text-xs font-semibold {{ $active ? 'bg-brand text-white' : 'bg-mist text-muted' }}">{{ $index }}</span>
                                    <span>
                                        <span class="block text-sm font-medium {{ $active ? 'text-ink' : 'text-muted' }}">{{ $step[1] }}</span>
                                        <span class="block text-xs text-muted">{{ $step[2] }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="border-t border-line px-6 py-5">
                        <h2 class="font-display text-base font-semibold text-ink">Item Pesanan</h2>
                        <ul class="mt-3 divide-y divide-line">
                            @foreach ($order->items as $item)
                                <li class="flex items-center justify-between gap-3 py-2.5 text-sm">
                                    <span class="text-ink">{{ $item->name }} <span class="text-muted">&times; {{ $item->quantity }}</span></span>
                                    <span class="font-medium text-ink">{{ rupiah($item->subtotal) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-3 flex justify-between border-t border-line pt-3 text-sm font-semibold">
                            <span>Total</span>
                            <span class="text-brand">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
