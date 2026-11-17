<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CatalogSeeder extends Seeder
{
    /**
     * Seed the storefront catalog from the bundled fixture so a fresh clone
     * has categories, products, tags, images, and pages without scraping.
     */
    public function run(): void
    {
        $path = database_path('data/catalog.json');

        if (! File::exists($path)) {
            $this->command?->warn('Catalog fixture not found: '.$path);

            return;
        }

        /** @var array{categories: array<int, array<string, mixed>>, tags: array<int, array<string, mixed>>, products: array<int, array<string, mixed>>, pages: array<int, array<string, mixed>>} $data */
        $data = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($data): void {
            $categoryIds = $this->seedCategories($data['categories'] ?? []);
            $tagIds = $this->seedTags($data['tags'] ?? []);

            $this->seedProducts($data['products'] ?? [], $categoryIds, $tagIds);

            foreach ($data['pages'] ?? [] as $page) {
                Page::updateOrCreate(
                    ['slug' => $page['slug']],
                    [
                        'title' => $page['title'],
                        'content' => $page['content'],
                        'template' => $page['template'],
                        'meta_title' => $page['meta_title'],
                        'meta_description' => $page['meta_description'],
                        'is_active' => $page['is_active'],
                        'sort_order' => $page['sort_order'],
                    ]
                );
            }
        });

        $this->command?->info(sprintf(
            'Catalog seeded: %d categories, %d products, %d tags, %d pages.',
            Category::query()->count(),
            Product::query()->count(),
            Tag::query()->count(),
            Page::query()->count(),
        ));
    }

    /**
     * @param  array<int, array<string, mixed>>  $categories
     * @return array<string, int>
     */
    private function seedCategories(array $categories): array
    {
        $ids = [];

        foreach ($categories as $category) {
            $model = Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'image' => $category['image'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => $category['is_active'],
                    'meta_title' => $category['meta_title'],
                    'meta_description' => $category['meta_description'],
                ]
            );

            $ids[$category['slug']] = $model->id;
        }

        foreach ($categories as $category) {
            $parent = $category['parent'] ?? null;

            if ($parent && isset($ids[$parent])) {
                Category::query()
                    ->whereKey($ids[$category['slug']])
                    ->update(['parent_id' => $ids[$parent]]);
            }
        }

        return $ids;
    }

    /**
     * @param  array<int, array<string, mixed>>  $tags
     * @return array<string, int>
     */
    private function seedTags(array $tags): array
    {
        $ids = [];

        foreach ($tags as $tag) {
            $model = Tag::updateOrCreate(
                ['slug' => $tag['slug']],
                ['name' => $tag['name']]
            );

            $ids[$tag['slug']] = $model->id;
        }

        return $ids;
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @param  array<string, int>  $categoryIds
     * @param  array<string, int>  $tagIds
     */
    private function seedProducts(array $products, array $categoryIds, array $tagIds): void
    {
        foreach ($products as $product) {
            $model = Product::updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'category_id' => $product['category'] ? ($categoryIds[$product['category']] ?? null) : null,
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'short_description' => $product['short_description'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'compare_price' => $product['compare_price'],
                    'stock' => $product['stock'],
                    'stock_status' => $product['stock_status'],
                    'weight' => $product['weight'],
                    'dimensions' => $product['dimensions'],
                    'is_active' => $product['is_active'],
                    'is_featured' => $product['is_featured'],
                    'views' => $product['views'],
                    'rating_avg' => $product['rating_avg'],
                    'rating_count' => $product['rating_count'],
                    'meta_title' => $product['meta_title'],
                    'meta_description' => $product['meta_description'],
                ]
            );

            foreach ($product['images'] ?? [] as $image) {
                ProductImage::updateOrCreate(
                    ['product_id' => $model->id, 'sort_order' => $image['sort_order']],
                    ['path' => $image['path'], 'alt' => $image['alt']]
                );
            }

            $relatedTags = array_values(array_filter(
                array_map(fn (string $slug): ?int => $tagIds[$slug] ?? null, $product['tags'] ?? [])
            ));

            $model->tags()->sync($relatedTags);
        }
    }
}
