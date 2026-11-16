<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Request $request, Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'category', 'tags', 'approvedReviews']);
        $product->increment('views');

        $related = Product::query()
            ->active()
            ->with('images', 'category')
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->orderBy('name')
            ->take(4)
            ->get();

        $frequentlyBought = Product::query()
            ->active()
            ->with('images', 'category')
            ->whereKeyNot($product->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        $recentIds = collect(session()->get('recently_viewed', []))
            ->reject(fn ($id): bool => (int) $id === $product->id)
            ->take(4)
            ->all();

        $recentlyViewed = Product::query()
            ->active()
            ->with('images', 'category')
            ->whereIn('id', $recentIds)
            ->get();

        session()->put('recently_viewed', collect([$product->id])
            ->merge(session()->get('recently_viewed', []))
            ->unique()
            ->take(8)
            ->values()
            ->all());

        return view('product.show', compact('product', 'related', 'frequentlyBought', 'recentlyViewed'));
    }

    public function quickView(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'category']);

        return view('product.quick-view', compact('product'));
    }
}
