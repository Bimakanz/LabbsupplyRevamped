@extends('layouts.app')

@section('title', $title ?? 'My Account - '.setting('site_name', 'Lab Supply'))

@section('content')
    @include('partials.breadcrumbs', ['items' => [['label' => 'My Account', 'url' => route('account.dashboard')]]])

    <div class="container-x py-10 lg:py-14">
        <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
            <aside class="h-fit rounded-lg border border-line p-5">
                <p class="font-display text-sm font-semibold text-ink">{{ auth()->user()->name }}</p>
                <p class="mt-0.5 truncate text-xs text-muted">{{ auth()->user()->email }}</p>

                <nav class="mt-5 flex flex-col gap-1 text-sm">
                    @foreach ([
                        ['account.dashboard', 'Dashboard'],
                        ['account.orders', 'Pesanan'],
                        ['wishlist.index', 'Wishlist'],
                        ['account.addresses', 'Alamat'],
                    ] as [$route, $label])
                        <a href="{{ route($route) }}"
                           class="rounded px-3 py-2 {{ request()->routeIs($route) ? 'bg-brand-soft font-medium text-brand' : 'text-muted hover:bg-mist hover:text-ink' }}">
                            {{ $label }}
                        </a>
                    @endforeach

                    <form action="{{ route('logout') }}" method="post" class="mt-2 border-t border-line pt-2">
                        @csrf
                        <button type="submit" class="w-full rounded px-3 py-2 text-left text-muted hover:bg-mist hover:text-red-600">Logout</button>
                    </form>
                </nav>
            </aside>

            <div>
                @yield('account')
            </div>
        </div>
    </div>
@endsection
