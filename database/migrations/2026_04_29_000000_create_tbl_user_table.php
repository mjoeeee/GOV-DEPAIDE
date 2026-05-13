<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_user', function (Blueprint $table) {
            $table->bigIncrements('userId');
            $table->string('fullname');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('extname')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('job_title')->nullable();
            $table->string('role')->default('user');
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_user');
    }
};
