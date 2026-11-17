<?php

namespace App\Services;

use App\Models\Order;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaymentService
{
    public function isDemo(): bool
    {
        return config('services.mayar.mode', 'demo') === 'demo' || blank(config('services.mayar.api_key'));
    }

    /**
     * @return array{reference: string|null, link: string|null, qr: string|null, payload: array<string, mixed>}
     */
    public function createInvoice(Order $order): array
    {
        if ($this->isDemo()) {
            $link = route('payment.show', $order);

            return [
                'reference' => 'DEMO-'.$order->number,
                'link' => $link,
                'qr' => $this->qrSvg($link),
                'payload' => ['mode' => 'demo', 'created_at' => now()->toIso8601String()],
            ];
        }

        $payload = [
            'name' => trim($order->billing_first_name.' '.$order->billing_last_name),
            'email' => $order->billing_email,
            'mobile' => $order->billing_phone,
            'description' => 'Pesanan '.$order->number.' - Lab Supply',
            'expiredAt' => now()->addDay()->toIso8601String(),
            'items' => $order->items->map(fn ($item): array => [
                'quantity' => $item->quantity,
                'rate' => (int) round($item->price),
                'description' => $item->name,
            ])->values()->all(),
            'extraData' => ['order_number' => $order->number],
        ];

        $response = Http::withToken((string) config('services.mayar.api_key'))
            ->acceptJson()
            ->timeout(30)
            ->post(rtrim((string) config('services.mayar.base_url'), '/').'/invoices/create', $payload);

        if ($response->failed()) {
            throw new RuntimeException('Gagal membuat invoice Mayar: '.$response->body());
        }

        $data = $response->json('data', []);
        $link = $data['link'] ?? null;

        return [
            'reference' => $data['id'] ?? null,
            'link' => $link,
            'qr' => $link ? $this->qrSvg($link) : null,
            'payload' => $data,
        ];
    }

    public function qrSvg(string $data): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => QRCode::ECC_L,
            'svgUseFillAttributes' => true,
            'addQuietzone' => true,
        ]);

        return (new QRCode($options))->render($data);
    }

    public function markPaid(Order $order, ?string $reference = null): void
    {
        if ($order->isPaid()) {
            return;
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'paid_at' => now(),
            'payment_reference' => $reference ?? $order->payment_reference,
        ]);
    }

    public function verify(Order $order): bool
    {
        if ($this->isDemo()) {
            return true;
        }

        if (blank($order->payment_reference)) {
            return false;
        }

        $response = Http::withToken((string) config('services.mayar.api_key'))
            ->acceptJson()
            ->timeout(30)
            ->get(rtrim((string) config('services.mayar.base_url'), '/').'/invoices/'.$order->payment_reference);

        if ($response->failed()) {
            return false;
        }

        $status = strtolower((string) ($response->json('data.status') ?? $response->json('data.paymentStatus') ?? ''));

        return in_array($status, ['paid', 'success', 'settled', 'completed'], true);
    }
}
