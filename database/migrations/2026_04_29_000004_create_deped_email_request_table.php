<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deped_email_request', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('userId')->on('tbl_user')->onDelete('cascade');
            $table->string('office_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('suffix')->nullable();
            $table->string('position');
            $table->string('email_format');
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deped_email_request');
    }
};
