@props([
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => 'Pilih...',
    'required' => false,
    'submit' => false,
])

@php
    $normalized = collect($options)
        ->map(fn ($option) => is_array($option) ? $option : ['value' => $option, 'label' => $option])
        ->values()
        ->all();

    $selectedValue = old($name, $value) ?? '';
@endphp

<div x-data="customSelect({
        value: @js((string) $selectedValue),
        options: @js($normalized),
        placeholder: @js($placeholder),
        submit: @js((bool) $submit),
     })"
     class="relative" @keydown.escape="close()">
    <select name="{{ $name }}" x-model="value" class="sr-only" tabindex="-1" @if ($required) required @endif aria-hidden="true">
        <option value="">{{ $placeholder }}</option>
        @foreach ($normalized as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
        @endforeach
    </select>

    <button type="button" @click="toggle()" :aria-expanded="open"
            {{ $attributes->merge(['class' => 'field flex items-center justify-between gap-2 text-left']) }}>
        <span class="truncate" :class="! selected && 'text-muted/80'" x-text="label"></span>
        <svg class="size-4 shrink-0 text-muted transition-transform duration-200" :class="open && 'rotate-180'"
             viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition.origin.top.duration.150ms
         class="absolute left-0 right-0 z-40 mt-1.5 max-h-64 overflow-y-auto rounded-lg border border-line bg-white py-1.5 shadow-xl">
        <template x-for="option in options" :key="option.value">
            <button type="button" @click="choose(option)"
                    class="flex w-full items-center justify-between gap-3 px-4 py-2 text-left text-sm transition"
                    :class="String(option.value) === String(value) ? 'bg-brand-soft font-medium text-brand' : 'text-ink hover:bg-mist'"
                    :data-option-active="String(option.value) === String(value)">
                <span class="truncate" x-text="option.label"></span>
                <svg x-show="String(option.value) === String(value)" class="size-4 shrink-0" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </template>

        <p x-show="options.length === 0" class="px-4 py-2 text-sm text-muted">Tidak ada pilihan.</p>
    </div>
</div>
