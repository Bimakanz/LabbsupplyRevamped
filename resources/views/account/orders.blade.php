@extends('layouts.app')

@section('title', 'Pesanan Saya - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'My Account', 'url' => route('account.dashboard')], ['label' => 'Pesanan', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
            @include('partials.account-sidebar')

            <div class="rounded-lg border border-line p-6">
                <h1 class="font-display text-xl font-semibold text-ink">Pesanan Saya</h1>

                @if ($orders->isEmpty())
                    <p class="mt-4 text-sm text-muted">Belum ada pesanan. <a href="{{ route('shop') }}" class="text-brand underline">Mulai belanja</a>.</p>
                @else
                    <div class="mt-5 divide-y divide-line">
                        @foreach ($orders as $order)
                            <div class="flex flex-wrap items-center justify-between gap-3 py-4">
                                <div>
                                    <a href="{{ route('account.order', $order) }}" class="font-medium text-ink hover:text-brand">{{ $order->number }}</a>
                                    <p class="mt-0.5 text-xs text-muted">{{ $order->created_at->format('d M Y H:i') }} &middot; {{ $order->items->count() }} item</p>
                                </div>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="rounded-full px-2.5 py-1 text-xs {{ $order->isPaid() ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $order->status_label }}</span>
                                    <span class="font-medium text-brand">{{ $order->formatted_total }}</span>
                                    <a href="{{ route('account.order', $order) }}" class="text-xs text-brand hover:underline">Detail</a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">{{ $orders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
