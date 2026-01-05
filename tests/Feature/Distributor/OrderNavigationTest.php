<?php

namespace Tests\Feature\Distributor;

use App\Models\User;
use App\Models\Distributor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Spatie\Permission\Models\Role;

class OrderNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create role if not exists
        if (!Role::where('name', 'distributor_owner')->exists()) {
            Role::create(['name' => 'distributor_owner']);
        }
    }

    public function test_distributor_can_access_orders_page()
    {
        // Create distributor and user
        $distributor = Distributor::factory()->create();
        $user = User::factory()->create([
            'distributor_id' => $distributor->id,
        ]);
        $user->assignRole('distributor_owner');

        $response = $this->actingAs($user)->get(route('distributor.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('distributor.orders.index');
        
        // Check if sidebar link is present
        $response->assertSee(route('distributor.orders.index'));
        $response->assertSee('Orders');
    }

    public function test_non_distributor_cannot_access_orders_page()
    {
        $user = User::factory()->create([
            'distributor_id' => null,
        ]);

        $response = $this->actingAs($user)->get(route('distributor.orders.index'));

        $response->assertStatus(403);
    }

    public function test_sidebar_active_state_on_orders_page()
    {
        $distributor = Distributor::factory()->create();
        $user = User::factory()->create([
            'distributor_id' => $distributor->id,
        ]);

        $response = $this->actingAs($user)->get(route('distributor.orders.index'));

        // Check for active class on the link (bg-primary-50)
        // Note: HTML structure might vary, but we look for the class near the text
        $response->assertSee('bg-primary-50');
    }
}
