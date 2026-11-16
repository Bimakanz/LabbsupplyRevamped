<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const ITEMS_KEY = 'cart.items';

    private const COUPON_KEY = 'cart.coupon';

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
        session()->forget([self::ITEMS_KEY, self::COUPON_KEY]);
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

    public function coupon(): ?Coupon
    {
        $code = session()->get(self::COUPON_KEY);

        if (blank($code)) {
            return null;
        }

        return Coupon::query()->where('code', $code)->first();
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function applyCoupon(string $code): array
    {
        $coupon = Coupon::query()->where('code', strtoupper(trim($code)))->first();

        if (! $coupon) {
            return ['success' => false, 'message' => 'Kode kupon tidak ditemukan.'];
        }

        if (! $coupon->isValidFor($this->subtotal())) {
            return ['success' => false, 'message' => 'Kupon tidak valid atau syarat minimum belum terpenuhi.'];
        }

        session()->put(self::COUPON_KEY, $coupon->code);

        return ['success' => true, 'message' => 'Kupon berhasil digunakan.'];
    }

    public function removeCoupon(): void
    {
        session()->forget(self::COUPON_KEY);
    }

    public function discount(): float
    {
        $coupon = $this->coupon();

        if (! $coupon || ! $coupon->isValidFor($this->subtotal())) {
            return 0.0;
        }

        return $coupon->discountFor($this->subtotal());
    }

    public function total(): float
    {
        return max(0, $this->subtotal() - $this->discount());
    }
}
