<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StoreLocation;
use App\Models\Cart;
use App\Models\CartItem;

class PickupOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_place_pickup_order()
    {
        // Setup Data
        $distributor = Distributor::factory()->create();
        $user = User::factory()->create(['distributor_id' => $distributor->id]);
        
        $product = Product::factory()->create(['distributor_id' => $distributor->id, 'price' => 10000]);
        
        $store = StoreLocation::create([
            'distributor_id' => $distributor->id,
            'name' => 'Test Store',
            'address' => 'Test Address',
            'city' => 'Test City',
            'is_active' => true
        ]);

        // Stock at store
        ProductStock::create([
            'distributor_id' => $distributor->id,
            'product_id' => $product->id,
            'store_location_id' => $store->id,
            'quantity' => 10
        ]);

        // Cart
        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->actingAs($user);

        // Checkout Request
        $response = $this->post(route('checkout.store'), [
            'delivery_type' => 'pickup',
            'pickup_store_id' => $store->id,
            'pickup_at' => now()->addDay()->format('Y-m-d H:i:s'), // Format matching default date parsing
            'payment_method' => 'bank_transfer',
            // Fields that should be ignored or empty
            'shipping_address' => null,
            'shipping_courier' => null,
        ]);

        // Assert Redirect to Success
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(); 

        // Get the order
        $order = \App\Models\Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        
        $this->assertEquals('pickup', $order->delivery_type);
        $this->assertEquals($store->id, $order->pickup_store_id);
        $this->assertEquals(20000, $order->total_amount);
        $this->assertNotNull($order->pickup_token);

        // Assert Stock Decremented
        $this->assertDatabaseHas('product_stocks', [
            'store_location_id' => $store->id,
            'product_id' => $product->id,
            'quantity' => 8 // 10 - 2
        ]);
    }
}
