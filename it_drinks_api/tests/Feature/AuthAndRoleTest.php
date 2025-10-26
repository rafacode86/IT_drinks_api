<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class AuthAndRoleTest extends TestCase
{   
    use RefreshDatabase;
   
    /**
     * @test*/
    public function it_register_new_user(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response ->assertStatus(201)
                    ->assertJsonStructure(['user', 'token']);
    }

    /**
     * @test*/
    public function it_logs_in_a_registered_user(): void
    {
        $user = User::factory()->create([
            'email' => 'login_user@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login_user@example.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'token']);
    }

    /**
     * @test*/
    public function it_accesses_a_protected_route_with_valid_token(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'user']);
    }

    /**
     * @test*/
    public function it_fails_to_access_protected_route_without_token(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /**
     * @test*/
    public function a_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $response = $this->getJson('/api/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * @test*/
    public function an_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $response = $this->getJson('/api/admin/dashboard');
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Bienvenido, Admin']);
    }
}
