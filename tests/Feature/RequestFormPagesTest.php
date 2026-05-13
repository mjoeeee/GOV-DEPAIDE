<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RequestFormPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_visit_new_request_form_pages()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/documentation')->assertOk();
        $this->get('/audio-visual')->assertOk();
        $this->get('/software-request')->assertOk();
        $this->get('/id-card-printing')->assertOk();
    }

    public function test_documentation_request_form_submits_and_creates_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/documentation', [
            'title' => 'School Program',
            'event_location' => 'Main Hall',
            'event_date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'description' => 'Year-end documentation',
            'details' => 'Need photographers and equipment',
            'photo_link' => 'https://drive.google.com/file/d/12345/view',
        ]);

        $response->assertRedirect(route('documentation.create'));
        $this->assertDatabaseHas('requests', [
            'user_id' => $user->userId,
            'request_type_table' => 'documentation',
            'stat' => 'Pending',
        ]);
        $this->assertDatabaseHas('documentation', [
            'title' => 'School Program',
            'event_location' => 'Main Hall',
        ]);
    }

    public function test_software_development_request_form_submits_with_attachment()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $attachment = UploadedFile::fake()->create('spec.docx', 100);

        $response = $this->post('/software-request', [
            'projName' => 'Online Portal',
            'briefDesc' => 'Build a new online portal for school services.',
            'primeObj' => 'Improve access',
            'features' => 'Login, dashboard, reports',
            'spec' => 'Web-based, mobile responsive',
            'projDeadline' => now()->addWeek()->toDateString(),
            'addInfo' => 'Integrate with existing systems',
            'attachment' => $attachment,
        ]);

        $response->assertRedirect(route('software-request.create'));
        $this->assertDatabaseHas('requests', [
            'user_id' => $user->userId,
            'request_type_table' => 'software_development',
            'stat' => 'Pending',
        ]);
        $this->assertDatabaseHas('software_development', [
            'proj_name' => 'Online Portal',
            'brief_desc' => 'Build a new online portal for school services.',
        ]);
    }

    public function test_id_card_printing_request_form_submits_and_creates_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/id-card-printing', [
            'email' => $user->email,
            'depId' => 'ICT Office',
            'role' => 'Teacher',
            'jobTitle' => 'Instructor',
            'hrId' => 'HR123',
            'bday' => '1990-01-01',
            'empId' => 'EMP001',
            'prcNo' => 'PRC100',
            'emrgncyName' => 'Jane Doe',
            'emrgncyNo' => '09171234567',
            'emrgncyEmail' => 'emergency@example.com',
            'fname' => $user->firstname,
            'lname' => $user->lastname,
            'mname' => 'A.',
            'extName' => 'Jr.',
        ]);

        $response->assertRedirect(route('id-card-printing.create'));
        $this->assertDatabaseHas('requests', [
            'user_id' => $user->userId,
            'request_type_table' => 'id_card_printing',
            'stat' => 'Pending',
        ]);
        $this->assertDatabaseHas('id_card_printing', [
            'email' => $user->email,
            'dep_id' => 'ICT Office',
            'role' => 'Teacher',
        ]);
    }
}
