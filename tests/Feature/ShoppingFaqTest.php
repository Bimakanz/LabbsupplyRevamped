<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingFaqTest extends TestCase
{
    use RefreshDatabase;

    private function makeFaqPage(): Page
    {
        return Page::create([
            'slug' => 'shopping-faq',
            'title' => 'Shopping FAQs',
            'template' => 'faq',
            'meta_title' => 'Shopping FAQs - Lab Supply',
            'meta_description' => 'Pertanyaan yang sering diajukan.',
            'is_active' => true,
            'content' => <<<'HTML'
                <p><strong>Terakhir diperbarui:</strong> 8 Oktober 2025</p>
                <p>Selamat datang di halaman FAQ <strong>LabSupply.id</strong>!</p>
                <p>Kami mengumpulkan pertanyaan yang paling sering diajukan pelanggan.</p>
                <h3><strong>1. Tentang Produk</strong></h3>
                <p><strong>Q: Apakah semua produk baru dan original?</strong></p>
                <p>A: Ya, semua produk kami baru dan original.</p>
                <p><strong>Q: Apakah semua produk ready stock?</strong><br>A: Sebagian besar produk pre-order.</p>
                <h3><strong>2. Pengiriman</strong></h3>
                <p><strong>Q: Bagaimana cara melacak pesanan?</strong></p>
                <p>A: Hubungi <strong><a href="">support@labsupply.id</a></strong> dengan nomor pesanan.</p>
                HTML,
        ]);
    }

    public function test_shopping_faq_page_renders_the_premium_faq_layout(): void
    {
        $page = $this->makeFaqPage();

        $this->get('/'.$page->slug)
            ->assertOk()
            ->assertSee('Shopping FAQs')
            ->assertSee('Cari jawaban')
            ->assertSee('Tentang Produk')
            ->assertSee('Pengiriman')
            ->assertSee('Apakah semua produk baru dan original?')
            ->assertSee('Apakah semua produk ready stock?')
            ->assertSee('Sebagian besar produk pre-order.')
            ->assertSee('mailto:support@labsupply.id', false)
            ->assertSee('faq-panel-tentang-produk-0', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"FAQPage"', false);
    }

    public function test_pages_without_a_template_still_use_the_regular_page_layout(): void
    {
        $page = Page::create([
            'slug' => 'tentang-kami',
            'title' => 'Tentang Kami',
            'is_active' => true,
            'content' => '<p>Konten halaman biasa.</p>',
        ]);

        $this->get('/'.$page->slug)
            ->assertOk()
            ->assertSee('Tentang Kami')
            ->assertSee('Konten halaman biasa.', false)
            ->assertDontSee('faq-panel-', false);
    }
}
