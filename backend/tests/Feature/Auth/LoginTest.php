<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Enums\User\Roles;
use App\Enums\User\Active;
use App\Enums\User\Statuses;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\Auth\Messages as AuthMessages;
use App\Enums\User\Messages as UserMessages;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $credentials;
    protected $invalidCredentials;

    public function setUp(): void
    {
        parent::setUp();

        $this->credentials = [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'role' => Roles::ADMIN,
            'active' => Active::YES,
            'status' => Statuses::APPROVED
        ];
        $this->invalidCredentials = [
            'email' => 'wrongemail@gmail.com',
            'password' => 'wrongpassword',
        ];

        $this->admin = User::factory()->create($this->credentials + [
            'password' => Hash::make($this->credentials['password'])
        ]);
    }

    public function test_success_to_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->credentials['email'],
            'password' => $this->credentials['password'],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'token', 'data']);
    }

    public function test_failed_to_login_with_missing_credentials()
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_failed_to_login_with_incorrect_email()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->invalidCredentials['email'],
            'password' => $this->credentials['password']
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => AuthMessages::INVALID_CREDENTIALS->value
            ]);
    }

    public function test_success_to_login_with_incorrect_password()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->credentials['email'],
            'password' => $this->invalidCredentials['password']
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => AuthMessages::INVALID_CREDENTIALS->value
            ]);
    }

    public function test_failed_to_login_with_inactive_user()
    {
        User::factory()->create([
            'email' => 'inactiveuser@gmail.com',
            'password' => Hash::make('password'),
            'role' => Roles::ADMIN,
            'active' => Active::NO,
            'status' => Statuses::APPROVED
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'inactiveuser@gmail.com',
            'password' => 'password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'User account is inactive.',
                'message' => UserMessages::USER_INACTIVE->value
            ]);
    }

    public function test_failed_to_login_with_restricted_user()
    {
        User::factory()->create([
            'email' => 'restricteduser@gmail.com',
            'password' => Hash::make('password'),
            'role' => Roles::ADMIN,
            'active' => Active::YES,
            'status' => Statuses::PENDING
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'restricteduser@gmail.com',
            'password' => 'password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => UserMessages::USER_RESTRICTED->value
            ]);
    }
}
