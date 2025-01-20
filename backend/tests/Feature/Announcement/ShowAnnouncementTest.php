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


class ShowAnnouncementTest extends TestCase
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

    public function test_success_to_show_announcement_with_valid_id()
    {
        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->actingAs($this->admin, 'sanctum');

        $response = $this->getJson("/api/v1/announcements/{$announcement->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => Messages::FOUND->value,
            ]);
        $data = $response->json('data');

        $this->assertEquals($announcement->id, $data['id']);
        $this->assertEquals($announcement->user_id, $data['user_id']);
        $this->assertEquals($announcement->title, $data['title']);
        $this->assertEquals($announcement->content, $data['content']);
        $this->assertEquals($announcement->start_date->format('Y-m-d'), $data['start_date']);
        $this->assertEquals($announcement->end_date->format('Y-m-d'), $data['end_date']);
    }

    public function test_unauthorized_failed_to_show_announcement()
    {
        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->getJson("/api/v1/announcements/{$announcement->id}")
            ->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_failed_to_show_announcement_with_invalid_id()
    {
        $this->actingAs($this->admin, 'sanctum');

        Announcement::factory()->count(10)->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $radomId = 13689644;
        $stringId = '!@#$%^sdasdfasHJSAD';

        $this->getJson("/api/v1/announcements/{$radomId}")
            ->assertStatus(404)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => Messages::NOT_FOUND->value,
            ]);

        $this->getJson("/api/v1/announcements/{$stringId}")
            ->assertStatus(404)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => Messages::NOT_FOUND->value,
            ]);
    }
}
