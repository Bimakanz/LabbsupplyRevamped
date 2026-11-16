<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Coupon;
use App\Models\Download;
use App\Models\Post;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percent',
                'value' => 10,
                'min_total' => 500000,
                'max_discount' => 250000,
                'usage_limit' => 100,
                'is_active' => true,
            ]
        );

        $posts = [
            [
                'title' => 'Cara Memilih Glassware yang Tepat untuk Laboratorium',
                'excerpt' => 'Panduan singkat memilih glassware berdasarkan aplikasi, akurasi, dan bahan kimia yang digunakan.',
            ],
            [
                'title' => 'Tips Merawat Instrumen Laboratorium Agar Awet',
                'excerpt' => 'Perawatan rutin sederhana yang dapat memperpanjang usia pakai instrumen laboratorium Anda.',
            ],
            [
                'title' => 'Mengenal Standar Kalibrasi Alat Ukur Laboratorium',
                'excerpt' => 'Kenapa kalibrasi penting dan bagaimana memastikan alat ukur Anda selalu akurat.',
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                [
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => '<p>'.$post['excerpt'].'</p><p>Tim Lab Supply menyediakan berbagai pilihan produk dan layanan pendukung untuk memastikan kegiatan laboratorium Anda berjalan lancar. Hubungi kami untuk konsultasi kebutuhan alat dan bahan laboratorium.</p>',
                    'is_published' => true,
                    'published_at' => now()->subDays(3),
                ]
            );
        }

        $downloads = [
            ['Katalog Produk Lab Supply 2026', 'Daftar lengkap produk laboratorium beserta spesifikasi dan harga.'],
            ['Sertifikat ISO 9001:2015', 'Sertifikat sistem manajemen mutu Lab Supply.'],
            ['Company Profile', 'Profil perusahaan, visi misi, dan layanan Lab Supply.'],
            ['Panduan Pemesanan', 'Langkah mudah melakukan pemesanan di Lab Supply.'],
        ];

        foreach ($downloads as $index => $item) {
            Download::updateOrCreate(
                ['title' => $item[0]],
                [
                    'description' => $item[1],
                    'file' => '#',
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }

        Banner::updateOrCreate(
            ['title' => 'Lab Supply for Your Laboratory'],
            [
                'subtitle' => 'Alat dan bahan laboratorium untuk riset yang lebih efisien.',
                'image' => 'theme/img/hero-bg.webp',
                'position' => 'home',
                'is_active' => true,
            ]
        );

        $product = Product::query()->first();

        if ($product) {
            Review::updateOrCreate(
                ['product_id' => $product->id, 'email' => 'reviewer@example.com'],
                [
                    'name' => 'Rina',
                    'rating' => 5,
                    'comment' => 'Produk sesuai deskripsi, packing rapi, dan pengiriman cepat.',
                    'is_approved' => true,
                ]
            );
        }
    }
}
