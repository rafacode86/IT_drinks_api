<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use App\Models\Cocktail;
use App\Models\Ingredient;
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

    /**
     * @test*/
    public function admin_can_delete_cocktails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $cocktail = Cocktail::factory()->create([
            'name' => 'Jack Rose',
            ]);

        $response = $this->deleteJson("/api/cocktails/{$cocktail->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('ingredients', ['id' => $cocktail->id]);
    }

    /**
     * @test*/
    public function user_cannot_delete_cocktails(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $cocktail = Cocktail::factory()->create([
            'name' => 'Jaguerbomb',
            ]);

        $response = $this->deleteJson("/api/cocktails/{$cocktail->id}");

        $response->assertStatus(403);
    }

    /**
     * @test*/
    public function guest_cannot_delete_cocktails(): void
    {
        $cocktail = Cocktail::factory()->create([
            'name' => 'Piña Colada',
            ]);

        $response = $this->deleteJson("/api/cocktails/{$cocktail->id}");

        $response->assertStatus(401);
    }

    /** 
     * @test */
    public function user_it_can_search_cocktails_by_ingredient(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $ingredient = Ingredient::factory()->create(['name' => 'Vodka']);
        $cocktail = Cocktail::factory()->create(['name' => 'Vodka Tonic']);
        $cocktail->ingredients()->attach($ingredient->id);

        $response = $this->getJson("/api/search/{$ingredient->id}");

        $response->assertStatus(200)
                ->assertJsonFragment(['name' => 'Vodka Tonic']);
    }

    /** 
     * @test */
    public function admin_it_can_search_cocktails_by_ingredient(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $ingredient = Ingredient::factory()->create(['name' => 'Rum']);
        $cocktail = Cocktail::factory()->create(['name' => 'rum and coke']);
        $cocktail->ingredients()->attach($ingredient->id);

        $response = $this->getJson("/api/search/{$ingredient->id}");

        $response->assertStatus(200)
                ->assertJsonFragment(['name' => 'rum and coke']);
    }

    /** 
     * @test */
public function it_calculates_the_alcohol_content_of_a_cocktail(): void
{
    $user = User::factory()->create(['role' => 'user']);
    Passport::actingAs($user);

    $vodka = Ingredient::factory()->create([
        'name' => 'Vodka',
        'classification' => 'alcoholic',
        'alcohol_content' => 40, // 40%
    ]);

    $juice = Ingredient::factory()->create([
        'name' => 'Orange Juice',
        'classification' => 'juice',
        'alcohol_content' => 0,
    ]);

    $cocktail = Cocktail::factory()->create(['name' => 'Screwdriver']);
    $cocktail->ingredients()->attach([
        $vodka->id => ['measure_ml' => 50],
        $juice->id => ['measure_ml' => 100],
    ]);

    $response = $this->getJson("/api/cocktails/{$cocktail->id}/alcohol");

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'cocktail' => 'Screwdriver',
                 'alcohol_content' => '13.33%',
             ]);
}

}
