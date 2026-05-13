<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('documentation') && ! Schema::hasColumn('documentation', 'photo_link')) {
            Schema::table('documentation', function (Blueprint $table) {
                $table->string('photo_link')->nullable()->after('details');
            });
        }

        if (Schema::hasTable('tbl_document_depaide') && ! Schema::hasColumn('tbl_document_depaide', 'photo_link')) {
            Schema::table('tbl_document_depaide', function (Blueprint $table) {
                $table->string('photo_link')->nullable()->after('details');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('documentation') && Schema::hasColumn('documentation', 'photo_link')) {
            Schema::table('documentation', function (Blueprint $table) {
                $table->dropColumn('photo_link');
            });
        }

        if (Schema::hasTable('tbl_document_depaide') && Schema::hasColumn('tbl_document_depaide', 'photo_link')) {
            Schema::table('tbl_document_depaide', function (Blueprint $table) {
                $table->dropColumn('photo_link');
            });
        }
    }
};
