<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(WishlistService $wishlist): View
    {
        return view('wishlist.index', [
            'products' => $wishlist->products(),
            'ids' => $wishlist->ids(),
        ]);
    }

    public function toggle(Request $request, WishlistService $wishlist): JsonResponse|RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer', 'exists:products,id']]);
        $product = Product::find($data['product_id']);
        $added = $wishlist->toggle((int) $data['product_id']);
        $name = $product?->name ?? 'Produk';

        $message = $added
            ? '"'.$name.'" has been added to your "My wishlist" list!'
            : '"'.$name.'" has been removed from your wishlist.';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'added' => $added,
                'count' => $wishlist->count(),
                'product_name' => $name,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    public function remove(Request $request, WishlistService $wishlist): JsonResponse|RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer']]);
        $wishlist->remove((int) $data['product_id']);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'count' => $wishlist->count()]);
        }

        return back()->with('success', 'Produk dihapus dari wishlist.');
    }
}
