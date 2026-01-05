<?php

namespace Tests\Feature\Checkout;

use App\Models\Distributor;
use App\Models\User;
use App\Services\RajaOngkirService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class ShippingCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected $distributor;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Distributor
        $this->distributor = Distributor::create([
            'name' => 'Test Distributor',
            'code' => 'TEST001',
            'status' => 'active',
            'level' => 'distributor',
            'config' => [
                'shipping' => [
                    'origin_city_id' => '255', // Malang
                    'couriers' => ['jne'],
                    'margin' => 1000
                ]
            ]
        ]);

        // Create User
        $this->user = User::factory()->create([
            'distributor_id' => $this->distributor->id,
        ]);
    }

    public function test_can_fetch_shipping_rates_via_ajax()
    {
        $this->actingAs($this->user);

        // Mock RajaOngkirService
        $mock = Mockery::mock(RajaOngkirService::class);
        $mock->shouldReceive('calculateCost')
            ->with('255', '114', 1000, 'jne') // Origin 255 (Malang), Dest 114 (Denpasar), 1000g, jne
            ->once()
            ->andReturn([
                'origin_details' => ['city_name' => 'Malang'],
                'destination_details' => ['city_name' => 'Denpasar'],
                'costs' => [
                    [
                        'service' => 'REG',
                        'description' => 'Layanan Reguler',
                        'cost' => [['value' => 20000, 'etd' => '2-3']]
                    ],
                    [
                        'service' => 'YES',
                        'description' => 'Yakin Esok Sampai',
                        'cost' => [['value' => 30000, 'etd' => '1-1']]
                    ]
                ]
            ]);

        $this->app->instance(RajaOngkirService::class, $mock);

        // Make Request
        $response = $this->postJson(route('checkout.check-shipping'), [
            'city_id' => '114', // Denpasar
            'weight' => 1000
        ]);

        $response->assertStatus(200);
        
        // Assert JSON structure and margin application
        $response->assertJsonCount(2); // REG and YES
        
        $response->assertJsonFragment([
            'service' => 'REG',
            'cost' => 21000, // 20000 + 1000 margin
            'formatted_cost' => 'Rp 21.000'
        ]);

        $response->assertJsonFragment([
            'service' => 'YES',
            'cost' => 31000 // 30000 + 1000 margin
        ]);
    }

    public function test_returns_empty_if_courier_fails()
    {
        $this->actingAs($this->user);

        // Mock RajaOngkirService to fail/return null
        $mock = Mockery::mock(RajaOngkirService::class);
        $mock->shouldReceive('calculateCost')
            ->andReturn([]); // Empty result

        $this->app->instance(RajaOngkirService::class, $mock);

        $response = $this->postJson(route('checkout.check-shipping'), [
            'city_id' => '114',
            'weight' => 1000
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }
}
