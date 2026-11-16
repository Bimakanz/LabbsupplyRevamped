@extends('layouts.app')

@section('title', 'Register - '.setting('site_name', 'Lab Supply'))

@section('content')
    <div class="container-x py-14 lg:py-20">
        <div class="mx-auto max-w-md rounded-lg border border-line p-7">
            <h1 class="font-display text-2xl font-semibold text-ink">Register</h1>
            <p class="mt-2 text-sm text-muted">Buat akun untuk pengalaman belanja yang lebih mudah.</p>

            <form action="{{ route('register.store') }}" method="post" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="label" for="name">Nama lengkap</label>
                    <input id="name" name="name" class="field" required value="{{ old('name') }}">
                </div>
                <div>
                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" class="field" required value="{{ old('email') }}">
                </div>
                <div>
                    <label class="label" for="phone">Telepon</label>
                    <input id="phone" name="phone" class="field" value="{{ old('phone') }}">
                </div>
                <div>
                    <label class="label" for="company">Perusahaan / Institusi</label>
                    <input id="company" name="company" class="field" value="{{ old('company') }}">
                </div>
                <div>
                    <label class="label" for="password">Password</label>
                    <input id="password" type="password" name="password" class="field" required>
                </div>
                <div>
                    <label class="label" for="password_confirmation">Konfirmasi password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="field" required>
                </div>

                <button type="submit" class="btn-primary !w-full">Buat Akun</button>
            </form>

            <p class="mt-5 text-center text-sm text-muted">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-brand hover:underline">Login</a>
            </p>
        </div>
    </div>
@endsection
