<?php

namespace Tests\Feature;

use App\Models\Documentation;
use App\Models\ServiceRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ServiceRequestDetailMappingTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_request_event_columns_map_to_tbl_document_depaide(): void
    {
        Schema::create('tbl_request_depaide', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedBigInteger('userId')->nullable();
            $table->unsignedBigInteger('request_type_id')->nullable();
            $table->string('request_type_table')->nullable();
            $table->text('remarks')->nullable();
            $table->string('stat')->default('Pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->integer('rated')->default(0);
        });

        Schema::create('tbl_document_depaide', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('event_location');
            $table->date('event_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('details')->nullable();
        });

        $detailId = DB::table('tbl_document_depaide')->insertGetId([
            'title' => 'REQUEST FOR NEW KEYBOARD',
            'event_location' => 'FINANCE UNIT',
            'event_date' => '2026-01-19',
            'start_time' => '11:49:00',
            'end_time' => '12:49:00',
            'details' => 'CHANGE NEW KEYBOARD SINCE THE KEYS ARE HARD TO PRESS.',
        ]);

        $requestId = DB::table('tbl_request_depaide')->insertGetId([
            'userId' => 1,
            'request_type_id' => $detailId,
            'request_type_table' => 'documentation',
            'remarks' => 'Document request mapping test',
            'stat' => 'Pending',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($requestId);
        $documentation = Documentation::findOrFail($detailId);

        $this->assertSame('REQUEST FOR NEW KEYBOARD', $serviceRequest->event_title);
        $this->assertSame('FINANCE UNIT', $serviceRequest->location_event);
        $this->assertSame('2026-01-19 • 11:49:00 - 12:49:00', $serviceRequest->event_date_time);
        $this->assertSame('CHANGE NEW KEYBOARD SINCE THE KEYS ARE HARD TO PRESS.', $serviceRequest->event_details);
        $this->assertSame($requestId, $documentation->serviceRequest->request_id);
    }
}
