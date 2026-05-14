<?php

namespace Tests\Feature;

use App\Models\Documentation;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BasePathRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        putenv('BASE_PATH');
        unset($_ENV['BASE_PATH'], $_SERVER['BASE_PATH']);
        putenv('VITE_APP_BASE_PATH=/depaide');
        $_ENV['VITE_APP_BASE_PATH'] = '/depaide';
        $_SERVER['VITE_APP_BASE_PATH'] = '/depaide';

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        putenv('VITE_APP_BASE_PATH');
        unset($_ENV['VITE_APP_BASE_PATH'], $_SERVER['VITE_APP_BASE_PATH']);
    }

    public function test_login_screen_can_be_rendered_with_frontend_base_path_configured(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_root_redirects_to_login_with_frontend_base_path_configured(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_users_are_redirected_to_dashboard_with_frontend_base_path_configured(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_authenticated_api_and_view_routes_work_with_frontend_base_path_configured(): void
    {
        $user = User::factory()->create();
        $serviceRequest = ServiceRequest::create([
            'user_id' => $user->userId,
            'request_type_table' => 'documentation',
            'stat' => 'Pending',
        ]);

        Documentation::create([
            'request_id' => $serviceRequest->request_id,
            'title' => 'Annual Event',
            'event_location' => 'Conference Hall',
            'event_date' => now()->toDateString(),
            'start_time' => now()->format('H:i:s'),
            'end_time' => now()->addHour()->format('H:i:s'),
            'description' => 'Description here',
            'details' => 'Detailed briefing',
            'photo_link' => 'https://drive.google.com/example',
        ]);

        $this->actingAs($user)
            ->get('/api/calendar-events')
            ->assertOk();

        $this->actingAs($user)
            ->get('/status/view/documentation/'.$serviceRequest->request_id)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('ViewDocumentation')
                ->where('documentation.title', 'Annual Event')
            );
    }
}
