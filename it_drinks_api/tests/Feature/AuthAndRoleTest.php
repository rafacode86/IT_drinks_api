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
}
