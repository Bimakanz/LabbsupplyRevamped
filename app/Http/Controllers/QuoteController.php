<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QuoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class QuoteController extends Controller
{
    private const KEY = 'quote.items';

    public function index(): View
    {
        return view('quote.index', [
            'items' => $this->items(),
            'products' => Product::query()->active()->with('images')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:150'],
            'message' => ['nullable', 'string', 'max:3000'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $quote = QuoteRequest::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'company' => $data['company'] ?? null,
            'message' => $data['message'] ?? null,
            'status' => 'new',
        ]);

        $rows = $data['items'] ?? [];

        foreach ($rows as $row) {
            $product = Product::query()->find($row['product_id']);

            if (! $product) {
                continue;
            }

            $quote->items()->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => (int) $row['quantity'],
            ]);
        }

        session()->forget(self::KEY);

        return back()->with('success', 'Permintaan penawaran harga berhasil dikirim. Tim kami akan menghubungi Anda.');
    }

    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer', 'exists:products,id']]);
        $items = session()->get(self::KEY, []);
        $items[(int) $data['product_id']] = true;
        session()->put(self::KEY, $items);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Produk ditambahkan ke permintaan penawaran.']);
        }

        return redirect()->route('quote.index')->with('success', 'Produk ditambahkan ke permintaan penawaran.');
    }

    public function remove(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer']]);
        $items = session()->get(self::KEY, []);
        unset($items[(int) $data['product_id']]);
        session()->put(self::KEY, $items);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Produk dihapus dari permintaan penawaran.');
    }

    /**
     * @return Collection<int, Product>
     */
    private function items(): Collection
    {
        $ids = array_keys(session()->get(self::KEY, []));

        if ($ids === []) {
            return collect();
        }

        return Product::query()->active()->with('images')->whereIn('id', $ids)->get();
    }
}
