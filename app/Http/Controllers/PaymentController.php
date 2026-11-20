<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request, Order $order): View|RedirectResponse
    {
        if ($order->isPaid()) {
            return redirect()->route('payment.success', $order);
        }

        $order->load('items');
        $qr = session()->get('payment.qr.'.$order->id);

        if (! $qr && $order->payment_url) {
            $qr = app(PaymentService::class)->qrSvg($order->payment_url);
        }

        return view('checkout.payment', compact('order', 'qr'));
    }

    public function confirm(Request $request, Order $order, PaymentService $payment): RedirectResponse
    {
        if (! $order->isPaid()) {
            if (! $payment->verify($order)) {
                return back()->with('error', 'Pembayaran belum terdeteksi. Silakan coba lagi.');
            }

            $payment->markPaid($order);
            $this->decreaseStock($order);
        }

        return redirect()->route('payment.success', $order);
    }

    public function success(Order $order): View
    {
        $order->load('items');

        return view('checkout.success', compact('order'));
    }

    public function webhook(Request $request, PaymentService $payment): JsonResponse
    {
        $payload = $request->all();
        $orderNumber = $payload['extraData']['order_number']
            ?? $payload['data']['extraData']['order_number']
            ?? null;

        $order = $orderNumber
            ? Order::query()->where('number', $orderNumber)->first()
            : null;

        if (! $order && ! empty($payload['id'])) {
            $order = Order::query()->where('payment_reference', $payload['id'])->first();
        }

        if (! $order) {
            return response()->json(['ok' => false, 'message' => 'Order not found'], 404);
        }

        $status = strtolower((string) ($payload['status'] ?? $payload['data']['status'] ?? ''));

        if (in_array($status, ['paid', 'success', 'settled', 'completed'], true)) {
            $payment->markPaid($order, $payload['id'] ?? null);
            $this->decreaseStock($order);
        }

        return response()->json(['ok' => true]);
    }

    private function decreaseStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_id === null) {
                continue;
            }

            $product = Product::query()->find($item->product_id);

            if (! $product) {
                continue;
            }

            $product->stock = max(0, $product->stock - $item->quantity);
            $product->stock_status = $product->stock > 0 ? 'in_stock' : 'out_of_stock';
            $product->save();
        }
    }
}
