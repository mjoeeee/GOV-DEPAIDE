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
            $table->text('description')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });

        Schema::create('audio_visual_editing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });

        Schema::create('id_card_printing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('details')->nullable();
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
