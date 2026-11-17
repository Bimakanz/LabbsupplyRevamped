<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_the_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_the_panel(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Master Data');
        $this->actingAs($admin)->get('/admin/products')->assertOk();
        $this->actingAs($admin)->get('/admin/products/create')->assertOk()->assertSee('Gambar Produk');
        $this->actingAs($admin)->get('/admin/orders')->assertOk();
        $this->actingAs($admin)->get('/admin/settings')->assertOk();
    }

    public function test_customer_cannot_access_the_panel(): void
    {
        Role::create(['name' => 'customer', 'guard_name' => 'web']);

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)->get('/admin')->assertForbidden();
    }

    public function test_product_table_renders_the_stored_image(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $product = Product::create([
            'name' => 'Burette 50ml',
            'slug' => 'burette-50ml',
            'price' => 250000,
            'stock' => 5,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/burette-50ml.webp',
            'alt' => $product->name,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)
            ->get('/admin/products')
            ->assertOk()
            ->assertSee('products/burette-50ml.webp', false);

        $this->actingAs($admin)
            ->get("/admin/products/{$product->slug}/edit")
            ->assertOk()
            ->assertSee('js/filament-bg-editor.js', false);
    }

    public function test_admin_can_remove_the_background_automatically(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $gd = imagecreatetruecolor(40, 40);
        imagefill($gd, 0, 0, imagecolorallocate($gd, 250, 250, 250));
        imagefilledrectangle($gd, 10, 10, 30, 30, imagecolorallocate($gd, 180, 40, 40));

        ob_start();
        imagepng($gd);
        $png = (string) ob_get_clean();
        imagedestroy($gd);

        $response = $this->actingAs($admin)->postJson(route('admin.image.remove-background'), [
            'image' => 'data:image/png;base64,'.base64_encode($png),
            'tolerance' => 32,
        ]);

        $response->assertOk();
        $this->assertStringStartsWith('data:image/webp;base64,', $response->json('image'));
    }
}
