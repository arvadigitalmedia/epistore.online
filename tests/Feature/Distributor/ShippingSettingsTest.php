<?php

namespace Tests\Feature\Distributor;

use App\Models\Distributor;
use App\Models\User;
use App\Services\RajaOngkirService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class ShippingSettingsTest extends TestCase
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
            'config' => []
        ]);

        // Create User (Owner)
        $this->user = User::factory()->create([
            'distributor_id' => $this->distributor->id,
            'email' => 'owner@test.com'
        ]);

        // Seed Couriers
        \App\Models\Courier::create(['code' => 'jne', 'name' => 'JNE', 'is_active' => true, 'priority' => 1]);
        \App\Models\Courier::create(['code' => 'pos', 'name' => 'POS', 'is_active' => true, 'priority' => 2]);

        // Mock RajaOngkirService default methods
        $mock = Mockery::mock(RajaOngkirService::class);
        $mock->shouldReceive('getProvinces')->andReturn([
            ['province_id' => '11', 'province' => 'Jawa Timur']
        ]);
        $this->app->instance(RajaOngkirService::class, $mock);
    }

    public function test_distributor_can_enable_store_pickup()
    {
        $this->actingAs($this->user);

        $data = [
            'origin_province_id' => '11',
            'origin_city_id' => '255',
            'default_weight' => 1000,
            'enable_store_pickup' => 1
        ];

        $response = $this->post(route('distributor.shipping.update'), $data);

        $response->assertRedirect();
        
        $this->distributor->refresh();
        $this->assertTrue($this->distributor->config['shipping']['enable_store_pickup']);
    }

    public function test_courier_list_is_dynamic()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('distributor.shipping.index'));

        $response->assertStatus(200);
        $response->assertSee('JNE (JNE)');
        $response->assertSee('POS (POS)');
        
        // Add a new courier
        \App\Models\Courier::create(['code' => 'sicepat', 'name' => 'SiCepat', 'is_active' => true, 'priority' => 3]);
        
        $response = $this->get(route('distributor.shipping.index'));
        $response->assertSee('SiCepat (SICEPAT)');
    }

    public function test_distributor_can_update_shipping_settings_simplified()
    {
        $this->actingAs($this->user);

        // Data without subdistrict, postal code, address (simplified form)
        $data = [
            'origin_province_id' => '11',
            'origin_province_name' => 'Jawa Timur',
            'origin_city_id' => '255',
            'origin_city_name' => 'Malang',
            'origin_district_id' => '3578010',
            'origin_district_name' => 'Klojen',
            'default_weight' => 1000,
            'margin' => 5000,
            'couriers' => ['jne', 'pos']
        ];

        $response = $this->post(route('distributor.shipping.update'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify Database Columns (Persistence Check)
        $this->assertDatabaseHas('distributors', [
            'id' => $this->distributor->id,
            'province_id' => '11',
            'city_id' => '255',
            'district_id' => '3578010',
            // Ensure nulls for removed fields if they were not sent
            // 'postal_code' => null, // If column allows null
        ]);

        // Verify Config JSON (Legacy Support Check)
        $this->distributor->refresh();
        $config = $this->distributor->config;
        
        $this->assertEquals('11', $config['shipping']['origin_province_id']);
        $this->assertEquals('255', $config['shipping']['origin_city_id']);
        $this->assertEquals('3578010', $config['shipping']['origin_district_id']);
    }

    public function test_shipping_settings_validation_errors()
    {
        $this->actingAs($this->user);

        // Send empty data
        $response = $this->post(route('distributor.shipping.update'), [
            'origin_province_id' => '', 
        ]);

        // Postal code and address are no longer required
        $response->assertSessionHasErrors(['origin_province_id', 'origin_city_id']);
        $response->assertSessionDoesntHaveErrors(['origin_postal_code', 'origin_address']);
    }

    public function test_can_calculate_shipping_cost_using_saved_settings()
    {
        $this->actingAs($this->user);

        // 1. Save Settings First
        $this->distributor->update([
            'province_id' => '11',
            'city_id' => '255', // Malang
            'district_id' => '3578010', // Klojen
            'config' => [
                'shipping' => [
                    'origin_province_id' => '11',
                    'origin_city_id' => '255',
                    'origin_district_id' => '3578010',
                    'margin' => 1000
                ]
            ]
        ]);

        // 2. Mock RajaOngkir calculateCost
        $mock = Mockery::mock(RajaOngkirService::class);
        $mock->shouldReceive('calculateCost')
            ->with('255', '114', 1000, 'jne') // Expect origin=city_id (Malang) to ensure Starter compatibility
            ->once()
            ->andReturn([
                'origin_details' => ['city_name' => 'Malang'],
                'destination_details' => ['city_name' => 'Denpasar'],
                'costs' => [
                    [
                        'service' => 'REG',
                        'description' => 'Layanan Reguler',
                        'cost' => [['value' => 20000, 'etd' => '2-3']]
                    ]
                ]
            ]);
        $this->app->instance(RajaOngkirService::class, $mock);

        // 3. Request Preview
        $response = $this->postJson(route('distributor.shipping.preview'), [
            'destination_city_id' => '114', // Denpasar
            'destination_district_id' => null,
            'weight' => 1000,
            'courier' => 'jne'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('costs.0.service', 'REG')
            ->assertJsonPath('costs.0.cost.0.value', 21000); // 20000 + 1000 margin

        // 4. Verify Log (Optional, check if DB log created)
        $this->assertDatabaseHas('shipping_calculation_logs', [
            'distributor_id' => $this->distributor->id,
            'origin_city_id' => '255', // Origin is city ID
            'destination_city_id' => '114',
            'total_price' => 21000
        ]);
    }
}
