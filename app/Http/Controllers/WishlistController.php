<?php

namespace App\Http\Controllers;

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
        $added = $wishlist->toggle((int) $data['product_id']);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'added' => $added,
                'count' => $wishlist->count(),
                'message' => $added ? 'Produk ditambahkan ke wishlist.' : 'Produk dihapus dari wishlist.',
            ]);
        }

        return back()->with('success', $added ? 'Produk ditambahkan ke wishlist.' : 'Produk dihapus dari wishlist.');
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
