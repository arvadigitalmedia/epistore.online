<?php

namespace Tests\Feature;

use App\Models\Distributor;
use App\Models\DistributorDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DomainManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles if necessary, or just skip permissions middleware in test if needed.
        // For now assuming basic auth works.
    }

    public function test_distributor_can_update_subdomain()
    {
        $user = User::factory()->create();
        $distributor = Distributor::factory()->create();
        // Link user to distributor as owner logic might differ, but based on logic:
        $user->distributor_id = $distributor->id;
        $user->save();

        $response = $this->actingAs($user)
            ->post(route('distributor.domains.update-subdomain'), [
                'subdomain' => 'my-new-store'
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('distributors', [
            'id' => $distributor->id,
            'subdomain' => 'my-new-store'
        ]);
    }

    public function test_distributor_cannot_use_reserved_subdomain()
    {
        $user = User::factory()->create();
        $distributor = Distributor::factory()->create();
        $user->distributor_id = $distributor->id;
        $user->save();

        $response = $this->actingAs($user)
            ->post(route('distributor.domains.update-subdomain'), [
                'subdomain' => 'admin'
            ]);

        $response->assertSessionHasErrors('subdomain');
    }

    public function test_distributor_can_add_custom_domain()
    {
        $user = User::factory()->create();
        $distributor = Distributor::factory()->create();
        $user->distributor_id = $distributor->id;
        $user->save();

        $response = $this->actingAs($user)
            ->post(route('distributor.domains.store'), [
                'domain' => 'mystore.com'
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('distributor_domains', [
            'distributor_id' => $distributor->id,
            'domain' => 'mystore.com',
            'status' => 'pending'
        ]);
    }
}
