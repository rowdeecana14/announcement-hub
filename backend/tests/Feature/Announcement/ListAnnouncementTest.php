<?php

namespace Tests\Feature\Announcement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Announcement;
use App\Enums\User\Roles;
use App\Enums\User\Active as UserActive;
use App\Enums\User\Statuses;
use App\Enums\Announcement\Active as AnnouncementActive;
use App\Enums\Announcement\Messages;

class ListAnnouncementTest extends TestCase
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

    public function test_unauthorized_failed_to_fetch_announcements()
    {
        Announcement::factory()->count(10)->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $response = $this->getJson('/api/v1/announcements');

        $response->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_success_to_fetch_announcements()
    {
        $this->actingAs($this->admin, 'sanctum');
        $response = $this->getJson('/api/v1/announcements');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'start_date',
                        'end_date',
                        'active',
                    ]
                ],
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ]
            ])
            ->assertJson(['message' => Messages::FETCH->value])
            ->assertJsonPath('pagination.total', 0);
    }

    public function test_success_to_fetch_with_no_announcements_available()
    {
        Announcement::truncate();

        $this->actingAs($this->admin, 'sanctum');

        $this->getJson('/api/v1/announcements')
            ->assertStatus(200)
            ->assertJson(['message' => Messages::FETCH->value])
            ->assertJsonPath('data', [])
            ->assertJsonPath('pagination.total', 0);
    }

    public function test_success_to_fetch_with_paginated_announcements()
    {
        $this->actingAs($this->admin, 'sanctum');

        Announcement::truncate();
        Announcement::factory()->count(20)->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->getJson('/api/v1/announcements?page=1&per_page=10')
            ->assertStatus(200)
            ->assertJsonPath('pagination.total', 20)
            ->assertJsonPath('pagination.per_page', 10)
            ->assertJsonPath('pagination.current_page', 1)
            ->assertJsonPath('pagination.last_page', 2);
    }
}
