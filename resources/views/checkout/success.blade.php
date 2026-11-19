@extends('layouts.app')

@section('title', 'Pembayaran Berhasil - '.setting('site_name', 'Lab Supply'))

@section('content')
    <div class="container-x py-14 lg:py-20" x-data="{ showInvoice: false }">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mx-auto grid size-20 place-items-center rounded-full bg-emerald-50 text-emerald-600">
                <svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h1 class="mt-6 font-display text-[28px] font-semibold tracking-tight text-ink lg:text-[34px]">Pembayaran Berhasil</h1>
            <p class="mt-3 text-sm leading-6 text-muted">
                Terima kasih! Pesanan <strong class="text-ink">{{ $order->number }}</strong> sudah kami terima dan sedang diproses.
                Konfirmasi rincian pesanan juga telah dikirimkan ke <strong class="text-ink">{{ $order->billing_email }}</strong>.
            </p>

            <div class="mt-8 rounded-lg border border-line bg-mist p-6 text-left">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-display text-base font-semibold text-ink">Ringkasan Pesanan</h2>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $order->status_label }}</span>
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
                    <span>Total Pembayaran</span>
                    <span class="text-brand">{{ $order->formatted_total }}</span>
                </div>
            </div>

            <!-- Tombol Aksi Utama -->
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <button type="button" @click="showInvoice = true" class="btn-primary inline-flex items-center gap-2 shadow-sm">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="14 2 14 8 20 8" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="13" x2="8" y2="13" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="17" x2="8" y2="17" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="10 9 9 9 8 9" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Buka Invoice Resmi</span>
                </button>
                <a href="{{ route('shop') }}" class="btn-outline">Lanjut Belanja</a>
                <a href="{{ route('order.track') }}" class="btn-outline">Lacak Pesanan</a>
                <a href="{{ wa_link('Halo, saya ingin menanyakan pesanan '.$order->number) }}" target="_blank" rel="noopener" class="btn-outline">Hubungi Kami</a>
            </div>
        </div>

        <!-- MODAL INVOICE ELEKTRONIK -->
        <div x-show="showInvoice"
             x-cloak
             @keydown.escape.window="showInvoice = false"
             class="fixed inset-0 z-[110] flex items-center justify-center p-3 sm:p-5"
             role="dialog"
             aria-modal="true">
            <!-- Backdrop -->
            <div x-show="showInvoice"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                 @click="showInvoice = false"></div>

            <!-- Invoice Document Container -->
            <div x-show="showInvoice"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                 class="relative z-10 max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl border border-slate-200 print:shadow-none print:border-none print:max-h-none print:w-full">

                <!-- Header Bar Modal (Tombol Tutup & Cetak) -->
                <div class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-slate-50/90 px-6 py-3.5 backdrop-blur print:hidden">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pratinjau Faktur Penjualan</span>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="window.print()" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:bg-slate-50">
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                <rect x="6" y="14" width="12" height="8"></rect>
                            </svg>
                            Cetak / PDF
                        </button>
                        <button type="button" @click="showInvoice = false" class="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-200 hover:text-slate-700" aria-label="Tutup">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Lembar Faktur / Invoice Paper -->
                <div class="p-6 sm:p-10 text-slate-800" id="printable-invoice">
                    <!-- Kop Perusahaan & Judul Invoice -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 border-b border-slate-200 pb-8">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span class="font-display text-2xl font-black tracking-tight text-brand">LABSUPPLY</span>
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-600">Official</span>
                            </div>
                            <p class="mt-2 text-xs font-semibold text-slate-900">PT Lichemindo Adhiraya</p>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Kawasan Niaga Tajur No. 128, Bogor Selatan<br>
                                Kota Bogor, Jawa Barat 16141<br>
                                Telp: +62 811-1174-992 &bull; Email: billing@labsupply.id
                            </p>
                        </div>

                        <div class="text-left sm:text-right">
                            <h3 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-slate-900">INVOICE</h3>
                            <p class="mt-1 font-mono text-xs font-medium text-slate-600">INV/{{ ($order->paid_at ?? $order->created_at)->format('Ymd') }}/{{ $order->number }}</p>
                            <div class="mt-2.5 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 border border-emerald-200 text-xs font-bold text-emerald-700">
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                LUNAS / PAID
                            </div>
                        </div>
                    </div>

                    <!-- Meta Data Invoice & Customer Info -->
                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 text-xs">
                        <div class="rounded-lg bg-slate-50 p-4 border border-slate-100">
                            <p class="font-semibold uppercase tracking-wider text-slate-500">Ditagihkan Kepada (Bill To):</p>
                            <p class="mt-2 text-sm font-bold text-slate-900">{{ $order->billing_first_name }} {{ $order->billing_last_name }}</p>
                            @if ($order->billing_company)
                                <p class="font-medium text-slate-700">{{ $order->billing_company }}</p>
                            @endif
                            <p class="mt-1 text-slate-600 leading-relaxed">
                                {{ $order->billing_address_1 }}{{ $order->billing_address_2 ? ', '.$order->billing_address_2 : '' }}<br>
                                {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postcode }}
                            </p>
                            <p class="mt-2 text-slate-600">
                                <span class="text-slate-500">Telp:</span> {{ $order->billing_phone }}<br>
                                <span class="text-slate-500">Email:</span> {{ $order->billing_email }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-slate-50 p-4 border border-slate-100 space-y-2">
                            <p class="font-semibold uppercase tracking-wider text-slate-500">Informasi Transaksi:</p>
                            <div class="flex justify-between border-b border-slate-200/60 pb-1.5">
                                <span class="text-slate-500">No. Pesanan:</span>
                                <span class="font-mono font-medium text-slate-900">{{ $order->number }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200/60 pb-1.5">
                                <span class="text-slate-500">Tanggal Terbit:</span>
                                <span class="font-medium text-slate-900">{{ ($order->paid_at ?? $order->created_at)->format('d F Y, H:i') }} WIB</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200/60 pb-1.5">
                                <span class="text-slate-500">Metode Bayar:</span>
                                <span class="font-semibold text-slate-900">{{ strtoupper($order->payment_method ?? 'QRIS Instant') }}</span>
                            </div>
                            <div class="flex justify-between pt-0.5">
                                <span class="text-slate-500">Status Transaksi:</span>
                                <span class="font-semibold text-emerald-600">Terverifikasi Otomatis</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Item Produk -->
                    <div class="mt-8 overflow-hidden rounded-lg border border-slate-200">
                        <table class="w-full border-collapse text-left text-xs">
                            <thead>
                                <tr class="bg-slate-100/80 text-slate-700 font-semibold border-b border-slate-200 uppercase tracking-wider">
                                    <th class="py-3 px-4 w-12 text-center">No</th>
                                    <th class="py-3 px-4">Deskripsi Produk</th>
                                    <th class="py-3 px-4 text-center">SKU</th>
                                    <th class="py-3 px-4 text-center w-16">Qty</th>
                                    <th class="py-3 px-4 text-right">Harga Satuan</th>
                                    <th class="py-3 px-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($order->items as $index => $item)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-3.5 px-4 text-center text-slate-500">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-4 font-medium text-slate-900">
                                            {{ $item->name }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-mono text-slate-500 text-[11px]">
                                            {{ $item->product?->sku ?? '-' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-semibold text-slate-900">{{ $item->quantity }}</td>
                                        <td class="py-3.5 px-4 text-right text-slate-600">{{ rupiah($item->price) }}</td>
                                        <td class="py-3.5 px-4 text-right font-semibold text-slate-900">{{ rupiah($item->subtotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Perhitungan Total -->
                    <div class="mt-6 flex flex-col sm:flex-row justify-between gap-6 pt-2">
                        <div class="max-w-xs text-xs text-slate-500 leading-relaxed">
                            <p class="font-semibold text-slate-700 mb-1">Catatan Dokumen:</p>
                            <p>Faktur ini diterbitkan secara otomatis dan sah sebagai bukti transaksi yang sah oleh PT Lichemindo Adhiraya.</p>
                            <div class="mt-3 inline-block rounded border border-emerald-300 bg-emerald-50 px-3 py-1 font-mono text-[11px] font-bold text-emerald-800">
                                DIGITAL SEAL VERIFIED
                            </div>
                        </div>

                        <div class="w-full sm:w-64 space-y-2 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal Produk:</span>
                                <span class="font-medium text-slate-900">{{ rupiah($order->subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Ongkos Kirim:</span>
                                <span class="font-medium text-slate-900">
                                    {{ (float) $order->shipping > 0 ? rupiah($order->shipping) : 'Rp 0 (Gratis)' }}
                                </span>
                            </div>
                            <div class="flex justify-between border-t-2 border-slate-900 pt-2 text-sm font-bold text-slate-900">
                                <span>Total Pembayaran:</span>
                                <span class="text-brand">{{ $order->formatted_total }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Invoice -->
                    <div class="mt-10 border-t border-slate-200 pt-4 text-center text-[11px] text-slate-400">
                        Terima kasih telah mempercayakan kebutuhan laboratorium Anda kepada LabSupply &bull; www.labsupply.id
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
