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
            'classification' => 'juice'
        ]);

        $response->assertStatus(201)
                    ->assertJsonFragment([
                        'name' => 'Lime Juice',
                        'classification' => 'juice',
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

    /**
     * @test*/
    public function guest_cannot_view_ingredients(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Sugar Syrup',
            ]);

        $response = $this->getJson("/api/ingredients/{$ingredient->id}");

        $response->assertStatus(401);
    }

    /**
     * @test*/
    public function user_can_view_ingredients(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $ingredient = Ingredient::factory()->create([
            'name' => 'Cola',
            ]);

        $response = $this->getJson("/api/ingredients/{$ingredient->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Cola',]);
    }

    /**
     * @test*/
    public function admin_can_view_ingredients(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $ingredient = Ingredient::factory()->create([
            'name' => 'Peper',
            ]);

        $response = $this->getJson("/api/ingredients/{$ingredient->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Peper',]);
    }    

     /**
     * @test*/
    public function admin_can_update_ingredients(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $ingredient = Ingredient::factory()->create([
            'name' => 'Salt',
            ]);

        $response = $this->putJson("/api/ingredients/{$ingredient->id}", [
            'name' => 'Marine salt',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Marine salt',]);

    }

    /**
     * @test*/
    public function user_cannot_update_ingredients(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $ingredient = Ingredient::factory()->create([
            'name' => 'Sugar',
            ]);

        $response = $this->putJson("/api/ingredients/{$ingredient->id}", [
            'name' => 'Brown sugar',
        ]);

        $response->assertStatus(403);

    }

    /**
     * @test*/
    public function guest_cannot_update_ingredients(): void
    {

        $ingredient = Ingredient::factory()->create([
            'name' => 'Cinnamon',
            ]);

        $response = $this->putJson("/api/ingredients/{$ingredient->id}", [
            'name' => 'Cayena',
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test*/
    public function admin_can_delete_ingredients(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $ingredient = Ingredient::factory()->create([
            'name' => 'Salt',
            ]);

        $response = $this->deleteJson("/api/ingredients/{$ingredient->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);
    }

    /**
     * @test*/
    public function user_cannot_delete_ingredients(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $ingredient = Ingredient::factory()->create([
            'name' => 'Vodka',
            ]);

        $response = $this->deleteJson("/api/ingredients/{$ingredient->id}");

        $response->assertStatus(403);
    }

    /**
     * @test*/
    public function guest_cannot_delete_ingredients(): void
    {
        $ingredient = Ingredient::factory()->create([
            'name' => 'Gin',
            ]);

        $response = $this->deleteJson("/api/ingredients/{$ingredient->id}");

        $response->assertStatus(401);
    }
}
