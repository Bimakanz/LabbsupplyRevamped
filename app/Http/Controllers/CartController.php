<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        $items = $cart->content();
        $suggestions = Product::query()->active()->with('images', 'category')->inRandomOrder()->take(4)->get();

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $cart->subtotal(),
            'total' => $cart->total(),
            'suggestions' => $suggestions,
        ]);
    }

    public function add(Request $request, CartService $cart): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $cart->add((int) $data['product_id'], (int) ($data['quantity'] ?? 1));

        return $this->cartResponse($request, $cart, 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartService $cart): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $cart->update((int) $data['product_id'], (int) $data['quantity']);

        return $this->cartResponse($request, $cart, 'Keranjang diperbarui.');
    }

    public function remove(Request $request, CartService $cart): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        $cart->remove((int) $data['product_id']);

        return $this->cartResponse($request, $cart, 'Produk dihapus dari keranjang.');
    }

    public function mini(CartService $cart): View
    {
        return view('partials.mini-cart', [
            'items' => $cart->content(),
            'subtotal' => $cart->subtotal(),
            'total' => $cart->total(),
        ]);
    }

    private function cartResponse(Request $request, CartService $cart, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'count' => $cart->count(),
                'subtotal' => rupiah($cart->subtotal()),
                'total' => rupiah($cart->total()),
                'mini' => view('partials.mini-cart', [
                    'items' => $cart->content(),
                    'subtotal' => $cart->subtotal(),
                    'total' => $cart->total(),
                ])->render(),
            ]);
        }

        return back()->with('success', $message);
    }
}
