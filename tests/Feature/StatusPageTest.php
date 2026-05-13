<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StatusPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_page_shows_email_management_subtype_labels()
    {
        $user = User::factory()->create();

        $firstRequest = ServiceRequest::forceCreate([
            'user_id' => $user->userId,
            'request_type_table' => 'deped_email_request',
            'stat' => 'Pending',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $secondRequest = ServiceRequest::forceCreate([
            'user_id' => $user->userId,
            'request_type_table' => 'password_reset',
            'stat' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/status');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Status')
            ->has('requests', 2)
            ->where('requests.0.mapped_type', 'Email Concern')
            ->where('requests.1.mapped_type', 'DepEd Email Request')
        );
    }
}
