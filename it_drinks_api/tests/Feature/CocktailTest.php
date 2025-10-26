<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use App\Models\Cocktail;
use App\Models\User;
use Laravel\Passport\Passport;

class CocktailTest extends TestCase
{   
    use RefreshDatabase;

    /**
     * @test*/
    public function user_can_list_all_cocktails(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);
        
        Cocktail::factory()->count(3)->create();
        $response = $this->getJson('/api/cocktails');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'created_at', 'updated_at']
                 ]);
    }

    /**
     * @test*/
    public function admin_can_list_all_cocktails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);
        
        Cocktail::factory()->count(3)->create();
        $response = $this->getJson('/api/cocktails');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'created_at', 'updated_at']
                 ]);
    }

    /**
     * @test*/
    public function guest_cannot_list_all_cocktails(): void
    {
        Cocktail::factory()->count(3)->create();

        $response = $this->getJson('/api/cocktails');

        $response->assertStatus(401);
    }

    /**
     * @test*/
    public function admin_can_create_a_cocktail(): void
    {   
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $response = $this->postJson('/api/cocktails', [
            'name' => 'Negroni',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Negroni']);
    }

     /** 
      * @test */
    public function user_cannot_create_cocktail(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $response = $this->postJson('/api/cocktails', [
            'name' => 'Gintonic',
        ]);

        $response->assertStatus(403);
    }

     /** @test */
    public function guest_cannot_create_cocktail(): void
    {
        $response = $this->postJson('/api/cocktails', [
            'name' => 'Gintonic',
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test*/
    public function guest_cannot_view_cocktail(): void
    {
        $cocktail = Cocktail::factory()->create([
            'name' => 'Blody Mary',
            ]);

        $response = $this->getJson("/api/cocktails/{$cocktail->id}");

        $response->assertStatus(401);
    }

    /**
     * @test*/
    public function user_can_view_cocktail(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $cocktail = Cocktail::factory()->create([
            'name' => 'Sex on the Beach',
            ]);

        $response = $this->getJson("/api/cocktails/{$cocktail->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Sex on the Beach',]);
    }

    /**
     * @test*/
    public function admin_can_view_cocktail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $cocktail = Cocktail::factory()->create([
            'name' => 'Cuba libre',
            ]);

        $response = $this->getJson("/api/cocktails/{$cocktail->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Cuba libre',]);
    }

    /**
     * @test*/
    public function admin_can_update_cocktails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $cocktail = Cocktail::factory()->create([
            'name' => 'Sandia',
            ]);

        $response = $this->putJson("/api/cocktails/{$cocktail->id}", [
            'name' => 'Sangria',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Sangria',]);

    }

    /**
     * @test*/
    public function user_cannot_update_cocktails(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $cocktail = Cocktail::factory()->create([
            'name' => 'San Francisco',
            ]);

        $response = $this->putJson("/api/cocktails/{$cocktail->id}", [
            'name' => 'San Francisco Deluxe',
        ]);

        $response->assertStatus(403);
    }

    /**
     * @test*/
    public function guest_cannot_update_cocktails(): void
    {

        $cocktail = Cocktail::factory()->create([
            'name' => 'Tornillo',
            ]);

        $response = $this->putJson("/api/cocktails/{$cocktail->id}", [
            'name' => 'Destornillador',
        ]);

        $response->assertStatus(401);
    }
}
