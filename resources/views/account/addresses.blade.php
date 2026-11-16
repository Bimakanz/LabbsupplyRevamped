@extends('layouts.app')

@section('title', 'Alamat - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'My Account', 'url' => route('account.dashboard')], ['label' => 'Alamat', 'url' => null]]])

    <div class="container-x py-10 lg:py-14">
        <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
            @include('partials.account-sidebar')

            <div class="rounded-lg border border-line p-6">
                <h1 class="font-display text-xl font-semibold text-ink">Alamat</h1>
                <p class="mt-1 text-sm text-muted">Alamat akan otomatis terisi saat Anda checkout menggunakan akun ini.</p>

                <div class="mt-5 rounded-lg border border-line bg-mist p-5 text-sm leading-6 text-muted">
                    <p class="font-medium text-ink">{{ auth()->user()->name }}</p>
                    <p>{{ auth()->user()->company ?: 'Belum ada perusahaan' }}</p>
                    <p>{{ auth()->user()->phone ?: 'Belum ada nomor telepon' }}</p>
                    <p>{{ auth()->user()->email }}</p>
                </div>

                <p class="mt-4 text-xs leading-6 text-muted">
                    Untuk mengubah alamat pengiriman utama, silakan update profil Anda atau hubungi tim kami melalui
                    <a href="{{ wa_link() }}" target="_blank" rel="noopener" class="text-brand underline">WhatsApp</a>.
                </p>

                <a href="{{ route('account.dashboard') }}" class="btn-outline mt-5 !py-2 !text-xs">Edit Profil</a>
            </div>
        </div>
    </div>
@endsection
