<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->text('reason');
            $table->string('attachment')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_requests');
    }
};
