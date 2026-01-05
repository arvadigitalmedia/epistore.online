<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Distributor;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DistributorDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Roles
        $role = Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'distributor_owner']);
        
        // Create Admin User
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super_admin');
    }

    public function test_admin_can_hard_delete_distributor_and_related_users()
    {
        // 1. Create Distributor & User
        $distributor = Distributor::create([
            'name' => 'Distributor to Delete',
            'code' => 'DEL001',
            'status' => 'active',
            'email' => 'owner@delete.com',
            'level' => 'epi_store'
        ]);

        $user = User::create([
            'name' => 'Owner Delete',
            'email' => 'owner@delete.com',
            'password' => bcrypt('password'),
            'distributor_id' => $distributor->id,
        ]);
        $user->assignRole('distributor_owner');

        $this->assertDatabaseHas('distributors', ['id' => $distributor->id]);
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        // 2. Perform Delete Action
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.distributors.destroy', $distributor));

        // 3. Assert Redirect
        $response->assertRedirect(route('admin.distributors.index'));
        $response->assertSessionHas('success');

        // 4. Assert Data Gone (Hard Delete)
        $this->assertDatabaseMissing('distributors', ['id' => $distributor->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_deleted_data_can_be_reused()
    {
        // 1. Create & Delete first time
        $distributor = Distributor::create([
            'name' => 'Reusable Name',
            'code' => 'REUSE01',
            'status' => 'active',
            'email' => 'reuse@test.com',
            'level' => 'epi_store'
        ]);

        $user = User::create([
            'name' => 'Reuse Owner',
            'email' => 'reuse@test.com',
            'password' => bcrypt('password'),
            'distributor_id' => $distributor->id,
        ]);

        // Hard Delete via Controller logic
        $this->actingAs($this->admin)
            ->delete(route('admin.distributors.destroy', $distributor));

        // 2. Try to Register Again with SAME data
        $response = $this->actingAs($this->admin)
            ->post(route('admin.distributors.store'), [
                'name' => 'Reusable Name',
                'code' => 'REUSE01', // Same Code
                'email' => 'reuse@test.com', // Same Email
                'phone' => '081234567890',
                'address' => 'New Address',
                'status' => 'active',
                'level' => 'epi_store',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        // 3. Assert Success
        $response->assertRedirect(route('admin.distributors.index'));
        $this->assertDatabaseHas('distributors', ['code' => 'REUSE01']);
        $this->assertDatabaseHas('users', ['email' => 'reuse@test.com']);
    }

    public function test_audit_log_is_created_on_hard_delete()
    {
        $distributor = Distributor::create([
            'name' => 'Audit Test',
            'code' => 'AUDIT01',
            'email' => 'audit@test.com',
            'level' => 'epi_store'
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.distributors.destroy', $distributor));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'HARD_DELETE_DISTRIBUTOR',
            'model_type' => Distributor::class,
            'model_id' => $distributor->id,
            'user_id' => $this->admin->id,
        ]);
        
        // Check if snapshot exists in old_values
        $log = AuditLog::where('model_id', $distributor->id)->first();
        $this->assertNotNull($log->old_values);
        $this->assertEquals('Audit Test', $log->old_values['name']);
    }
}
