<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ict_equipment_inspection', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('item');
            $table->string('property_no')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('acquisition_cost')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->text('complaints');
            $table->text('scope_last_repair')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ict_equipment_inspection');
    }
};
