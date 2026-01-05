<?php

namespace Tests\Feature\Storefront;

use App\Models\Distributor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_access_storefront_via_subdomain()
    {
        $distributor = Distributor::factory()->create([
            'name' => 'Test Store',
            'subdomain' => 'teststore',
            'status' => 'active',
        ]);

        $response = $this->get('http://teststore.epi-oss.test');

        $response->assertStatus(200);
        $response->assertViewIs('storefront.home');
        $response->assertSee('Test Store');
    }

    public function test_cannot_access_storefront_via_invalid_subdomain()
    {
        $response = $this->get('http://invalid.epi-oss.test');

        $response->assertStatus(404);
    }

    public function test_inactive_store_shows_maintenance_mode()
    {
        Distributor::factory()->create([
            'subdomain' => 'inactive',
            'status' => 'inactive',
        ]);

        $response = $this->get('http://inactive.epi-oss.test');

        $response->assertStatus(503);
    }
}
