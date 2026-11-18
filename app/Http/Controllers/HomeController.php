<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return $this->render((string) setting('active_home', 'home-08'));
    }

    public function variant(string $variant): View
    {
        $name = 'home.home-'.$variant;
        abort_unless(view()->exists($name), 404);

        return $this->render('home-'.$variant);
    }

    private function render(string $variant): View
    {
        $newProducts = Product::query()
            ->active()
            ->with('images', 'category')
            ->orderBy('name')
            ->take(32)
            ->get();

        $featured = Product::query()
            ->active()
            ->with('images', 'category')
            ->orderByDesc('price')
            ->take(8)
            ->get();

        $bestSellers = Product::query()
            ->active()
            ->with('images', 'category')
            ->orderByDesc('views')
            ->take(8)
            ->get();

        $categories = Category::query()->active()->ordered()->get();

        $banner = Banner::query()
            ->where('is_active', true)
            ->where('position', 'home')
            ->orderBy('sort_order')
            ->first();

        return view('home.'.$variant, compact('newProducts', 'featured', 'bestSellers', 'categories', 'variant', 'banner'));
    }
}
