<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ingredient;
use App\Models\User;
use Laravel\Passport\Passport;

class IngredientTest extends TestCase
{   
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    
    /**
     * @test*/
    public function user_can_list_all_ingredients(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);
        
        Ingredient::factory()->count(3)->create();
        $response = $this->getJson('/api/ingredients');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'created_at', 'updated_at']
                 ]);
    }

    /**
     * @test*/
    public function admin_can_list_all_ingredients(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);
        
        Ingredient::factory()->count(3)->create();
        $response = $this->getJson('/api/ingredients');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'created_at', 'updated_at']
                 ]);
    }

    /**
     * @test*/
    public function guest_cannot_list_ingredients(): void
    {
        Ingredient::factory()->count(3)->create();

        $response = $this->getJson('/api/ingredients');

        $response->assertStatus(401);
    }
}
