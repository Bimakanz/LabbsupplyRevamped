<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(CartService $cart): View|RedirectResponse
    {
        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        return view('checkout.index', [
            'items' => $cart->content(),
            'subtotal' => $cart->subtotal(),
            'discount' => $cart->discount(),
            'total' => $cart->total(),
            'coupon' => $cart->coupon(),
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request, CartService $cart, PaymentService $payment): RedirectResponse
    {
        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        $data = $request->validate([
            'billing_first_name' => ['required', 'string', 'max:100'],
            'billing_last_name' => ['required', 'string', 'max:100'],
            'billing_company' => ['nullable', 'string', 'max:150'],
            'billing_country' => ['required', 'string', 'max:5'],
            'billing_address_1' => ['required', 'string', 'max:255'],
            'billing_address_2' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['required', 'string', 'max:100'],
            'billing_state' => ['nullable', 'string', 'max:100'],
            'billing_postcode' => ['required', 'string', 'max:20'],
            'billing_phone' => ['required', 'string', 'max:30'],
            'billing_email' => ['required', 'email', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $items = $cart->content();

        $order = DB::transaction(function () use ($data, $items, $cart): Order {
            $order = Order::create($data + [
                'number' => 'LS-'.now()->format('ymd').'-'.strtoupper(Str::random(6)),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => 'mayar',
                'subtotal' => $cart->subtotal(),
                'discount' => $cart->discount(),
                'shipping' => 0,
                'total' => $cart->total(),
                'coupon_code' => $cart->coupon()?->code,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'name' => $item['product']->name,
                    'price' => $item['product']->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });

        try {
            $invoice = $payment->createInvoice($order->fresh('items'));

            $order->update([
                'payment_reference' => $invoice['reference'],
                'payment_url' => $invoice['link'],
                'payment_payload' => $invoice['payload'],
            ]);

            if ($invoice['qr']) {
                session()->put('payment.qr.'.$order->id, $invoice['qr']);
            }
        } catch (\Throwable $exception) {
            report($exception);
            $order->update(['payment_method' => 'manual']);
        }

        $cart->clear();

        return redirect()->route('payment.show', $order);
    }
}
