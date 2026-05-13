<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('userId')->on('tbl_user')->onDelete('cascade');
            $table->string('request_type_table');
            $table->enum('stat', ['Pending', 'In Progress', 'Completed', 'Rejected'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->boolean('rated')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
