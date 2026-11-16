@extends('layouts.app')

@section('title', 'Checkout - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'Cart', 'url' => route('cart.index')], ['label' => 'Checkout', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <h1 class="font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Checkout</h1>
        <p class="mt-2 text-sm text-muted">Lengkapi data pengiriman dan pilih metode pembayaran.</p>

        <form action="{{ route('checkout.store') }}" method="post" class="mt-8 grid gap-10 lg:grid-cols-[1fr_380px]">
            @csrf

            <div class="space-y-8">
                <section class="rounded-lg border border-line p-6">
                    <h2 class="font-display text-lg font-semibold text-ink">Billing details</h2>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label" for="billing_first_name">First name <span class="text-red-500">*</span></label>
                            <input id="billing_first_name" name="billing_first_name" class="field" required
                                   value="{{ old('billing_first_name', $user?->name ? explode(' ', $user->name)[0] : '') }}">
                        </div>
                        <div>
                            <label class="label" for="billing_last_name">Last name <span class="text-red-500">*</span></label>
                            <input id="billing_last_name" name="billing_last_name" class="field" required
                                   value="{{ old('billing_last_name', $user?->name ? (explode(' ', $user->name, 2)[1] ?? '') : '') }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="billing_company">Company name (optional)</label>
                            <input id="billing_company" name="billing_company" class="field" value="{{ old('billing_company', $user?->company) }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="billing_country">Country / Region <span class="text-red-500">*</span></label>
                            <select id="billing_country" name="billing_country" class="field" required>
                                <option value="ID" @selected(old('billing_country', 'ID') === 'ID')>Indonesia</option>
                                <option value="SG" @selected(old('billing_country') === 'SG')>Singapore</option>
                                <option value="MY" @selected(old('billing_country') === 'MY')>Malaysia</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="billing_address_1">Street address <span class="text-red-500">*</span></label>
                            <input id="billing_address_1" name="billing_address_1" class="field" placeholder="Nama jalan dan nomor rumah" required value="{{ old('billing_address_1') }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="billing_address_2">Apartment, suite, unit (optional)</label>
                            <input id="billing_address_2" name="billing_address_2" class="field" value="{{ old('billing_address_2') }}">
                        </div>
                        <div>
                            <label class="label" for="billing_city">City <span class="text-red-500">*</span></label>
                            <input id="billing_city" name="billing_city" class="field" required value="{{ old('billing_city') }}">
                        </div>
                        <div>
                            <label class="label" for="billing_state">Province / State</label>
                            <input id="billing_state" name="billing_state" class="field" value="{{ old('billing_state') }}">
                        </div>
                        <div>
                            <label class="label" for="billing_postcode">Postcode / ZIP <span class="text-red-500">*</span></label>
                            <input id="billing_postcode" name="billing_postcode" class="field" required value="{{ old('billing_postcode') }}">
                        </div>
                        <div>
                            <label class="label" for="billing_phone">Phone <span class="text-red-500">*</span></label>
                            <input id="billing_phone" name="billing_phone" class="field" required value="{{ old('billing_phone', $user?->phone) }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="billing_email">Email address <span class="text-red-500">*</span></label>
                            <input id="billing_email" type="email" name="billing_email" class="field" required value="{{ old('billing_email', $user?->email) }}">
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border border-line p-6">
                    <h2 class="font-display text-lg font-semibold text-ink">Catatan pesanan (optional)</h2>
                    <textarea name="notes" rows="4" class="field mt-4" placeholder="Catatan untuk pesanan Anda">{{ old('notes') }}</textarea>
                </section>

                <section class="rounded-lg border border-line p-6">
                    <h2 class="font-display text-lg font-semibold text-ink">Pembayaran</h2>
                    <p class="mt-1 text-sm text-muted">Semua transaksi diproses secara aman melalui Mayar.</p>

                    <div class="mt-4 divide-y divide-line rounded-lg border border-line">
                        <label class="flex cursor-pointer items-start gap-3 p-4">
                            <input type="radio" name="payment_method_ui" value="mayar" checked class="mt-1 accent-[#4981d4]">
                            <span>
                                <span class="block text-sm font-medium text-ink">Mayar — QRIS / Virtual Account</span>
                                <span class="mt-0.5 block text-xs leading-5 text-muted">Pindai kode QR atau transfer ke nomor virtual account. Pesanan diproses otomatis setelah pembayaran diterima.</span>
                            </span>
                        </label>
                    </div>

                    <div class="mt-4 rounded-lg bg-mist p-4 text-xs leading-6 text-muted">
                        Dengan menekan tombol <strong class="text-ink">Place order</strong>, Anda menyetujui
                        <a href="{{ url('terms-and-conditions') }}" class="text-brand underline">Terms &amp; Conditions</a> dan
                        <a href="{{ url('privacy-policy') }}" class="text-brand underline">Privacy Policy</a> Lab Supply.
                    </div>
                </section>
            </div>

            <aside class="h-fit rounded-lg border border-line bg-mist p-6 lg:sticky lg:top-6">
                <h2 class="font-display text-lg font-semibold text-ink">Your order</h2>

                <ul class="mt-4 divide-y divide-line/80">
                    @foreach ($items as $item)
                        <li class="flex gap-3 py-3">
                            <div class="relative size-14 shrink-0 overflow-hidden rounded border border-line bg-white">
                                <img src="{{ $item['product']->image_url }}" alt="" class="size-full object-contain p-1">
                                <span class="absolute -right-1 -top-1 grid size-5 place-items-center rounded-full bg-brand text-[10px] font-semibold text-white">{{ $item['quantity'] }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="line-clamp-2 text-[13px] font-medium leading-5 text-ink">{{ $item['product']->name }}</p>
                                <p class="mt-1 text-xs text-muted">{{ rupiah($item['subtotal']) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <dl class="mt-4 space-y-3 border-t border-line pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd class="font-medium text-ink">{{ rupiah($subtotal) }}</dd></div>
                    @if ($discount > 0)
                        <div class="flex justify-between"><dt class="text-muted">Diskon {{ $coupon?->code ? '('.$coupon->code.')' : '' }}</dt><dd class="font-medium text-emerald-600">- {{ rupiah($discount) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-muted">Shipping</dt><dd class="font-medium text-ink">Free delivery</dd></div>
                    <div class="flex justify-between border-t border-line pt-3 text-base"><dt class="font-semibold text-ink">Total</dt><dd class="font-display font-semibold text-brand">{{ rupiah($total) }}</dd></div>
                </dl>

                <button type="submit" class="btn-primary mt-6 !w-full">Place order</button>

                <p class="mt-3 text-center text-[11px] leading-5 text-muted">
                    Data Anda aman dan tidak dibagikan ke pihak lain.
                </p>
            </aside>
        </form>
    </div>
@endsection

@push('scripts')
    @include('partials.shop-scripts')
@endpush
