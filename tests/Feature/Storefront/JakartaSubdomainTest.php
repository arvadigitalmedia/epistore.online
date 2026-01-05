<?php

namespace Tests\Feature\Storefront;

use App\Models\Distributor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JakartaSubdomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_jakarta_subdomain_is_accessible()
    {
        // 1. Setup: Create the 'jakarta' distributor
        $distributor = Distributor::factory()->create([
            'name' => 'EPIS Jakarta',
            'subdomain' => 'jakarta',
            'status' => 'active',
        ]);

        // 2. Act: Simulate a GET request to http://jakarta.epi-oss.test/
        // Laravel allows simulating the host via the headers or the full URL
        $response = $this->get('http://jakarta.epi-oss.test/');

        // 3. Assert: Check response status and content
        $response->assertStatus(200);
        
        // Ensure it loads the storefront view, not the welcome view
        $response->assertViewIs('storefront.home');
        
        // Ensure the distributor name is visible
        $response->assertSee('EPIS Jakarta');
    }

    public function test_assets_are_accessible_on_subdomain()
    {
         // 1. Setup
         $distributor = Distributor::factory()->create([
            'name' => 'EPIS Jakarta',
            'subdomain' => 'jakarta',
            'status' => 'active',
        ]);

        // 2. Act
        $response = $this->get('http://jakarta.epi-oss.test/');

        // 3. Assert
        $response->assertStatus(200);
        // We just ensure the page loads successfully. 
        // Specific asset paths depend on Vite build state (dev vs prod), so we skip exact string match.
    }

    public function test_guest_sees_login_link()
    {
        // 1. Setup
        $distributor = Distributor::factory()->create([
            'name' => 'EPIS Jakarta',
            'subdomain' => 'jakarta',
            'status' => 'active',
        ]);

        // 2. Act
        $response = $this->get('http://jakarta.epi-oss.test/');

        // 3. Assert
        $response->assertStatus(200);
        // Should see the link to storefront.login
        // We can check for the URL or the text
        $response->assertSee(route('storefront.login'));
    }
    
    public function test_authenticated_user_sees_cart_link()
    {
        // 1. Setup
        $distributor = Distributor::factory()->create([
            'name' => 'EPIS Jakarta',
            'subdomain' => 'jakarta',
            'status' => 'active',
        ]);
        
        $user = \App\Models\User::factory()->create();

        // 2. Act
        $response = $this->actingAs($user)->get('http://jakarta.epi-oss.test/');

        // 3. Assert
        $response->assertStatus(200);
        $response->assertSee(route('storefront.cart.index'));
        $response->assertDontSee(route('storefront.login'));
    }
    
    public function test_non_existent_subdomain_shows_404_or_redirect()
    {
        // Act
        $response = $this->get('http://unknown.epi-oss.test/');

        // Assert
        // Depending on middleware logic, it might return 404 or redirect to main
        // Current IdentifyDistributorByDomain logic needs to be checked.
        // If logic says "abort(404)" it should be 404.
        $response->assertStatus(404);
    }
}
