<?php

namespace Tests\Feature\Announcement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Announcement;
use App\Enums\User\Roles;
use App\Enums\User\Active as UserActive;
use App\Enums\Announcement\Active as AnnouncementActive;
use App\Enums\User\Statuses;
use App\Enums\Announcement\Messages;


class UpdateDatesAnnouncementTest extends TestCase
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

    public function test_unauthorized_failed_to_update_announcement_dates()
    {
        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $updated = [
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays()->format('Y-m-d'),
        ];

        $this->putJson("/api/v1/announcements/{$announcement->id}", $updated)
            ->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_success_to_update_announcement_dates()
    {
        $this->actingAs($this->admin, 'sanctum');

        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $updated = [
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays()->format('Y-m-d'),
        ];

        $this->putJson("/api/v1/announcements/dates/{$announcement->id}", $updated)
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::UPDATED_DATES->value,
            ]);
    }

    public function test_success_to_update_announcement_with_empty_fields()
    {
        $this->actingAs($this->admin, 'sanctum');

        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $this->putJson("/api/v1/announcements/dates/{$announcement->id}", [])
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'data'])
            ->assertJson([
                'message' => Messages::UPDATED_DATES->value,
            ]);
    }

    public function test_failed_to_update_announcement_with_invalid_schedule()
    {
        $this->actingAs($this->admin, 'sanctum');

        $announcement = Announcement::factory()->create([
            'user_id' => $this->admin->id,
            'start_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'active' => AnnouncementActive::YES
        ]);

        // Only start_date provided
        $this->putJson("/api/v1/announcements/dates/{$announcement->id}", [
                'start_date' => Carbon::now()->format('Y-m-d'),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['start_date']);

        // Only invalid dates schedule
        $this->putJson("/api/v1/announcements/dates/{$announcement->id}", [
                'start_date' => Carbon::now()->format('Y-m-d'),
                'end_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['start_date', 'end_date']);

        $this->putJson("/api/v1/announcements/dates/{$announcement->id}", [
                'start_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d'),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['start_date', 'end_date']);
    }

}
