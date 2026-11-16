<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('rupiah')) {
    function rupiah(float|int|string|null $amount): string
    {
        return 'Rp'.number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('wa_link')) {
    function wa_link(?string $message = null): string
    {
        $number = preg_replace('/[^0-9]/', '', (string) setting('whatsapp', '628111174992'));
        $text = $message ?? (string) setting('whatsapp_message', 'Halo Lab Supply, saya ingin bertanya.');

        return 'https://wa.me/'.$number.'?text='.rawurlencode($text);
    }
}

if (! function_exists('product_image')) {
    function product_image(?string $path): string
    {
        if (blank($path)) {
            return asset('theme/img/logo.webp');
        }

        return str_starts_with($path, 'http') ? $path : asset('storage/'.$path);
    }
}
