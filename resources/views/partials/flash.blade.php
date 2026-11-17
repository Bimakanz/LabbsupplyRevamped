@if (session('success') || session('error') || session('status'))
    <div class="pointer-events-none fixed inset-x-0 top-24 z-[70] flex justify-center px-4">
        @php
            $type = session('error') ? 'error' : 'success';
            $message = session('success') ?? session('error') ?? session('status');
        @endphp
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition.opacity
             class="pointer-events-auto flex max-w-lg items-start gap-3 rounded-lg border px-4 py-3 text-sm shadow-lg
                {{ $type === 'error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
            <span class="mt-0.5">{{ $type === 'error' ? '!' : '✓' }}</span>
            <span>{{ $message }}</span>
            <button type="button" class="ml-2 opacity-60 hover:opacity-100" @click="show = false" aria-label="Tutup">&times;</button>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="pointer-events-none fixed inset-x-0 top-24 z-[70] flex justify-center px-4">
        <div x-data="{ show: true }" x-show="show" x-transition.opacity
             class="pointer-events-auto max-w-lg rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-lg">
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
