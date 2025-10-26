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

    /**
     * @test*/
    public function admin_can_create_an_ingredient(): void
    {   
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $response = $this->postJson('/api/ingredients', [
            'name' => 'Lime Juice',
        ]);

        $response->assertStatus(201)
                    ->assertJsonFragment([
                        'name' => 'Lime Juice',
                    ]);
    }

    /**
     * @test*/
    public function user_cannot_create_an_ingredient(): void
    {   
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $response = $this->postJson('/api/ingredients', [
            'name' => 'Mint',
        ]);

        $response->assertStatus(403);
    
    }

    /**
     * @test*/
    public function guest_cannot_create_an_ingredient(): void
    {   

        $response = $this->postJson('/api/ingredients', [
            'name' => 'Mint',
        ]);

        $response->assertStatus(401);
    
    }

}
