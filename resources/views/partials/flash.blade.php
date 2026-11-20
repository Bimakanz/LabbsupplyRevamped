@if (! request()->routeIs('payment.success') && (session('success') || session('error') || session('status')))
    <div class="pointer-events-none fixed inset-x-0 top-24 z-[70] flex justify-center px-4">
        @php
            $type = session('error') ? 'error' : 'success';
            $message = session('success') ?? session('error') ?? session('status');
        @endphp
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
             class="pointer-events-auto flex max-w-lg items-center gap-3 rounded-2xl border px-6 py-3.5 text-sm shadow-[0_15px_35px_-5px_rgba(0,0,0,0.12)] backdrop-blur-md
                {{ $type === 'error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200/90 bg-white/95 text-slate-800' }}">
            @if ($type === 'error')
                <span class="font-bold text-red-600">!</span>
            @endif
            <span class="font-normal leading-relaxed">{{ $message }}</span>
            <button type="button" class="ml-2 text-slate-400 hover:text-slate-700 text-base leading-none transition" @click="show = false" aria-label="Tutup">&times;</button>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="pointer-events-none fixed inset-x-0 top-24 z-[70] flex justify-center px-4">
        <div x-data="{ show: true }" x-show="show" x-transition.opacity
             class="pointer-events-auto max-w-lg rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 text-sm text-red-700 shadow-lg">
            <p class="font-semibold">Periksa kembali data berikut:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="mt-2 text-xs underline" @click="show = false">Tutup</button>
        </div>
    </div>
@endif
