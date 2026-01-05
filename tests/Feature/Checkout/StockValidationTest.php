<?php

namespace Tests\Feature\Checkout;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $distributor;
    protected $product;
    protected $brand;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Distributor
        $this->distributor = Distributor::create([
            'name' => 'Test Distributor',
            'code' => 'TEST001',
            'status' => 'active',
            'level' => 'distributor',
            'config' => [
                'shipping' => [
                    'enable_store_pickup' => true
                ]
            ]
        ]);

        // Setup User
        $this->user = User::factory()->create([
            'distributor_id' => $this->distributor->id,
            'address' => 'Test Address',
            'province_id' => '1',
            'city_id' => '1',
            'district_id' => '1',
            'postal_code' => '12345',
            'phone' => '08123456789'
        ]);

        // Setup Brand
        $this->brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'status' => 'active'
        ]);

        // Setup Product
        $this->product = Product::create([
            'brand_id' => $this->brand->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TP001',
            'price' => 100000,
            'status' => 'active'
        ]);

        // Setup Stock
        ProductStock::create([
            'distributor_id' => $this->distributor->id,
            'product_id' => $this->product->id,
            'quantity' => 10
        ]);

        // Setup Cart
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);
    }

    public function test_checkout_fails_if_stock_insufficient()
    {
        // Reduce stock to less than cart quantity
        ProductStock::where('distributor_id', $this->distributor->id)
            ->where('product_id', $this->product->id)
            ->update(['quantity' => 2]);

        $this->actingAs($this->user);

        $response = $this->post(route('checkout.store'), [
            'delivery_method' => 'store_pickup',
            'payment_method' => 'bank_transfer'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Insufficient stock', session('error'));
    }

    public function test_checkout_deducts_stock_on_success()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('checkout.store'), [
            'delivery_method' => 'store_pickup',
            'payment_method' => 'bank_transfer'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('product_stocks', [
            'distributor_id' => $this->distributor->id,
            'product_id' => $this->product->id,
            'quantity' => 5 // 10 - 5
        ]);
    }

    public function test_stock_check_uses_correct_distributor()
    {
        // Create another distributor
        $otherDistributor = Distributor::create([
            'name' => 'Other Distributor',
            'code' => 'OTHER001',
            'status' => 'active'
        ]);

        // Stock in other distributor is 0
        ProductStock::create([
            'distributor_id' => $otherDistributor->id,
            'product_id' => $this->product->id,
            'quantity' => 0
        ]);

        // Assign user to other distributor
        $this->user->update(['distributor_id' => $otherDistributor->id]);
        
        // Also need to enable store pickup for other distributor if we test that path
        $otherDistributor->update([
            'config' => ['shipping' => ['enable_store_pickup' => true]]
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('checkout.store'), [
            'delivery_method' => 'store_pickup',
            'payment_method' => 'bank_transfer'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error'); // Should fail because other distributor has 0 stock
        $this->assertStringContainsString('Insufficient stock', session('error'));
    }
}
