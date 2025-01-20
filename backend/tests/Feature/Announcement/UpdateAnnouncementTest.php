<?php

namespace Tests\Feature\Announcement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Announcement;
use App\Enums\User\Roles;
use App\Enums\User\Active as UserActive;
use App\Enums\Announcement\Active as AnnouncementActive;
use App\Enums\User\Statuses;
use App\Enums\Announcement\Messages;


class UpdateAnnouncementTest extends TestCase
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

    public function test_success_to_update_announcement_dates()
    {
        $updated = [
            'title' => 'This is title updated',
            'content' => 'This is content updated',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ];

        $this->actingAs($this->admin, 'sanctum');

        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->putJson("/api/v1/announcements/{$announcement->id}", $updated)
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::UPDATED->value,
            ]);

        $this->putJson("/api/v1/announcements/{$announcement->id}", $updated)
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::UPDATED->value,
            ]);

        $this->assertDatabaseCount('announcements', 1)
            ->assertDatabaseHas('announcements', $updated);
    }

    public function test_unauthorized_failed_to_update_announcement()
    {
        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $updated = [
            'title' => 'This is title updated',
            'content' => 'This is content updated',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays()->format('Y-m-d'),
        ];

        $this->putJson("/api/v1/announcements/{$announcement->id}", $updated)
            ->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);

        $this->assertDatabaseCount('announcements', 1)
            ->assertDatabaseMissing('announcements', $updated);
    }

    public function test_failed_to_update_announcement_with_invalid_id()
    {
        $this->actingAs($this->admin, 'sanctum');

        $radomId = 13689644;
        $stringId = '!@#$%^sdasdfasHJSAD';

        $updated = [
            'title' => 'This is title updated',
            'content' => 'This is content updated',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays()->format('Y-m-d'),
        ];

        Announcement::factory()->count(10)->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->putJson("/api/v1/announcements/{$radomId}", $updated)
            ->assertStatus(404)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => Messages::NOT_FOUND->value,
            ]);

        $this->putJson("/api/v1/announcements/{$stringId}", $updated)
            ->assertStatus(404)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => Messages::NOT_FOUND->value,
            ]);

        $this->assertDatabaseCount('announcements', 10)
            ->assertDatabaseMissing('announcements', $updated);
    }

    public function test_success_to_update_announcement_with_empty_fields()
    {
        $this->actingAs($this->admin, 'sanctum');

        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        Announcement::factory()->count(10)->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->putJson("/api/v1/announcements/{$announcement->id}", [])
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::UPDATED->value,
            ]);

        $this->assertDatabaseCount('announcements', 11)
            ->assertDatabaseHas('announcements', [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'start_date' => $announcement->start_date->format('Y-m-d'),
                'end_date' => $announcement->end_date->format('Y-m-d'),
            ]);
    }

    public function test_success_to_update_announcement_with_missing_fields()
    {
        $this->actingAs($this->admin, 'sanctum');

        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        Announcement::factory()->count(10)->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        // Only title provided
        $this->putJson("/api/v1/announcements/{$announcement->id}", [
            'title' => 'This is title updated',
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::UPDATED->value,
            ]);

        // Only content provided
        $this->putJson("/api/v1/announcements/{$announcement->id}", [
            'content' => 'This is content updated',
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::UPDATED->value,
            ]);

        // Only dates provided
        $this->putJson("/api/v1/announcements/{$announcement->id}", [
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays()->format('Y-m-d'),
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::UPDATED->value,
            ]);
    }

    public function test_failed_to_update_announcement_with_invalid_inputs()
    {
        $this->actingAs($this->admin, 'sanctum');

        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        Announcement::factory()->count(10)->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        // Invalid start and end dates
        $this->putJson("/api/v1/announcements/{$announcement->id}", [
            'start_date' => 'wrong date',
            'end_date' => 'wrong date',
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['start_date', 'end_date']);
        $this->assertDatabaseCount('announcements', 11);

        $this->putJson("/api/v1/announcements/{$announcement->id}", [
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->subDays(5)->format('Y-m-d')
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['start_date', 'end_date']);
        $this->assertDatabaseCount('announcements', 11);
    }

    public function test_failed_to_update_with_existing_announcement_title()
    {
        $this->actingAs($this->admin, 'sanctum');

        $firstAnnouncement = [
            'title' => 'First announcement title',
            'content' => 'First announcement content',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays()->format('Y-m-d'),
        ];
        $secondAnnouncement = [
            'title' => 'Second announcement title',
            'content' => 'Second announcement content',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays()->format('Y-m-d'),
        ];

        $announcement = Announcement::factory()->create($firstAnnouncement + [
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);
        Announcement::factory()->create($secondAnnouncement + [
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->putJson("/api/v1/announcements/{$announcement->id}", [
            'title' => $firstAnnouncement['title']
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::UPDATED->value,
            ]);

        $this->putJson("/api/v1/announcements/{$announcement->id}", [
            'title' => $secondAnnouncement['title']
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['title']);
    }
}
