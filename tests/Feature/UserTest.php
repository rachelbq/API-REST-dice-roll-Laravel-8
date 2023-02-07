<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_be_edit_nickname()
    {
        $this->artisan('passport:install');
        
        Passport::actingAs(
            $user = User::factory()
                ->create()
        );
        
        $this->put('api/players/' . $user->id, [
            'nickname' => 'editedNickname'
        ]);

        $this->assertDatabaseHas('users', [
            'nickname' => 'editedNickname'
        ]);    
    }

    public function test_admin_role_can_list_all_players_info()
    {
        $this->artisan('passport:install');

        $adminUser = User::factory()->create(['role' => "admin"]);
        Passport::actingAs($adminUser);

        if ($adminUser->role == "admin") {
            $response = $this->actingAs($adminUser, 'api')->getJson('api/players');
            $response->assertOk();
            $this->assertAuthenticated();
            $expectedResponse = [
                'users' => [[
                    'id' => $adminUser->id,
                    'name' => $adminUser->name,
                    'email' => $adminUser->email,
                    'successPercentage' => 0
                ]],

            'status' => 200
            ];

        $this->assertEquals($expectedResponse, $response->json());
        }
    }

    public function test_player_role_cannot_list_all_players_info()
    {
        $this->artisan('passport:install');

        $response = $this->getJson('api/players');
        $user = User::factory()->create(['role' => "player"]);

        if ($user->role !== "admin") {
            $response->assertStatus(401);
            $response->assertUnauthorized();
        }
    }

    public function test_unauth_user_cannot_list_all_players_info()
    {
        $response = $this->getJson('api/players');
        $response->assertStatus(401);
    }
}