<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Database\Seeders\CatalogSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class LabSupplyDoctor extends Command
{
    protected $signature = 'labsupply:doctor {--seed : Isi katalog otomatis kalau datanya kosong}';

    protected $description = 'Periksa koneksi database, data katalog, fixture, dan gambar produk';

    public function handle(): int
    {
        $connection = config('database.default');
        $database = config('database.connections.'.$connection.'.database');

        $this->newLine();
        $this->info('Lab Supply Doctor');
        $this->line('  PHP            : '.PHP_VERSION);
        $this->line('  APP_ENV        : '.config('app.env'));
        $this->line('  DB connection  : '.$connection);
        $this->line('  DB database    : '.$database);
        $this->line('  Config cached  : '.(app()->configurationIsCached() ? 'YA (bisa sebabkan data tidak terbaca)' : 'tidak'));
        $this->line('  Storage link   : '.(File::exists(public_path('storage')) ? 'ada' : 'tidak ada'));

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $exception) {
            $this->error('  Koneksi database GAGAL: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->line('  Koneksi DB     : OK');

        $counts = [
            'products' => Product::class,
            'categories' => Category::class,
            'tags' => Tag::class,
            'product_images' => ProductImage::class,
            'pages' => Page::class,
        ];

        $this->newLine();
        $this->line('  Data saat ini:');

        foreach ($counts as $table => $model) {
            $count = Schema::hasTable($table) ? $model::query()->count() : -1;
            $label = $count === -1 ? 'TABEL BELUM ADA' : (string) $count;
            $this->line('    '.str_pad($table, 16).': '.$label);
        }

        $fixture = database_path('data/catalog.json');
        $fixtureExists = File::exists($fixture);
        $this->newLine();
        $this->line('  Fixture katalog: '.($fixtureExists ? 'ada' : 'TIDAK ADA'));

        if ($fixtureExists) {
            $data = json_decode(File::get($fixture), true) ?? [];
            $this->line('    isi fixture   : '.count($data['products'] ?? []).' produk, '.count($data['categories'] ?? []).' kategori, '.count($data['pages'] ?? []).' halaman');
        }

        $imageDirectory = storage_path('app/public/products');
        $imageCount = File::exists($imageDirectory) ? count(File::files($imageDirectory)) : 0;
        $this->line('  File gambar    : '.$imageCount.' file di storage/app/public/products');

        $productCount = Schema::hasTable('products') ? Product::query()->count() : 0;
        $hasTables = Schema::hasTable('products');

        if ($hasTables && $productCount === 0 && $this->option('seed')) {
            $this->newLine();
            $this->info('  Mengisi katalog...');
            $this->call('db:seed', ['--class' => CatalogSeeder::class, '--force' => true]);
            $productCount = Product::query()->count();
        }

        $this->newLine();

        if (! $hasTables) {
            $this->error('  VERDICT: tabel database belum dibuat. Jalankan: php artisan migrate --seed');

            return self::FAILURE;
        }

        if ($productCount === 0) {
            $this->error('  VERDICT: data produk KOSONG. Jalankan: php artisan db:seed');

            return self::FAILURE;
        }

        $imageRows = Schema::hasTable('product_images') ? ProductImage::query()->count() : 0;

        if ($imageRows === 0) {
            $this->warn('  VERDICT: produk ada tapi relasi gambar KOSONG (gambar akan tampil logo). Jalankan: php artisan labsupply:images --repair');

            return self::FAILURE;
        }

        $this->info('  VERDICT: katalog siap dipakai ('.$productCount.' produk).');

        return self::SUCCESS;
    }
}
