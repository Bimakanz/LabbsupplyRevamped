<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const ITEMS_KEY = 'cart.items';

    /**
     * @return array<int, int>
     */
    public function items(): array
    {
        return session()->get(self::ITEMS_KEY, []);
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $items = $this->items();
        $items[$productId] = max(1, ($items[$productId] ?? 0) + $quantity);
        session()->put(self::ITEMS_KEY, $items);
    }

    public function update(int $productId, int $quantity): void
    {
        $items = $this->items();

        if ($quantity <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = $quantity;
        }

        session()->put(self::ITEMS_KEY, $items);
    }

    public function remove(int $productId): void
    {
        $items = $this->items();
        unset($items[$productId]);
        session()->put(self::ITEMS_KEY, $items);
    }

    public function clear(): void
    {
        session()->forget(self::ITEMS_KEY);
    }

    public function count(): int
    {
        return (int) array_sum($this->items());
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return Collection<int, array{product: Product, quantity: int, subtotal: float}>
     */
    public function content(): Collection
    {
        $items = $this->items();

        if ($items === []) {
            return collect();
        }

        $products = Product::query()
            ->active()
            ->whereIn('id', array_keys($items))
            ->with('images', 'category')
            ->get()
            ->keyBy('id');

        return collect($items)
            ->map(function (int $quantity, int $productId) use ($products): ?array {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => (float) $product->price * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function subtotal(): float
    {
        return (float) $this->content()->sum('subtotal');
    }

    public function total(): float
    {
        return $this->subtotal();
    }
}
