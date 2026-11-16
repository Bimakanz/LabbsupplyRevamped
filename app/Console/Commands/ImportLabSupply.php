<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ImportLabSupply extends Command
{
    protected $signature = 'labsupply:import {--pages : Import static pages as well} {--images : Download product images}';

    protected $description = 'Import catalog, categories, tags, pages and images from labsupply.id';

    private string $baseUrl = 'https://labsupply.id';

    private array $headers = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/131 Safari/537.36',
        'Accept' => 'application/json',
    ];

    public function handle(): int
    {
        $this->info('Importing categories...');
        $categories = $this->importCategories();

        $this->info('Importing tags...');
        $tags = $this->importTags();

        $this->info('Importing products...');
        $this->importProducts($categories, $tags);

        if ($this->option('pages')) {
            $this->info('Importing pages...');
            $this->importPages();
        }

        $this->info('Saving settings and admin user...');
        $this->importSettings();
        $this->createAdmin();

        $this->newLine();
        $this->info('Done. Products: '.Product::count().' | Categories: '.Category::count().' | Tags: '.Tag::count().' | Pages: '.Page::count());

        return self::SUCCESS;
    }

    private function get(string $path): array
    {
        $response = Http::withHeaders($this->headers)->timeout(60)->get($this->baseUrl.$path);

        if (! $response->successful()) {
            $this->warn("Request failed: {$path} ({$response->status()})");

            return [];
        }

        return $response->json() ?? [];
    }

    private function importCategories(): array
    {
        $items = $this->get('/wp-json/wc/store/v1/products/categories?per_page=100');
        $map = [];

        foreach ($items as $index => $item) {
            $category = Category::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => html_entity_decode($item['name']),
                    'description' => strip_tags(html_entity_decode($item['description'] ?? '')),
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );

            $map[$item['id']] = $category->id;
            $map[$item['slug']] = $category->id;
        }

        return $map;
    }

    private function importTags(): array
    {
        $items = $this->get('/wp-json/wc/store/v1/products/tags?per_page=100');
        $map = [];

        foreach ($items as $item) {
            $tag = Tag::updateOrCreate(
                ['slug' => $item['slug']],
                ['name' => html_entity_decode($item['name'])]
            );

            $map[$item['id']] = $tag->id;
            $map[$item['slug']] = $tag->id;
        }

        return $map;
    }

    private function importProducts(array $categories, array $tags): void
    {
        $items = $this->get('/wp-json/wc/store/v1/products?per_page=100&orderby=title&order=asc');

        if (empty($items)) {
            $this->error('No products returned by the store API.');

            return;
        }

        $directory = storage_path('app/public/products');
        File::ensureDirectoryExists($directory);

        foreach ($items as $index => $item) {
            $categoryId = null;
            if (! empty($item['categories'][0]['slug'])) {
                $categoryId = $categories[$item['categories'][0]['slug']] ?? null;
            }

            $product = Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $categoryId,
                    'name' => html_entity_decode($item['name']),
                    'sku' => 'LS-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'short_description' => trim(strip_tags(html_entity_decode($item['short_description'] ?? ''))),
                    'description' => $this->cleanHtml($item['description'] ?? ''),
                    'price' => (float) ($item['prices']['price'] ?? 0),
                    'stock' => 25,
                    'stock_status' => 'in_stock',
                    'is_active' => true,
                    'is_featured' => $index < 32,
                    'meta_title' => html_entity_decode($item['name']).' - Lab Supply',
                    'meta_description' => Str::limit(trim(strip_tags(html_entity_decode($item['description'] ?? ''))), 155),
                ]
            );

            $tagIds = [];
            foreach ($item['tags'] ?? [] as $tag) {
                if (isset($tags[$tag['slug']])) {
                    $tagIds[] = $tags[$tag['slug']];
                }
            }
            $product->tags()->sync(array_unique($tagIds));

            if ($this->option('images') && ! empty($item['images'][0]['src'])) {
                $this->downloadImage($product, $item['images'][0]['src'], $directory);
            }

            if (($index + 1) % 10 === 0) {
                $this->line('  ... '.($index + 1).' products');
            }
        }
    }

    private function downloadImage(Product $product, string $url, string $directory): void
    {
        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
        $filename = $product->slug.'.'.$extension;
        $target = $directory.DIRECTORY_SEPARATOR.$filename;

        if (! File::exists($target)) {
            try {
                $response = Http::withHeaders(['User-Agent' => $this->headers['User-Agent']])
                    ->timeout(60)
                    ->get($url);

                if ($response->successful()) {
                    File::put($target, $response->body());
                }
            } catch (\Throwable $exception) {
                $this->warn("  Image failed for {$product->slug}: {$exception->getMessage()}");
            }
        }

        if (File::exists($target)) {
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => 0],
                ['path' => 'products/'.$filename, 'alt' => $product->name]
            );
        }
    }

    private function importPages(): void
    {
        $items = $this->get('/wp-json/wp/v2/pages?per_page=100&_fields=slug,title,content,link');

        $skip = ['sample-page', 'elementor-5924', 'wishlist-2', 'cart', 'checkout', 'my-account', 'shop', 'wishlist', 'home-01', 'home-02', 'home-03', 'home-04', 'home-05', 'home-06', 'home-07', 'home-08'];

        foreach ($items as $item) {
            if (in_array($item['slug'], $skip, true)) {
                continue;
            }

            Page::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => html_entity_decode(strip_tags($item['title']['rendered'] ?? $item['slug'])),
                    'content' => $this->cleanHtml($item['content']['rendered'] ?? ''),
                    'meta_title' => html_entity_decode(strip_tags($item['title']['rendered'] ?? '')).' - Lab Supply',
                    'is_active' => true,
                ]
            );
        }
    }

    private function cleanHtml(string $html): string
    {
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $html = preg_replace('#<(script|style|noscript)[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $html = preg_replace('#<!--.*?-->#s', '', $html) ?? $html;

        if (class_exists(\DOMDocument::class)) {
            $document = new \DOMDocument;
            libxml_use_internal_errors(true);
            $document->loadHTML('<?xml encoding="utf-8" ?><div id="root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $root = $document->getElementById('root');
            if ($root instanceof \DOMElement) {
                $allowed = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'br', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'blockquote', 'figure', 'figcaption'];
                $output = '';

                foreach (iterator_to_array($root->childNodes) as $node) {
                    $output .= $this->renderNode($node, $allowed);
                }

                $output = trim($output);

                return $output !== '' ? $output : '';
            }
        }

        return trim(strip_tags($html, '<p><h2><h3><h4><ul><ol><li><strong><em><br><table><tr><td><th><img>'));
    }

    private function renderNode(\DOMNode $node, array $allowed): string
    {
        if ($node instanceof \DOMText) {
            $text = trim($node->nodeValue ?? '');

            return $text === '' ? '' : htmlspecialchars($node->nodeValue ?? '', ENT_QUOTES, 'UTF-8');
        }

        if (! $node instanceof \DOMElement) {
            return '';
        }

        $tag = strtolower($node->tagName);

        if ($tag === 'img') {
            $src = $node->getAttribute('src');
            if ($src === '') {
                return '';
            }

            return '<img src="'.htmlspecialchars($src, ENT_QUOTES, 'UTF-8').'" alt="'.htmlspecialchars($node->getAttribute('alt'), ENT_QUOTES, 'UTF-8').'" loading="lazy">';
        }

        if ($tag === 'a') {
            $href = $node->getAttribute('href');
            $inner = '';
            foreach (iterator_to_array($node->childNodes) as $child) {
                $inner .= $this->renderNode($child, $allowed);
            }

            return '<a href="'.htmlspecialchars($href, ENT_QUOTES, 'UTF-8').'" rel="noopener">'.$inner.'</a>';
        }

        $inner = '';
        foreach (iterator_to_array($node->childNodes) as $child) {
            $inner .= $this->renderNode($child, $allowed);
        }

        if (trim($inner) === '' && ! in_array($tag, ['br'], true)) {
            return '';
        }

        if (! in_array($tag, $allowed, true)) {
            return $inner;
        }

        return "<{$tag}>{$inner}</{$tag}>";
    }

    private function importSettings(): void
    {
        $settings = [
            'site_name' => 'Lab Supply',
            'site_tagline' => 'Lab Supply for Your Laboratory',
            'phone' => '+62 811-1174-992',
            'phone_link' => '628111174992',
            'email' => 'support@labsupply.id',
            'address' => 'Ruko No. 8 Green Residence – Jl. Pangeran Sogiri, RT.01/RW.01, Kecamatan Tanah Baru, Bogor Utara 16154',
            'whatsapp' => '628111174992',
            'whatsapp_message' => 'Halo Lab Supply, saya ingin bertanya tentang produk laboratorium.',
            'active_home' => 'home-08',
            'free_shipping_text' => 'Free delivery',
            'support_text' => 'Support 24/7',
            'payment_text' => 'Payment',
            'guarantee_text' => 'Reliable Guarantee',
            'footer_about' => 'Lab Supply for Your Laboratory',
            'instagram' => '#',
            'facebook' => '#',
            'youtube' => '#',
            'linkedin' => '#',
            'twitter' => '#',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }

    private function createAdmin(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@labsupply.id'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');
    }
}
