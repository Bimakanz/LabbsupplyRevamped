<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CompareService
{
    private const KEY = 'compare.items';

    private const MAX = 4;

    /**
     * @return array<int, int>
     */
    public function ids(): array
    {
        return array_values(array_unique(session()->get(self::KEY, [])));
    }

    /**
     * @return array{added: bool, message: string}
     */
    public function toggle(int $productId): array
    {
        $ids = $this->ids();

        if (in_array($productId, $ids, true)) {
            $ids = array_values(array_diff($ids, [$productId]));
            session()->put(self::KEY, $ids);

            return ['added' => false, 'message' => 'Produk dihapus dari perbandingan.'];
        }

        if (count($ids) >= self::MAX) {
            return ['added' => false, 'message' => 'Maksimal '.self::MAX.' produk untuk dibandingkan.'];
        }

        $ids[] = $productId;
        session()->put(self::KEY, $ids);

        return ['added' => true, 'message' => 'Produk ditambahkan ke perbandingan.'];
    }

    public function remove(int $productId): void
    {
        session()->put(self::KEY, array_values(array_diff($this->ids(), [$productId])));
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    public function count(): int
    {
        return count($this->ids());
    }

    /**
     * @return Collection<int, Product>
     */
    public function products(): Collection
    {
        $ids = $this->ids();

        if ($ids === []) {
            return collect();
        }

        $products = Product::query()
            ->active()
            ->whereIn('id', $ids)
            ->with('images', 'category')
            ->get()
            ->keyBy('id');

        return collect($ids)->map(fn (int $id) => $products->get($id))->filter()->values();
    }
}
