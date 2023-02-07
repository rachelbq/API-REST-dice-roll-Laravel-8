<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    // register testing
    public function test_user_can_register()
    {
        $this->artisan('passport:install');
        $this->withoutExceptionHandling();

        $response = $this->postJson('api/players', [
            'nickname' => 'UserNickname',
            'email' => 'user@email.com',
            'password' => 'UserPassword',
            'password_confirmation' => 'UserPassword',
            'role' => 'player'
        ]);

        $response->assertCreated();
        $user = User::first();
        $this->assertCount(1, User::all());
        $this->assertEquals('UserNickname', $user->nickname);
        $this->assertEquals('user@email.com', $user->email);
        $this->assertDatabaseHas('users', $user->toArray());
    }

    public function test_user_can_register_empty_nickname_and_nicknamed_anonymous()
    {
        $this->artisan('passport:install');

        $response = $this->postJson('api/players', [
            'nickname' => '',
            'email' => 'user@email.com',
            'password' => 'UserPassword',
            'password_confirmation' => 'UserPassword',
            'role' => 'player'
        ]);

        $response->assertCreated();
    }
    
    public function test_required_email()
    {
        $this->artisan('passport:install');

        $response = $this->post('api/players', [
            'nickname' => 'UserNickname',
            'email' => '',
            'password' => 'UserPassword',
            'password_confirmation' => 'UserPassword',
            'role' => 'player'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_required_password()
    {
        $this->artisan('passport:install');

        $response = $this->post('api/players', [
            'nickname' => 'UserNickname',
            'email' => 'user@email.com',
            'password' => '',
            'password_confirmation' => 'UserPassword',
            'role' => 'player'
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_required_password_confirmation()
    {
        $this->artisan('passport:install');

        $this->post('api/players', [
            'nickname' => 'UserNickname',
            'email' => 'user@email.com',
            'password' => 'UserPassword',
            'password_confirmation' => '',
            'role' => 'player'
        ])

        ->assertInvalid(['password']);
    }

    public function test_unique_nickname()
    {
        $this->artisan('passport:install');

        $response = $this->post('api/players', [
            'nickname' => 'UserNickname',
            'email' => 'user@email.com',
            'password' => 'UserPassword',
            'password_confirmation' => 'UserPassword',
            'role' => 'player'
        ]);

        $response = $this->post('api/players', [
            'nickname' => 'UserNickname',
            'email' => 'otheruser@email.com',
            'password' => 'UserPassword',
            'password_confirmation' => 'UserPassword',
            'role' => 'player'
        ]);

        $response->assertSessionHasErrors(['nickname']);
    }
    
    public function test_unique_email()
    {
        $this->artisan('passport:install');

        $response = $this->post('api/players', [
            'nickname' => 'UserNickname',
            'email' => 'user@email.com',
            'password' => 'UserPassword',
            'password_confirmation' => 'UserPassword',
            'role' => 'player'
        ]);

        $response = $this->post('api/players', [
            'nickname' => 'OtherUserNickname',
            'email' => 'user@email.com',
            'password' => 'UserPassword',
            'password_confirmation' => 'UserPassword',
            'role' => 'player'
        ]);

        $response->assertSessionHasErrors(['email']);
    }
    
    // login testing
    public function test_user_can_login()
    {
        $this->artisan('passport:install');

        $user = User::factory()->create([
            'password' => bcrypt($password = 'UserPassword'),
        ]);

        $response = $this->post('api/players/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    public function test_required_email_at_login()
    {
        $this->artisan('passport:install');

        $response = $this->post('api/players/login', [
            'email' => '',
            'password' => 'UserPassword'
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertStatus(302);
    }

    public function test_required_password_at_login()
    {
        $this->artisan('passport:install');

        $response = $this->post('api/players/login', [
            'email' => 'test@email.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertStatus(302);
    }

    public function test_errors_validation_login_email()
    {
        $response = $this->post('api/players/login', []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    public function test_errors_validation_login_password()
    {
        $response = $this->post('api/players/login', []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
    }

    // logout testing
    public function test_auth_user_can_logout()
    {
        $this->artisan('passport:install');

        $user = User::factory()->create();
        Passport::actingAs($user);
        $response = $this->postJson('api/players/logout');
        $response->assertStatus(200);
    }
}