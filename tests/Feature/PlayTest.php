<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class PlayTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_player_can_roll_dice()
    {
        $this->artisan('passport:install');
        
        Passport::actingAs(
            $user = User::factory()
            ->create()
        );
        
        $this->post('api/players/'. $user->id . '/games');

        $this->assertDatabaseHas('plays', [
            'user_id' => $user->id,
        ]);
    }

    public function test_unauth_player_cannot_roll_dice()
    {
        $this->artisan('passport:install');
        
        $user = User::factory()
            ->create();
        
        $this->post('api/players/'. $user->id . '/games')
            ->assertRedirect(route('login'));
    }

    public function test_auth_player_can_get_own_plays()
    {
        $this->artisan('passport:install');

        $user = User::factory()->create();
        $user = Passport::actingAs($user);
        $response = $this->actingAs($user, 'api')->get(route('getOwnPlays', $user->id));
        $response->assertStatus(200);
    }

    public function test_unauth_player_cannot_get_own_plays()
    {
        $response = $this->getJson('api/players/{id}/games');
        $response->assertStatus(401);
    }
    
    public function test_auth_player_can_remove_own_plays()
    {
        $this->artisan('passport:install');

        $user = User::factory()->create();
        $user = Passport::actingAs($user);
        $response = $this->actingAs($user, 'api')->delete(route('removeOwnPlays', $user->id));
        $response->assertStatus(200);
    }

    public function test_unauth_player_cannot_remove_own_plays()
    {
        $response = $this->deleteJson('api/players/{id}/games');
        $response->assertStatus(401);
    }

    public function test_admin_role_can_get_ranking_average()
    {
        $this->artisan('passport:install');
    
        $admin = User::factory()->create(['role' => "admin"]);
        $admin = Passport::actingAs($admin);
    
        $response = $this->actingAs($admin, 'api')->getJson('api/players/ranking');
        $response->assertOk();
    
        $data = $response->decodeResponseJson();
        $this->assertArrayHasKey('message', $data);
    
        $this->assertAuthenticated();
    }
 
    public function test_player_role_cannot_get_ranking_average()
    {
        $this->artisan('passport:install');

        $response = $this->getJson('api/players/ranking');
        $user = User::factory()->create(['role' => "player"]);

        if ($user->role !== "admin") {
            $response->assertUnauthorized();
        }
    }

    public function test_unauth_user_cannot_get_ranking_average()
    {
        $response = $this->getJson('api/players/ranking');
        $response->assertStatus(401);
    }

    public function test_admin_role_can_get_loser_player()
    {
        $this->withoutExceptionHandling();

        $this->artisan('passport:install');

        $admin = User::factory()->create(['role' => "admin"]);
        $admin = Passport::actingAs($admin);

        if ($admin['role'] == "admin") {
            $response = $this->actingAs($admin, 'api')->getJson('api/players/ranking/loser');
            $response->assertOk();
            $this->assertAuthenticated();
        }
    }

    public function test_player_role_cannot_get_loser_player()
    {

        $this->artisan('passport:install');

        $response = $this->getJson('api/players/ranking/loser');
        $user = User::factory()->create(['role' => "player"]);

        if ($user->role !== "admin") {
            $response->assertUnauthorized();
        }
    }

    public function test_unauth_user_cannot_get_loser_player()
    {
        $response = $this->getJson('api/players/ranking/loser');
        $response->assertStatus(401);
    }

    public function test_admin_role_can_get_winner_player()
    {
        $this->withoutExceptionHandling();

        $this->artisan('passport:install');

        $admin = User::factory()->create(['role' => "admin" ]);
        $admin = Passport::actingAs($admin);

        if ($admin['role'] == "admin") {
            $response = $this->actingAs($admin, 'api')->getJson('api/players/ranking/winner');
            $response->assertOk();
            $this->assertAuthenticated();
        }
    }

    public function test_player_role_cannot_get_winner_player()
    {
        $this->artisan('passport:install');

        $response = $this->getJson('api/players/ranking/winner');
        $user = User::factory()->create(['role' => "player"]);

        if ($user->role !== "admin") {
            $response->assertUnauthorized();
        }
    }

    public function test_unauth_user_cannot_get_winner_player()
    {
        $response = $this->getJson('api/players/ranking/winner');
        $response->assertStatus(401);
    }
}
