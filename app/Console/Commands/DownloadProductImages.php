<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DownloadProductImages extends Command
{
    protected $signature = 'labsupply:images {--force : Unduh ulang semua gambar} {--repair : Perbaiki relasi gambar dari file lokal tanpa mengunduh}';

    protected $description = 'Unduh gambar produk dari labsupply.id dan simpan sebagai WebP';

    public function handle(): int
    {
        if ($this->option('repair')) {
            return $this->repair();
        }

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/131',
        ])->timeout(60)->get('https://labsupply.id/wp-json/wc/store/v1/products?per_page=100');

        if ($response->failed()) {
            $this->error('Gagal mengambil data produk dari labsupply.id.');

            return self::FAILURE;
        }

        $directory = storage_path('app/public/products');
        File::ensureDirectoryExists($directory);

        $downloaded = 0;

        foreach ($response->json() as $item) {
            $slug = $item['slug'] ?? null;
            $source = $item['images'][0]['src'] ?? null;

            if (! $slug || ! $source) {
                continue;
            }

            $target = $directory.DIRECTORY_SEPARATOR.$slug.'.webp';

            if (File::exists($target) && ! $this->option('force')) {
                continue;
            }

            $bytes = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])->timeout(60)->get($source)->body();
            $image = @imagecreatefromstring($bytes);

            if (! $image) {
                $this->warn('Gagal membaca gambar '.$slug);

                continue;
            }

            imagepalettetotruecolor($image);
            imagesavealpha($image, true);
            imagewebp($image, $target, 90);
            imagedestroy($image);

            $product = Product::query()->where('slug', $slug)->first();

            if ($product) {
                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'sort_order' => 0],
                    ['path' => 'products/'.$slug.'.webp', 'alt' => $product->name]
                );
            }

            $downloaded++;
        }

        $this->info('Selesai. Gambar diunduh: '.$downloaded);

        return self::SUCCESS;
    }

    private function repair(): int
    {
        $directory = storage_path('app/public/products');
        File::ensureDirectoryExists($directory);

        $created = 0;
        $fixed = 0;

        Product::query()->with('images')->chunkById(200, function ($products) use ($directory, &$created, &$fixed): void {
            foreach ($products as $product) {
                $webp = 'products/'.$product->slug.'.webp';

                if (! File::exists($directory.DIRECTORY_SEPARATOR.$product->slug.'.webp')) {
                    continue;
                }

                if ($product->images->isEmpty()) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $webp,
                        'alt' => $product->name,
                        'sort_order' => 0,
                    ]);

                    $created++;

                    continue;
                }

                foreach ($product->images as $image) {
                    if (str_starts_with($image->path, 'http')) {
                        continue;
                    }

                    if (File::exists(storage_path('app/public/'.$image->path))) {
                        continue;
                    }

                    $image->update(['path' => $webp]);
                    $fixed++;
                }
            }
        });

        $this->info('Selesai. Gambar ditambahkan: '.$created.', path diperbaiki: '.$fixed);

        return self::SUCCESS;
    }
}
