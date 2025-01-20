<?php

namespace Tests\Feature\Public;

use Tests\TestCase;
use App\Models\User;
use App\Models\Announcement;
use App\Enums\User\Roles;
use App\Enums\User\Active as UserActive;
use App\Enums\Announcement\Active as AnnouncementActive;
use App\Enums\User\Statuses;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\Announcement\Messages as AnnouncementMessages;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $credentials;

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

        Announcement::factory()->count(10)->create([
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES,
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ]);
    }

    public function test_success_get_public_announcements(): void
    {
        $this->getJson('/api/v1/public/announcements')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                ],
                'data'
            ])
            ->assertJson([
                'message' => AnnouncementMessages::FETCH->value
            ]);
    }

    public function test_success_get_active_announcements(): void
    {
        Announcement::query()->first()
            ->update(['active' => AnnouncementActive::NO]);

        $this->getJson('/api/v1/public/announcements')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                ],
                'data'
            ])
            ->assertJson([
                'pagination' => [
                    'total' => 9,
                ],
                'message' => AnnouncementMessages::FETCH->value
            ]);
    }

    public function test_announcements_query_order_by_start_date()
    {
        $last = Carbon::now()->subDays(3)->format('Y-m-d');
        $second = Carbon::now()->subDays(2)->format('Y-m-d');
        $first = Carbon::now()->subDays(1)->format('Y-m-d');

        Announcement::query()->delete();

        Announcement::factory()->create([
            'start_date' => $first,
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);
        Announcement::factory()->create([
            'start_date' => $second,
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);
        Announcement::factory()->create([
            'start_date' => $last,
            'user_id' => $this->admin->id,
            'active' => AnnouncementActive::YES
        ]);

        $response = $this->getJson('/api/v1/public/announcements')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'next_page_url',
                    'prev_page_url',
                ],
                'data'
            ])
            ->assertJson([
                'pagination' => [
                    'total' => 3,
                ],
                'message' => AnnouncementMessages::FETCH->value
            ]);

        $data = $response->json('data');

        $this->assertEquals($first, $data[0]['start_date']);
        $this->assertEquals($second, $data[1]['start_date']);
        $this->assertEquals($last, $data[2]['start_date']);
    }
}
