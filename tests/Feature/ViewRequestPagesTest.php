<?php

namespace Tests\Feature;

use App\Models\AudioVisualEditing;
use App\Models\DepedEmailRequest;
use App\Models\Documentation;
use App\Models\IdCardPrinting;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ViewRequestPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_documentation_request_details()
    {
        $user = User::factory()->create();
        $request = ServiceRequest::create([
            'user_id' => $user->userId,
            'request_type_table' => 'documentation',
            'stat' => 'Pending',
        ]);

        Documentation::create([
            'request_id' => $request->request_id,
            'title' => 'Annual Event',
            'event_location' => 'Conference Hall',
            'event_date' => now()->toDateString(),
            'start_time' => now()->format('H:i:s'),
            'end_time' => now()->addHour()->format('H:i:s'),
            'description' => 'Description here',
            'details' => 'Detailed briefing',
            'photo_link' => 'https://drive.google.com/example',
        ]);

        $this->assertDatabaseHas('requests', [
            'request_id' => $request->request_id,
            'user_id' => $user->userId,
        ]);

        $response = $this->actingAs($user)->get('/status/view/documentation/'.$request->request_id);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('ViewDocumentation')
            ->where('documentation.title', 'Annual Event')
            ->where('documentation.photo_link', 'https://drive.google.com/example')
        );
    }

    public function test_user_can_view_audio_visual_editing_request_details()
    {
        $user = User::factory()->create();
        $request = ServiceRequest::create([
            'user_id' => $user->userId,
            'request_type_table' => 'audio_visual_editing',
            'stat' => 'Pending',
        ]);

        AudioVisualEditing::create([
            'request_id' => $request->request_id,
            'title' => 'Promo Video',
            'project_type' => 'Video',
            'delivery_method' => 'Online',
            'project_deadline' => now()->toDateString(),
            'proj_desc' => 'A short promo video',
            'music_preference' => 'Uplifting',
            'deliverables' => 'MP4 file',
            'style_tone' => 'Professional',
        ]);

        $this->assertDatabaseHas('requests', [
            'request_id' => $request->request_id,
            'user_id' => $user->userId,
        ]);

        $response = $this->actingAs($user)->get('/status/view/audio-visual-editing/'.$request->request_id);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('ViewAudioVisualEditing')
            ->where('audioVisual.title', 'Promo Video')
            ->where('audioVisual.project_type', 'Video')
        );
    }

    public function test_user_can_view_deped_email_request_details()
    {
        $user = User::factory()->create();
        $request = ServiceRequest::create([
            'user_id' => $user->userId,
            'request_type_table' => 'deped_email_request',
            'stat' => 'Pending',
        ]);

        DepedEmailRequest::create([
            'request_id' => $request->request_id,
            'user_id' => $user->userId,
            'office_id' => '12345',
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'suffix' => $user->extname,
            'position' => $user->job_title,
            'email_format' => 'test.user@deped.gov.ph',
        ]);

        $this->assertDatabaseHas('requests', [
            'request_id' => $request->request_id,
            'user_id' => $user->userId,
        ]);

        $response = $this->actingAs($user)->get('/status/view/deped-email-request/'.$request->request_id);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('ViewEmailManagement')
            ->where('emailManagement.office_id', '12345')
            ->where('emailManagement.email_format', 'test.user@deped.gov.ph')
        );
    }

    public function test_user_can_view_id_card_printing_request_details()
    {
        $user = User::factory()->create();
        $request = ServiceRequest::create([
            'user_id' => $user->userId,
            'request_type_table' => 'id_card_printing',
            'stat' => 'Pending',
        ]);

        IdCardPrinting::create([
            'request_id' => $request->request_id,
            'email' => $user->email,
            'dep_id' => 'IT-01',
            'role' => 'Engineer',
            'job_title' => 'IT Engineer',
            'hr_id' => 'HR001',
            'bday' => now()->subYears(30)->format('Y-m-d'),
            'emp_id' => 'EMP001',
            'prc_no' => 'PRC123',
            'emrgncy_no' => '09171234567',
            'emrgncy_name' => 'Jane Doe',
            'emrgncy_email' => 'jane.doe@example.com',
            'prfx_name' => 'Mr.',
            'fname' => $user->firstname,
            'lname' => $user->lastname,
            'mname' => 'A.',
            'ext_name' => 'Jr.',
        ]);

        $this->assertDatabaseHas('requests', [
            'request_id' => $request->request_id,
            'user_id' => $user->userId,
        ]);

        $response = $this->actingAs($user)->get('/status/view/id-card-printing/'.$request->request_id);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('ViewIdCardPrinting')
            ->where('idCard.dep_id', 'IT-01')
            ->where('idCard.role', 'Engineer')
        );
    }
}
