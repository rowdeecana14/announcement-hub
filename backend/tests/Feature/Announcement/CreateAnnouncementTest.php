<?php

namespace Tests\Feature\Announcement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Enums\User\Roles;
use App\Enums\User\Active as UserActive;
use App\Enums\User\Statuses;
use App\Enums\Announcement\Messages;

class CreateAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $credentials;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->credentials = [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'role' => Roles::ADMIN,
            'active' => UserActive::YES,
            'status' => Statuses::APPROVED
        ];

        $this->admin = User::factory()->create($this->credentials + [
            'password' => Hash::make($this->credentials['password'])
        ]);
    }

    public function test_success_to_create_announcement()
    {
        $this->actingAs($this->admin, 'sanctum');

        $announcement = [
            'title' => 'Title 1',
            'content' => 'Content 1',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ];

        $this->postJson('/api/v1/announcements', $announcement)
            ->assertStatus(201)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::CREATED->value,
            ]);

        $this->assertDatabaseCount('announcements', 1)
            ->assertDatabaseHas('announcements', [
                'title' => $announcement['title'],
                'content' => $announcement['content'],
            ]);
    }

    public function test_unauthorized_failed_to_create_announcement()
    {
        $announcement = [
            'title' => 'Title 1',
            'content' => 'Content 1',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ];

        $this->postJson('/api/v1/announcements', $announcement)
            ->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);

        $this->assertDatabaseCount('announcements', 0)
            ->assertDatabaseMissing('announcements', [
                'title' => $announcement['title'],
                'content' => $announcement['content'],
            ]);
    }

    public function test_failed_to_create_announcement_with_empty_fields()
    {
        $this->actingAs($this->admin, 'sanctum');

        $this->postJson('/api/v1/announcements', [])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['title', 'content', 'start_date', 'end_date']);

        $this->assertDatabaseCount('announcements', 0);
    }

    public function test_failed_to_create_announcement_with_missing_fields()
    {
        $this->actingAs($this->admin, 'sanctum');

        // Only title provided
        $this->postJson('/api/v1/announcements', [
            'title' => 'Title 1',
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['content', 'start_date', 'end_date']);
        $this->assertDatabaseCount('announcements', 0);

        // Only content provided
        $this->postJson('/api/v1/announcements', [
            'content' => 'Content 1',
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['title', 'start_date', 'end_date']);
        $this->assertDatabaseCount('announcements', 0);

        // Only start date provided
        $this->postJson('/api/v1/announcements', [
            'start_date' => Carbon::now()->format('Y-m-d')
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['title', 'content', 'end_date']);
        $this->assertDatabaseCount('announcements', 0);

        // Only end date provided
        $this->postJson('/api/v1/announcements', [
            Carbon::now()->addDays(5)->format('Y-m-d'),
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['title', 'content', 'start_date']);
        $this->assertDatabaseCount('announcements', 0);
    }

    public function test_failed_to_create_announcement_with_invalid_inputs()
    {
        $this->actingAs($this->admin, 'sanctum');

        // Invalid start and end dates
        $this->postJson('/api/v1/announcements', [
            'start_date' => 'wrong date',
            'end_date' => 'wrong date',
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['title', 'content', 'start_date', 'end_date']);
        $this->assertDatabaseCount('announcements', 0);

        $this->postJson('/api/v1/announcements', [
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->subDays(5)->format('Y-m-d')
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['title', 'content', 'start_date', 'end_date']);
        $this->assertDatabaseCount('announcements', 0);
    }

    public function test_failed_to_create_with_existing_title()
    {
        $this->actingAs($this->admin, 'sanctum');

        $announcement = [
            'title' => 'Title 1',
            'content' => 'Content 1',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ];

        $this->postJson('/api/v1/announcements', $announcement)
            ->assertStatus(201)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::CREATED->value,
            ]);

        $this->postJson('/api/v1/announcements', $announcement)
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['title']);

        $this->assertDatabaseCount('announcements', 1)
            ->assertDatabaseHas('announcements', $announcement);
    }
}
