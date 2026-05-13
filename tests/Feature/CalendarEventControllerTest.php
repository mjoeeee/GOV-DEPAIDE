<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CalendarEventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_all_calendar_events()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        DB::table('users')->insert([
            [
                'id' => $admin->userId,
                'fullname' => $admin->fullname,
                'firstname' => $admin->firstname,
                'lastname' => $admin->lastname,
                'email' => $admin->email,
                'password' => $admin->password,
                'job_title' => $admin->job_title,
                'role' => $admin->role,
                'remember_token' => $admin->remember_token,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => $user->userId,
                'fullname' => $user->fullname,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'password' => $user->password,
                'job_title' => $user->job_title,
                'role' => $user->role,
                'remember_token' => $user->remember_token,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        ServiceRequest::create([
            'user_id' => $admin->userId,
            'request_type_table' => 'documentation',
            'stat' => 'Pending',
            'remarks' => 'Admin request',
        ]);

        ServiceRequest::create([
            'user_id' => $user->userId,
            'request_type_table' => 'software_development',
            'stat' => 'Completed',
            'remarks' => 'User request',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/api/calendar-events');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['request_type_table' => 'documentation']);
        $response->assertJsonFragment(['request_type_table' => 'software_development']);
    }

    public function test_non_admin_sees_only_their_calendar_events()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        DB::table('users')->insert([
            [
                'id' => $user->userId,
                'fullname' => $user->fullname,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'password' => $user->password,
                'job_title' => $user->job_title,
                'role' => $user->role,
                'remember_token' => $user->remember_token,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => $other->userId,
                'fullname' => $other->fullname,
                'firstname' => $other->firstname,
                'lastname' => $other->lastname,
                'email' => $other->email,
                'password' => $other->password,
                'job_title' => $other->job_title,
                'role' => $other->role,
                'remember_token' => $other->remember_token,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        ServiceRequest::create([
            'user_id' => $user->userId,
            'request_type_table' => 'id_card_printing',
            'stat' => 'Pending',
            'remarks' => 'User request',
        ]);

        ServiceRequest::create([
            'user_id' => $other->userId,
            'request_type_table' => 'password_reset',
            'stat' => 'Rejected',
            'remarks' => 'Other user request',
        ]);

        $this->actingAs($user);

        $response = $this->get('/api/calendar-events');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['request_type_table' => 'id_card_printing']);
        $response->assertJsonMissing(['request_type_table' => 'password_reset']);
    }
}
