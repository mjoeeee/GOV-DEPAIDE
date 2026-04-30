<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_development', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('proj_name');
            $table->text('brief_desc')->nullable();
            $table->text('prime_obj')->nullable();
            $table->text('features')->nullable();
            $table->text('spec')->nullable();
            $table->string('attachment')->nullable();
            $table->datetime('proj_deadline')->nullable();
            $table->text('add_info')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_development');
    }
};
