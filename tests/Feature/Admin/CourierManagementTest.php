<?php

namespace Tests\Feature\Admin;

use App\Models\Courier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourierManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Admin
        $this->admin = User::factory()->create(['email' => 'admin@epi.com']);
        $role = Role::create(['name' => 'super_admin']);
        $this->admin->assignRole($role);

        // Seed some couriers
        Courier::create(['code' => 'jne', 'name' => 'JNE', 'is_active' => true, 'priority' => 1]);
        Courier::create(['code' => 'pos', 'name' => 'POS', 'is_active' => false, 'priority' => 2]);
    }

    public function test_admin_can_view_courier_list()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.couriers.index'));

        $response->assertStatus(200);
        $response->assertSee('JNE');
        $response->assertSee('POS');
    }

    public function test_admin_can_toggle_courier_status()
    {
        $this->actingAs($this->admin);

        $courier = Courier::where('code', 'jne')->first();
        $this->assertTrue($courier->is_active);

        $response = $this->post(route('admin.couriers.toggle', $courier));
        $response->assertRedirect();

        $courier->refresh();
        $this->assertFalse($courier->is_active);
    }

    public function test_admin_can_update_priority()
    {
        $this->actingAs($this->admin);

        $courier = Courier::where('code', 'jne')->first();
        $this->assertEquals(1, $courier->priority);

        $response = $this->patch(route('admin.couriers.priority', $courier), [
            'priority' => 10
        ]);
        $response->assertRedirect();

        $courier->refresh();
        $this->assertEquals(10, $courier->priority);
    }

    public function test_non_admin_cannot_access_courier_management()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.couriers.index'));
        $response->assertStatus(403);
    }
}
