<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key checks temporarily
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable('ict_maintenance_inspection')) {
            Schema::create('ict_maintenance_inspection', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('request_id');
                $table->enum('type', ['maintenance', 'inspection'])->default('maintenance');

                // Maintenance fields
                $table->date('date_current')->nullable();
                $table->time('time_current')->nullable();
                $table->string('req_name')->nullable();
                $table->string('req_designation')->nullable();
                $table->string('req_DO')->nullable();
                $table->string('DOPE')->nullable();
                $table->string('brand')->nullable();
                $table->string('prop_no')->nullable();
                $table->string('serial_no')->nullable();
                $table->date('date_last_repair')->nullable();
                $table->text('defects')->nullable();

                // Inspection fields
                $table->string('item')->nullable();
                $table->string('property_no')->nullable();
                $table->string('receipt_no')->nullable();
                $table->string('acquisition_cost')->nullable();
                $table->date('acquisition_date')->nullable();
                $table->text('complaints')->nullable();
                $table->text('scope_last_repair')->nullable();

                $table->timestamps();
            });
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('ict_maintenance_inspection');
    }
};
