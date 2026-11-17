@extends('layouts.app')

@section('title', 'Login - '.setting('site_name', 'Lab Supply'))

@section('content')
    <div class="container-x py-14 lg:py-20">
        <div class="mx-auto max-w-md rounded-lg border border-line p-7">
            <h1 class="font-display text-2xl font-semibold text-ink">Login</h1>
            <p class="mt-2 text-sm text-muted">Masuk untuk melihat riwayat pesanan dan wishlist Anda.</p>

            <form action="{{ route('login.store') }}" method="post" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_redirect" value="{{ request('redirect') }}">
                <div>
                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" class="field" required autofocus value="{{ old('email') }}">
                </div>
                <div>
                    <label class="label" for="password">Password</label>
                    <input id="password" type="password" name="password" class="field" required>
                </div>
                <label class="flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remember" class="rounded border-line text-brand focus:ring-brand">
                    Ingat saya
                </label>

                <button type="submit" class="btn-primary !w-full">Login</button>
            </form>

            <p class="mt-5 text-center text-sm text-muted">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-brand hover:underline">Register</a>
            </p>
        </div>
    </div>
@endsection
