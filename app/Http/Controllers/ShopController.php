<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request, 'Semua Produk');
    }

    public function category(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        return $this->listing($request, $category->name, function (Builder $query) use ($category): void {
            $query->where('category_id', $category->id);
        }, $category);
    }

    public function tag(Request $request, Tag $tag): View
    {
        return $this->listing($request, 'Tag: '.$tag->name, function (Builder $query) use ($tag): void {
            $query->whereHas('tags', fn (Builder $q) => $q->where('tags.id', $tag->id));
        });
    }

    public function search(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));
        $categorySlug = (string) $request->query('category', '');

        return $this->listing($request, $keyword !== '' ? 'Hasil pencarian: '.$keyword : 'Pencarian', function (Builder $query) use ($keyword, $categorySlug): void {
            if ($keyword !== '') {
                $query->where(function (Builder $q) use ($keyword): void {
                    $q->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('description', 'like', '%'.$keyword.'%')
                        ->orWhere('sku', 'like', '%'.$keyword.'%');
                });
            }

            if ($categorySlug !== '') {
                $query->whereHas('category', fn (Builder $q) => $q->where('slug', $categorySlug));
            }
        });
    }

    public function suggest(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->query('q', ''));

        if (strlen($keyword) < 2) {
            return response()->json(['results' => []]);
        }

        $products = Product::query()
            ->active()
            ->with('images')
            ->where(function (Builder $query) use ($keyword): void {
                $query->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('description', 'like', '%'.$keyword.'%');
            })
            ->orderBy('name')
            ->take(6)
            ->get();

        return response()->json([
            'results' => $products->map(fn (Product $product): array => [
                'name' => $product->name,
                'url' => $product->url,
                'price' => $product->formatted_price,
                'image' => $product->image_url,
            ])->all(),
        ]);
    }

    private function listing(Request $request, string $title, ?callable $filter = null, ?Category $activeCategory = null): View
    {
        $sort = (string) $request->query('sort', 'name');
        $keyword = trim((string) $request->query('q', ''));

        $query = Product::query()->active()->with('images', 'category');

        if ($filter) {
            $filter($query);
        }

        if ($keyword !== '' && ! $request->routeIs('search')) {
            $query->where(function (Builder $q) use ($keyword): void {
                $q->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('description', 'like', '%'.$keyword.'%');
            });
        }

        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderBy('name'),
        };

        $products = $query->get();
        $categories = Category::query()->active()->ordered()->get();

        return view('shop.index', compact('products', 'categories', 'title', 'sort', 'keyword', 'activeCategory'));
    }
}
