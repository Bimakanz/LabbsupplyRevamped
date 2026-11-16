@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan - '.setting('site_name', 'Lab Supply'))

@section('content')
    <div class="container-x grid min-h-[52vh] place-items-center py-16 text-center">
        <div>
            <p class="font-display text-[64px] font-bold leading-none text-brand">404</p>
            <h1 class="mt-3 font-display text-2xl font-semibold text-ink">Halaman tidak ditemukan</h1>
            <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-muted">
                Halaman yang Anda cari mungkin sudah dipindahkan atau tidak tersedia lagi.
            </p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="btn-primary">Kembali ke Home</a>
                <a href="{{ route('shop') }}" class="btn-outline">Lihat Semua Produk</a>
            </div>
        </div>
    </div>
@endsection
