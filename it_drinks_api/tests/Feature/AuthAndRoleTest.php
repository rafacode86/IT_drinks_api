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
}
