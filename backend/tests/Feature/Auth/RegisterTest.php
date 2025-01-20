<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\Auth\Messages as AuthMessages;
use App\Enums\User\Messages as UserMessages;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected $credentials;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->credentials = [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
    }

    public function test_failed_to_register_with_valid_data()
    {
        $response = $this->postJson('/api/v1/auth/register', $this->credentials);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'token'
            ])
            ->assertJson([
                'message' => AuthMessages::TOKEN_GENERATED->value,
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $this->credentials['name'],
            'email' => $this->credentials['email'],
        ]);
    }

    public function test_failed_to_register_with_empty_fields()
    {
        $this->postJson('/api/v1/auth/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_failed_to_register_with_missing_fields()
    {
        $this->postJson('/api/v1/auth/register', [
            'email' => 'test@gmail.com',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password', 'name']);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'test',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        $this->postJson('/api/v1/auth/register', [
            'password' => 'password',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);

        $this->postJson('/api/v1/auth/register', [
            'password_confirmation' => 'password_confirmation',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_failed_to_register_with_invalid_inputs()
    {
        // Invalid email
        $this->postJson('/api/v1/auth/register', [
            'name' => 'test1',
            'email' => 'wrongemail',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // weak password
        $this->postJson('/api/v1/auth/register', [
            'name' => 'test2',
            'email' => 'tes2@gmail.com',
            'password' => 'test',
            'password_confirmation' => 'test'
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // mismatch password
        $this->postJson('/api/v1/auth/register', [
            'name' => 'test3',
            'email' => 'test3@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password3'
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_failed_to_register_with_existing_email()
    {
        $user1 = [
            'name' => 'existing email',
            'email' => 'existing@gmail.com',
            'password' => 'existing',
            'password_confirmation' => 'existing'
        ];
        $user2 = [
            'name' => 'existing email',
            'email' => 'existing@gmail.com',
            'password' => 'existing',
            'password_confirmation' => 'existing'
        ];

        $this->postJson('/api/v1/auth/register', $user1)
            ->assertStatus(201)
            ->assertJson([
                'message' => AuthMessages::TOKEN_GENERATED->value,
            ]);

        $this->postJson('/api/v1/auth/register', $user2)
            ->assertStatus(422)
            ->assertJson([
                'message' => UserMessages::EMAIL_TAKEN->value
            ]);
    }

    public function test_success_to_register_password_is_hashed()
    {
        $userData = [
            'name' => 'hash password',
            'email' => 'hashpassword@gmail.com',
            'password' => 'hashpassword',
            'password_confirmation' => 'hashpassword'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'token'
            ])
            ->assertJson([
                'message' => AuthMessages::TOKEN_GENERATED->value
            ]);

        $user = User::where('email', $userData['email'])->first();

        $this->assertNotEquals($user->password, $userData['password']);
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }
}
