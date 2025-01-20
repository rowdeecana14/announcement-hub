<?php

namespace Tests\Feature\Announcement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Announcement;
use App\Enums\User\Roles;
use App\Enums\User\Active as UserActive;
use App\Enums\Announcement\Active as AnnouncementActive;
use App\Enums\User\Statuses;
use App\Enums\Announcement\Messages;

class DeleteAnnouncementTest extends TestCase
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

    public function test_unauthorized_failed_to_delete_announcement()
    {
        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->deleteJson("/api/v1/announcements/{$announcement->id}")
            ->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_success_to_delete_announcement()
    {
        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->actingAs($this->admin, 'sanctum');


        $this->deleteJson("/api/v1/announcements/{$announcement->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => Messages::DELETED->value,
            ]);

        $this->assertDatabaseCount('announcements', 1);
        $this->assertNotNull($announcement->fresh()->deleted_at);
    }

    public function test_failed_to_delete_announcement_with_invalid_id()
    {
        $this->actingAs($this->admin, 'sanctum');

        $radomId = 13689644;
        $stringId = '!@#$%^sdasdfasHJSAD';

        $this->deleteJson("/api/v1/announcements/{$radomId}")
            ->assertStatus(404)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => Messages::NOT_FOUND->value,
            ]);

        $this->deleteJson("/api/v1/announcements/{$stringId}")
            ->assertStatus(404)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => Messages::NOT_FOUND->value,
            ]);
    }
}
