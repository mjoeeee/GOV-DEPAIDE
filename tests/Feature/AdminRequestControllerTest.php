<?php

namespace Tests\Feature;

use App\Models\DepedEmailRequest;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_a_request()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        DB::table('users')->insert([
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
        ]);

        $request = ServiceRequest::create([
            'user_id' => $admin->userId,
            'request_type_table' => 'documentation',
            'stat' => 'Pending',
            'remarks' => 'Delete test',
        ]);

        $response = $this->actingAs($admin)
            ->delete('/admin/requests/'.$request->request_id);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Request deleted successfully.');
        $this->assertDatabaseMissing('requests', ['request_id' => $request->request_id]);
    }

    public function test_admin_request_page_shows_ict_maintenance_columns_when_filtered()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        DB::table('users')->insert([
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
        ]);

        $request = ServiceRequest::create([
            'user_id' => $admin->userId,
            'request_type_table' => 'ict_maintenance',
            'stat' => 'Pending',
            'remarks' => 'ICT filter test',
        ]);

        DB::table('ict_maintenance')->insert([
            'request_id' => $request->request_id,
            'date_current' => now()->toDateString(),
            'time_current' => now()->format('H:i:s'),
            'req_name' => 'John Tester',
            'req_designation' => 'Technician',
            'req_DO' => 'IT Department',
            'DOPE' => null,
            'brand' => 'Dell',
            'prop_no' => 'PROP-001',
            'serial_no' => 'SN-001',
            'last_repair_date' => null,
            'defects' => 'Monitor not working',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/requests?request_type_table=ict_maintenance');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Requests')
            ->where('currentType', 'ict_maintenance')
            ->where('requests.0.typeData.req_name', 'John Tester')
            ->where('requests.0.typeData.prop_no', 'PROP-001')
        );
    }

    public function test_admin_request_page_shows_documentation_columns_when_filtered()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        DB::table('users')->insert([
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
        ]);

        $request = ServiceRequest::create([
            'user_id' => $admin->userId,
            'request_type_table' => 'documentation',
            'stat' => 'Pending',
            'remarks' => 'Documentation test',
        ]);

        DB::table('documentation')->insert([
            'request_id' => $request->request_id,
            'title' => 'Annual Event',
            'description' => 'Event description',
            'details' => 'Detailed briefing',
            'photo_link' => 'https://drive.google.com/example',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/requests?request_type_table=documentation');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Requests')
            ->where('currentType', 'documentation')
            ->where('requests.0.typeData.title', 'Annual Event')
            ->where('requests.0.typeData.details', 'Detailed briefing')
            ->where('requests.0.typeData.photo_link', 'https://drive.google.com/example')
        );
    }

    public function test_admin_request_page_shows_documentation_columns_when_filtered_for_legacy_table()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        DB::table('users')->insert([
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
        ]);

        if (! Schema::hasColumn('requests', 'request_type_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->unsignedBigInteger('request_type_id')->nullable()->after('request_type_table');
            });
        }

        if (! Schema::hasTable('tbl_document_depaide')) {
            Schema::create('tbl_document_depaide', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->text('details')->nullable();
                $table->string('event_location')->nullable();
                $table->date('event_date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->timestamps();
            });
        }

        $detailId = DB::table('tbl_document_depaide')->insertGetId([
            'title' => 'Legacy Event',
            'description' => 'Legacy event description',
            'details' => 'Legacy detailed briefing',
            'event_location' => 'Legacy Hall',
            'event_date' => now()->toDateString(),
            'start_time' => now()->format('H:i:s'),
            'end_time' => now()->addHour()->format('H:i:s'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = ServiceRequest::create([
            'user_id' => $admin->userId,
            'request_type_table' => 'documentation',
            'request_type_id' => $detailId,
            'stat' => 'Pending',
            'remarks' => 'Legacy documentation test',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/requests?request_type_table=documentation');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Requests')
            ->where('currentType', 'documentation')
            ->where('requests.0.typeData.title', 'Legacy Event')
            ->where('requests.0.typeData.details', 'Legacy detailed briefing')
        );
    }

    public function test_admin_email_management_filter_marks_deped_email_request_as_deped_email()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        DB::table('users')->insert([
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
        ]);

        $request = ServiceRequest::create([
            'user_id' => $admin->userId,
            'request_type_table' => 'deped_email_request',
            'stat' => 'Pending',
        ]);

        DepedEmailRequest::create([
            'request_id' => $request->request_id,
            'user_id' => $admin->userId,
            'office_id' => '12345',
            'firstname' => $admin->firstname,
            'lastname' => $admin->lastname,
            'suffix' => $admin->extname,
            'position' => $admin->job_title,
            'email_format' => 'test.user@deped.gov.ph',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/requests?request_type_table=email_management');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Requests')
            ->where('currentType', 'email_management')
            ->where('requests.0.typeData.type', 'deped_email')
            ->where('requests.0.typeData.email_format', 'test.user@deped.gov.ph')
        );
    }
}
