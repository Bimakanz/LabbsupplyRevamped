<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(array $attributes = []): Product
    {
        $category = Category::create([
            'name' => 'Consumables',
            'slug' => 'consumables',
            'is_active' => true,
        ]);

        $product = Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Activated Carbon Lab-Grade 1kg',
            'slug' => 'activated-carbon-lab-grade-1kg',
            'sku' => 'LS-0001',
            'price' => 345000,
            'stock' => 10,
            'stock_status' => 'in_stock',
            'is_active' => true,
        ], $attributes));

        $product->images()->create([
            'path' => 'products/activated-carbon.jpg',
            'alt' => $product->name,
            'sort_order' => 0,
        ]);

        return $product;
    }

    public function test_homepage_renders_new_products(): void
    {
        $this->makeProduct();

        $this->get('/')
            ->assertOk()
            ->assertSee('New Products')
            ->assertSee('Activated Carbon Lab-Grade 1kg');
    }

    public function test_all_home_variants_are_accessible(): void
    {
        $this->makeProduct();

        foreach (range(1, 8) as $variant) {
            $suffix = str_pad((string) $variant, 2, '0', STR_PAD_LEFT);
            $this->get('/home-'.$suffix)->assertOk();
        }
    }

    public function test_shop_and_product_pages_render(): void
    {
        $product = $this->makeProduct();

        $this->get('/semua-produk')->assertOk()->assertSee($product->name);
        $this->get('/product/'.$product->slug)->assertOk()->assertSee($product->name);
    }

    public function test_items_can_be_added_to_the_cart(): void
    {
        $product = $this->makeProduct();

        $this->postJson(route('cart.add'), ['product_id' => $product->id, 'quantity' => 2])
            ->assertOk()
            ->assertJson(['ok' => true, 'count' => 2]);

        $this->get('/cart')->assertOk()->assertSee($product->name);
    }

    public function test_checkout_creates_order_and_marks_payment_as_paid(): void
    {
        $product = $this->makeProduct(['stock' => 10]);

        $this->postJson(route('cart.add'), ['product_id' => $product->id, 'quantity' => 2]);

        $response = $this->post('/checkout', [
            'billing_first_name' => 'Budi',
            'billing_last_name' => 'Santoso',
            'billing_company' => 'Lab Riset',
            'billing_country' => 'ID',
            'billing_address_1' => 'Jl. Pangeran Sogiri No. 8',
            'billing_city' => 'Bogor',
            'billing_state' => 'Jawa Barat',
            'billing_postcode' => '16154',
            'billing_phone' => '081234567890',
            'billing_email' => 'budi@example.com',
            'notes' => 'Kirim pagi hari',
        ]);

        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('payment.show', $order));
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame(2, $order->items()->sum('quantity'));

        $this->get(route('payment.show', $order))->assertOk()->assertSee('Sudah bayar');

        $this->post(route('payment.confirm', $order))->assertRedirect(route('payment.success', $order));

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_wishlist_and_compare_can_toggle_products(): void
    {
        $product = $this->makeProduct();

        $this->postJson(route('wishlist.toggle'), ['product_id' => $product->id])
            ->assertOk()
            ->assertJson(['added' => true, 'count' => 1]);

        $this->postJson(route('compare.toggle'), ['product_id' => $product->id])
            ->assertOk()
            ->assertJson(['added' => true, 'count' => 1]);

        $this->get('/wishlist')->assertOk()->assertSee($product->name);
        $this->get('/compare')->assertOk()->assertSee($product->name);
    }

    public function test_wishlist_page_shows_a_single_working_remove_button(): void
    {
        $product = $this->makeProduct();

        $this->withSession(['wishlist.items' => [$product->id]])
            ->get('/wishlist')
            ->assertOk()
            ->assertSee('data-wishlist-remove="'.$product->id.'"', false)
            ->assertDontSee('data-wishlist-toggle="'.$product->id.'"', false);
    }

    public function test_guest_can_submit_a_quote_request(): void
    {
        $product = $this->makeProduct();

        $this->post(route('quote.store'), [
            'name' => 'Siti',
            'email' => 'siti@example.com',
            'phone' => '081200000000',
            'company' => 'Universitas',
            'message' => 'Butuh 10 pcs',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('quote_requests', ['email' => 'siti@example.com']);
        $this->assertDatabaseHas('quote_items', ['product_id' => $product->id, 'quantity' => 10]);
    }

    public function test_settings_helper_returns_values(): void
    {
        Setting::set('site_name', 'Lab Supply Test');

        $this->assertSame('Lab Supply Test', setting('site_name'));
    }
}
