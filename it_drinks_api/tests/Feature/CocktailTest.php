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
}
