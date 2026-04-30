<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ict_maintenance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->date('date_current');
            $table->time('time_current');
            $table->string('req_name');
            $table->string('req_designation');
            $table->string('req_DO');
            $table->string('DOPE')->nullable();
            $table->string('brand')->nullable();
            $table->string('prop_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->date('last_repair_date')->nullable();
            $table->text('defects')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ict_maintenance');
    }
};
