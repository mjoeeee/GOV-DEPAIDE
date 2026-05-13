<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('title')->nullable();
            $table->string('event_location')->nullable();
            $table->date('event_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('description')->nullable();
            $table->text('details')->nullable();
            $table->string('photo_link')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });

        Schema::create('audio_visual_editing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('title')->nullable();
            $table->text('proj_desc')->nullable();
            $table->string('project_type')->nullable();
            $table->string('music_preference')->nullable();
            $table->text('deliverables')->nullable();
            $table->string('style_tone')->nullable();
            $table->string('delivery_method')->nullable();
            $table->datetime('project_deadline')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });

        Schema::create('id_card_printing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('email')->nullable();
            $table->string('dep_id')->nullable();
            $table->string('role')->nullable();
            $table->string('job_title')->nullable();
            $table->string('hr_id')->nullable();
            $table->date('bday')->nullable();
            $table->string('emp_id')->nullable();
            $table->string('prc_no')->nullable();
            $table->string('emrgncy_no')->nullable();
            $table->string('emrgncy_name')->nullable();
            $table->string('emrgncy_email')->nullable();
            $table->string('prfx_name')->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('mname')->nullable();
            $table->string('ext_name')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('gsis_no')->nullable();
            $table->string('pagibig_no')->nullable();
            $table->string('philhealth_no')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('image')->nullable();
            $table->string('sign')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentation');
        Schema::dropIfExists('audio_visual_editing');
        Schema::dropIfExists('id_card_printing');
    }
};
