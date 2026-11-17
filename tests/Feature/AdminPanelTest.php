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

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/products')->assertOk();
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
    }
}
